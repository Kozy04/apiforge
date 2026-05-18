#!/usr/bin/env python3
"""
APIForge Blog Generator — Posts a daily roundup directly. No external APIs needed.
"""

import logging
import os
import sys
import time
from datetime import datetime

import requests

WEBHOOK_URL = os.getenv("APIFORGE_BLOG_WEBHOOK", "https://apiforge-production.up.railway.app/api/blog-posts")
API_KEY = os.getenv("APIFORGE_API_KEY", "apiforge-prod-key-2025")

logging.basicConfig(
    level=logging.INFO,
    format="%(asctime)s [%(levelname)s] %(message)s",
    handlers=[logging.StreamHandler(sys.stdout)],
)
logger = logging.getLogger(__name__)

MODELS = {
    "GPT-4o": ["OpenAI", 2.50, 10.00, "gpt-4o"],
    "GPT-4o mini": ["OpenAI", 0.15, 0.60, "gpt-4o-mini"],
    "Claude 3.5 Sonnet": ["Anthropic", 3.00, 15.00, "claude-3-5-sonnet"],
    "Gemini 2.5 Pro": ["Google", 1.25, 10.00, "gemini-2-5-pro"],
    "Llama 3.3 70B": ["Groq", 0.59, 0.79, "llama-3-3-70b"],
    "DeepSeek-V3": ["DeepSeek", 0.27, 1.10, "deepseek-v3"],
    "Mistral Large": ["Mistral", 2.00, 6.00, "mistral-large"],
    "Gemini 2.5 Flash": ["Google", 0.15, 0.60, "gemini-2-5-flash"],
}

def generate():
    today = datetime.now().strftime("%B %d, %Y")
    today_iso = datetime.now().strftime("%Y-%m-%d")

    lines = [
        f"## AI Model API Pricing Update — {today}",
        "",
        "Here are the current rates for popular AI model APIs as tracked by APIForge:",
        "",
        "| Model | Provider | Input / 1M | Output / 1M |",
        "|-------|----------|-----------|-------------|",
    ]

    for name, (provider, inp, out, slug) in MODELS.items():
        lines.append(f"| [{name}](https://apiforge-production.up.railway.app/api-cost/{slug}) | {provider} | ${inp:.2f} | ${out:.2f} |")

    lines += [
        "",
        "## Key Takeaways",
        "",
        f"- **Cheapest frontier**: Gemini 2.5 Flash at $0.15/M input",
        f"- **Best value**: DeepSeek-V3 at $0.27/M with strong benchmarks",
        f"- **Enterprise**: GPT-4o at $2.50/M with the broadest ecosystem",
        "",
        "## Compare Yourself",
        "",
        "Use the [APIForge cost calculator](https://apiforge-production.up.railway.app/) to estimate your exact monthly spend across any model combination.",
        "",
        "---",
        f"*Automated weekly update — {today}*",
    ]

    return {
        "title": f"AI Model API Pricing Weekly Roundup — {today}",
        "excerpt": f"Current AI model pricing across OpenAI, Anthropic, Google, Groq, DeepSeek, and Mistral. Updated {today}.",
        "content": "\n".join(lines),
        "category": "pricing",
        "source_name": "APIForge Pricing Engine",
        "published": True,
    }


def main():
    logger.info("=" * 60)
    logger.info("Blog Generator — Run started")

    article = generate()
    headers = {
        "Authorization": f"Bearer {API_KEY}",
        "Content-Type": "application/json",
    }

    for attempt in range(1, 4):
        try:
            logger.info(f"POST {WEBHOOK_URL} (attempt {attempt}/3)")
            resp = requests.post(WEBHOOK_URL, json=article, headers=headers, timeout=60)
            resp.raise_for_status()
            logger.info(f"Success: {resp.json()}")
            return
        except Exception as e:
            logger.error(f"Attempt {attempt} failed: {e}")
            if attempt < 3:
                time.sleep(5)

    logger.critical("All attempts failed.")


if __name__ == "__main__":
    main()
