#!/usr/bin/env python3
"""
APIForge Blog Generator — Generates articles from internal pricing data.
No external RSS feeds needed. Runs daily.
"""

import json
import logging
import os
import sys
import time
from datetime import datetime, timezone

import requests

WEBHOOK_URL = os.getenv("APIFORGE_BLOG_WEBHOOK", "https://apiforge-production.up.railway.app/api/blog-posts")
API_KEY = os.getenv("APIFORGE_API_KEY", "apiforge-prod-key-2025")
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

HEADERS = {
    "Authorization": f"Bearer {API_KEY}",
    "Content-Type": "application/json",
    "User-Agent": "Mozilla/5.0 (compatible; APIForge/1.0)",
}

BASE_URL = os.getenv("APIFORGE_BASE_URL", "https://apiforge-production.up.railway.app")

def generate_weekly_roundup():
    """Generate a weekly pricing summary post using internal data."""
    logger.info("Generating weekly pricing roundup...")

    try:
        resp = requests.get(f"{BASE_URL}/api/search?q=cheap", headers={"User-Agent": HEADERS["User-Agent"]}, timeout=30)
        if resp.status_code != 200:
            logger.warning(f"Search API returned {resp.status_code}")
            return []

        search_data = []
        try:
            search_data = resp.json()
        except Exception:
            pass

        if not search_data:
            content = "## AI Model Pricing Update\n\nVisit [APIForge]({BASE_URL}) to compare the latest rates.\n\n---\n*Automated weekly update.*"
        else:
            lines = ["## This Week's AI Model Pricing Update", "", "Here are the current rates for popular models:", ""]
            for m in search_data[:8]:
                lines.append(f"- **{m['name']}** ({m['provider']}): ${float(m['input_cost']):.2f}/M input")
            lines.append("")
            lines.append(f"[Compare all 28 models on APIForge]({BASE_URL})")
            content = "\n".join(lines)

        today = datetime.now().strftime("%B %d, %Y")
        return [{
            "title": f"AI Model API Pricing Weekly Roundup — {today}",
            "excerpt": f"Weekly summary of current AI model API pricing across OpenAI, Anthropic, Google, and more. Updated {today}.",
            "content": content,
            "category": "pricing",
            "source_name": "APIForge Pricing Engine",
            "published": True,
        }]
    except Exception as e:
        logger.error(f"Roundup generation failed: {e}")
        return []


def post_articles(articles):
    """POST articles to the blog webhook."""
    posted = 0
    for article in articles[:5]:
        try:
            resp = requests.post(WEBHOOK_URL, json=article, headers=HEADERS, timeout=60)
            resp.raise_for_status()
            result = resp.json()
            logger.info(f"Posted: {article['title'][:80]} -> {result.get('slug', 'ok')}")
            posted += 1
            time.sleep(1)
        except Exception as e:
            logger.error(f"Post failed: {article['title'][:60]} -> {e}")
    return posted


def main():
    logger.info("=" * 60)
    logger.info("APIForge Blog Generator — Run started")

    articles = generate_weekly_roundup()

    if not articles:
        logger.warning("No articles generated.")
        return

    posted = post_articles(articles)
    logger.info(f"Done. {posted}/{len(articles)} articles posted.")


if __name__ == "__main__":
    main()
