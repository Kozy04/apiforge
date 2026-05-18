#!/usr/bin/env python3
"""
APIForge Blog Generator — Pulls AI news from RSS feeds, detects pricing changes,
generates structured Markdown articles, and POSTs them to the Laravel blog webhook.
Run daily via cron: 0 6 * * * cd /path/to/scraper && venv/bin/python blog_scraper.py
"""

import json
import logging
import os
import sys
import time
import xml.etree.ElementTree as ET
from datetime import datetime, timezone

import requests

# ─── Configuration ───────────────────────────────────────────────────────────

WEBHOOK_URL = os.getenv("APIFORGE_BLOG_WEBHOOK", "https://apiforge-production.up.railway.app/api/blog-posts")
API_KEY = os.getenv("APIFORGE_API_KEY", "apiforge-prod-key-2025")
PRICES_WEBHOOK = os.getenv("APIFORGE_WEBHOOK_URL", "https://apiforge-production.up.railway.app/api/update-prices")
LOG_FILE = os.path.join(os.path.dirname(__file__), "logs", "blog_scraper.log")

try:
    os.makedirs(os.path.dirname(LOG_FILE), exist_ok=True)
except Exception:
    LOG_FILE = None

logging.basicConfig(
    level=logging.INFO,
    format="%(asctime)s [%(levelname)s] %(message)s",
    handlers=[logging.StreamHandler(sys.stdout)],
)
logger = logging.getLogger(__name__)

# ─── RSS Feeds ───────────────────────────────────────────────────────────────

RSS_FEEDS = [
    {
        "url": "https://www.artificialintelligence-news.com/feed/",
        "source_name": "AI News",
    },
    {
        "url": "https://venturebeat.com/category/ai/feed/",
        "source_name": "VentureBeat AI",
    },
    {
        "url": "https://syncedreview.com/feed/",
        "source_name": "Synced",
    },
]

# ─── Templates ───────────────────────────────────────────────────────────────

NEWS_TEMPLATE = """## Summary

{summary}

## Key Details

{details}

## Why It Matters for API Pricing

{impact}

## Related Models

{related}

---
*This article was automatically generated from {source} and curated for APIForge readers.*
"""

PRICE_CHANGE_TEMPLATE = """## Summary

AI model pricing has shifted: {provider} has updated costs for **{model_name}**.

## Price Changes

| Metric | Old Price | New Price | Change |
|--------|----------|-----------|--------|
| Input (per 1M tokens) | {old_input}/M | {new_input}/M | {input_delta} |
| Output (per 1M tokens) | {old_output}/M | {new_output}/M | {output_delta} |

## What This Means

{analysis}

## Compare This Model

{compare}

---
*This article was automatically generated from pricing data updates on {date}.*
"""


# ─── RSS Scraping ─────────────────────────────────────────────────────────────

def fetch_rss_articles():
    """Fetch articles from all configured RSS feeds."""
    articles = []

    for feed in RSS_FEEDS:
        try:
            logger.info(f"Fetching RSS: {feed['url']}")
            resp = requests.get(feed["url"], timeout=30, headers={"User-Agent": "Mozilla/5.0 (compatible; APIForge/1.0; +https://apiforge-production.up.railway.app)"})
            resp.raise_for_status()

            root = ET.fromstring(resp.content)

            ns = {"": root.tag.split("}")[0][1:]} if "}" in root.tag else {}
            channel = root if root.tag == "channel" else root.find("channel", ns)
            if channel is None:
                channel = root

            items = channel.findall("item") if ns else channel.findall("item")
            if not items:
                items = channel.findall(".//item")

            for item in items[:3]:
                title_el = item.find("title")
                desc_el = item.find("description")
                link_el = item.find("link")
                date_el = item.find("pubDate")

                title = title_el.text if title_el is not None else ""
                description = desc_el.text if desc_el is not None else ""
                link = link_el.text if link_el is not None else ""
                pub_date = date_el.text if date_el is not None else ""

                if not title:
                    continue

                clean_desc = _strip_html(description)[:300]

                articles.append({
                    "title": _clean_text(title),
                    "description": clean_desc,
                    "link": link,
                    "pub_date": pub_date,
                    "source_name": feed["source_name"],
                })

            logger.info(f"  Got {min(len(items), 3)} articles from {feed['source_name']}")

        except Exception as e:
            logger.error(f"RSS fetch failed for {feed['url']}: {e}")

    return articles


# ─── Price Change Detection ──────────────────────────────────────────────────

def detect_price_changes():
    """Query our own API to detect if any model prices changed since last scrape."""
    articles = []

    try:
        headers = {
            "Authorization": f"Bearer {API_KEY}",
            "Accept": "application/json",
            "User-Agent": "APIForge-Blog/1.0",
        }
        resp = requests.post(
            PRICES_WEBHOOK,
            json={"check_only": True},
            headers=headers,
            timeout=30,
        )
        if resp.status_code == 200:
            data = resp.json()
            if data.get("changes"):
                for change in data["changes"]:
                    articles.append({
                        "title": f"Price Change Alert: {change['name']} Input Now ${change['input_cost_per_m']}/M",
                        "excerpt": f"{change['name']} pricing has shifted. See the new rates and compare with alternatives.",
                        "content": PRICE_CHANGE_TEMPLATE.format(
                            provider=change.get("provider", "Unknown"),
                            model_name=change["name"],
                            old_input=f"${change.get('old_input', 'N/A')}",
                            new_input=f"${change['input_cost_per_m']}",
                            old_output=f"${change.get('old_output', 'N/A')}",
                            new_output=f"${change.get('output_cost_per_m', 0)}",
                            input_delta=_delta_text(change.get("old_input", 0), change["input_cost_per_m"]),
                            output_delta=_delta_text(change.get("old_output", 0), change.get("output_cost_per_m", 0)),
                            analysis=change.get("analysis", "Stay informed about the latest AI API pricing."),
                            compare=f"See how [{change['name']}](https://apiforge.com/api-cost/{change.get('slug', '')}) compares.",
                            date=datetime.now().strftime("%B %d, %Y"),
                        ),
                        "category": "pricing",
                        "source_name": "APIForge Pricing Engine",
                        "source_url": "",
                        "published": True,
                    })
    except Exception as e:
        logger.error(f"Price change detection failed: {e}")

    return articles


# ─── Article Generation ──────────────────────────────────────────────────────

def generate_article(rss_item):
    """Generate a structured Markdown article from an RSS item."""
    title = rss_item["title"]
    description = rss_item["description"]
    source = rss_item["source_name"]

    impact = _generate_impact(title, description)
    related = _find_related_models(title, description)

    details = f"The original report from {source} highlights key developments in the AI landscape."
    if rss_item["link"]:
        details += f" [Read the full story]({rss_item['link']})."

    content = NEWS_TEMPLATE.format(
        summary=description if description else "Latest AI industry update.",
        details=details,
        impact=impact,
        related=related if related else "Compare AI model pricing on [APIForge](https://apiforge.com).",
        source=source,
    )

    return {
        "title": _clean_text(title),
        "excerpt": description[:250] if description else "",
        "content": content,
        "category": "news",
        "source_name": source,
        "source_url": rss_item.get("link", ""),
        "published": True,
    }


# ─── Helpers ─────────────────────────────────────────────────────────────────

def _strip_html(text):
    import re
    return re.sub(r"<[^>]+>", "", text)


def _clean_text(text):
    text = _strip_html(text)
    text = text.replace("\n", " ").replace("\r", "")
    text = " ".join(text.split())
    return text[:500]


def _delta_text(old, new):
    if old == 0 or old is None or old == "N/A":
        return "—"
    diff = new - old
    pct = (diff / old) * 100
    direction = "&#9650;" if diff > 0 else "&#9660;"
    return f"{direction} {abs(pct):.1f}%"


def _generate_impact(title, description):
    combined = (title + " " + description).lower()
    if "openai" in combined or "gpt" in combined:
        return "OpenAI pricing changes affect millions of developers. Compare the latest GPT model costs on APIForge to stay within budget."
    if "anthropic" in combined or "claude" in combined:
        return "Anthropic's pricing strategy continues to evolve. Developers should track Claude costs against alternatives like GPT-4o and Gemini."
    if "google" in combined or "gemini" in combined:
        return "Google's Gemini pricing is competitive for high-volume use cases. Check our comparison tables for the latest rates."
    if "llama" in combined or "meta" in combined:
        return "Open-source models like Llama offer significant cost savings through providers like Groq and Together AI."
    if "deepseek" in combined or "mistral" in combined:
        return "European and Chinese AI labs are driving prices down. DeepSeek and Mistral now offer compelling alternatives to US providers."
    if "price" in combined or "cost" in combined or "cheap" in combined:
        return "Pricing shifts in the AI API market can impact your bottom line significantly. Always compare before committing."
    return "Staying informed about AI industry movements helps you make cost-effective API choices for your projects."


def _find_related_models(title, description):
    combined = (title + " " + description).lower()
    models = []
    MODEL_KEYWORDS = {
        "gpt-4o": "GPT-4o",
        "gpt-4o-mini": "GPT-4o mini",
        "claude-3-5-sonnet": "Claude 3.5 Sonnet",
        "gemini-2-5-pro": "Gemini 2.5 Pro",
        "llama-3-3-70b": "Llama 3.3 70B",
        "deepseek-v3": "DeepSeek-V3",
        "mistral-large": "Mistral Large",
    }
    for slug, name in MODEL_KEYWORDS.items():
        if name.lower() in combined:
            models.append(f"- [{name}](https://apiforge.com/api-cost/{slug})")
    return "\n".join(models[:5]) if models else ""


# ─── Webhook Delivery ────────────────────────────────────────────────────────

def post_to_wordpress(articles):
    """POST generated articles to the Laravel blog webhook."""
    headers = {
        "Authorization": f"Bearer {API_KEY}",
        "Content-Type": "application/json",
        "User-Agent": "APIForge-Blog/1.0",
    }

    posted = 0
    for article in articles[:10]:
        try:
            resp = requests.post(WEBHOOK_URL, json=article, headers=headers, timeout=60)
            resp.raise_for_status()
            result = resp.json()
            logger.info(f"Posted: {article['title'][:80]}... -> {result.get('slug', 'ok')}")
            posted += 1
            time.sleep(1)
        except Exception as e:
            logger.error(f"Post failed: {article['title'][:60]}... -> {e}")

    return posted


# ─── Main ────────────────────────────────────────────────────────────────────

def main():
    logger.info("=" * 60)
    logger.info("APIForge Blog Generator — Run started")

    articles = []

    rss_articles = fetch_rss_articles()
    for item in rss_articles:
        article = generate_article(item)
        articles.append(article)

    price_articles = detect_price_changes()
    articles.extend(price_articles)

    if not articles:
        logger.warning("No articles generated. Nothing to post.")
        return

    posted = post_to_wordpress(articles)
    logger.info(f"Done. {posted}/{len(articles)} articles posted.")


if __name__ == "__main__":
    main()
