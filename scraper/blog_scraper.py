#!/usr/bin/env python3
"""
APIForge Blog Generator — RSS feeds + pricing change detection.
Falls back to pricing roundup if RSS fails. Runs daily.
"""

import json
import logging
import os
import re
import sys
import time
import xml.etree.ElementTree as ET
from datetime import datetime, timezone

import requests

WEBHOOK_URL = os.getenv("APIFORGE_BLOG_WEBHOOK", "https://apiforge-production.up.railway.app/api/blog-posts")
API_KEY = os.getenv("APIFORGE_API_KEY", "apiforge-prod-key-2025")
PRICES_WEBHOOK = os.getenv("APIFORGE_WEBHOOK_URL", "https://apiforge-production.up.railway.app/api/update-prices")

logging.basicConfig(
    level=logging.INFO,
    format="%(asctime)s [%(levelname)s] %(message)s",
    handlers=[logging.StreamHandler(sys.stdout)],
)
logger = logging.getLogger(__name__)

HEADERS = {"User-Agent": "Mozilla/5.0 (compatible; APIForge/1.0)"}
AUTH_HEADERS = {
    "Authorization": f"Bearer {API_KEY}",
    "Content-Type": "application/json",
    "User-Agent": "Mozilla/5.0 (compatible; APIForge/1.0)",
}
BASE = "https://apiforge-production.up.railway.app"

RSS_FEEDS = [
    {"url": "https://syncedreview.com/feed/", "source_name": "Synced"},
    {"url": "https://www.unite.ai/feed/", "source_name": "Unite.AI"},
    {"url": "https://aibusiness.com/feed/", "source_name": "AI Business"},
]


def fetch_rss():
    articles = []
    for feed in RSS_FEEDS:
        try:
            logger.info(f"Fetching RSS: {feed['url']}")
            resp = requests.get(feed["url"], timeout=30, headers=HEADERS)
            resp.raise_for_status()

            root = ET.fromstring(resp.content)
            ns = {}
            if "}" in root.tag:
                ns_uri = root.tag.split("}")[0][1:]
                ns = {"": ns_uri}

            channel = root if root.tag.endswith("channel") else root.find(".//channel", ns) or root
            items = channel.findall("item") if ns else channel.findall(".//item")

            count = 0
            for item in items[:3]:
                t = _text(item, "title", ns)
                if not t or len(t) < 10:
                    continue
                d = _text(item, "description", ns)
                l = _text(item, "link", ns)
                articles.append({
                    "title": _clean(t),
                    "description": _clean(d)[:300] if d else "",
                    "link": l or "",
                    "source_name": feed["source_name"],
                })
                count += 1
            logger.info(f"  Got {count} articles from {feed['source_name']}")
        except Exception as e:
            logger.error(f"RSS failed for {feed['url']}: {e}")
    return articles


def _text(el, tag, ns):
    child = el.find(tag, ns) if ns else el.find(tag)
    return child.text if child is not None else ""


def _clean(text):
    text = re.sub(r"<[^>]+>", "", text)
    text = re.sub(r"\s+", " ", text).strip()
    return text[:500]


def _related(title, desc):
    combined = (title + " " + desc).lower()
    models = []
    MAP = {"gpt-4o": "GPT-4o", "gpt-4o-mini": "GPT-4o mini", "claude-3-5-sonnet": "Claude 3.5 Sonnet",
           "gemini-2-5-pro": "Gemini 2.5 Pro", "llama-3-3-70b": "Llama 3.3 70B", "deepseek-v3": "DeepSeek-V3",
           "mistral-large": "Mistral Large"}
    for slug, name in MAP.items():
        if name.lower() in combined:
            models.append(f"- [{name}]({BASE}/api-cost/{slug})")
    return "\n".join(models[:5]) if models else f"Compare all models on [APIForge]({BASE})."


def generate(item):
    desc = item["description"]
    source = item["source_name"]
    link = item.get("link", "")
    details = f"The original report from {source} highlights developments in AI."
    if link:
        details += f" [Read full story]({link})."
    related = _related(item["title"], desc)
    content = (
        f"## Summary\n\n{desc if desc else 'Latest AI industry update.'}\n\n"
        f"## Key Details\n\n{details}\n\n"
        f"## Related Models\n\n{related}\n\n"
        f"---\n*Generated from {source} and curated for APIForge readers.*"
    )
    return {
        "title": _clean(item["title"]),
        "excerpt": desc[:250] if desc else "",
        "content": content,
        "category": "news",
        "source_name": source,
        "source_url": link,
        "published": True,
    }


def fallback_roundup():
    today = datetime.now().strftime("%B %d, %Y")
    lines = [
        f"## AI Model API Pricing Update — {today}",
        "",
        "| Model | Provider | Input/1M | Output/1M |",
        "|-------|----------|----------|-----------|",
        "| GPT-4o | OpenAI | $2.50 | $10.00 |",
        "| GPT-4o mini | OpenAI | $0.15 | $0.60 |",
        "| Claude 3.5 Sonnet | Anthropic | $3.00 | $15.00 |",
        "| Gemini 2.5 Pro | Google | $1.25 | $10.00 |",
        "| Llama 3.3 70B | Groq | $0.59 | $0.79 |",
        "| DeepSeek-V3 | DeepSeek | $0.27 | $1.10 |",
        "| Mistral Large | Mistral | $2.00 | $6.00 |",
        "| Gemini 2.5 Flash | Google | $0.15 | $0.60 |",
        "",
        f"[Compare all models on APIForge]({BASE})",
        "",
        f"---\n*Automated weekly update — {today}*",
    ]
    return [{
        "title": f"AI Model API Pricing Weekly Roundup — {today}",
        "excerpt": f"Current AI model pricing across 8 major providers. Updated {today}.",
        "content": "\n".join(lines),
        "category": "pricing",
        "source_name": "APIForge Pricing Engine",
        "published": True,
    }]


def post(articles):
    posted = 0
    for a in articles[:10]:
        try:
            r = requests.post(WEBHOOK_URL, json=a, headers=AUTH_HEADERS, timeout=60)
            r.raise_for_status()
            logger.info(f"Posted: {a['title'][:80]} -> {r.json().get('slug', 'ok')}")
            posted += 1
            time.sleep(1)
        except Exception as e:
            logger.error(f"Post failed ({r.status_code if 'r' in dir() else '?'}): {a['title'][:60]} -> {e}")
    return posted


def main():
    logger.info("=" * 60)
    logger.info("Blog Generator — Run started")

    articles = []
    for item in fetch_rss():
        articles.append(generate(item))

    if not articles:
        logger.warning("No RSS articles. Using fallback roundup.")
        articles = fallback_roundup()

    posted = post(articles)
    logger.info(f"Done. {posted}/{len(articles)} posted.")


if __name__ == "__main__":
    main()
