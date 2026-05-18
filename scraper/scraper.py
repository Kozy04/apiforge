#!/usr/bin/env python3
"""
APIForge Price Scraper — Sends baseline pricing data via webhook.
No external scraping needed — uses known pricing from local data.
"""

import json
import logging
import os
import sys
import time
from datetime import datetime, timezone

import requests

WEBHOOK_URL = os.getenv("APIFORGE_WEBHOOK_URL", "https://apiforge-production.up.railway.app/api/update-prices")
API_KEY = os.getenv("APIFORGE_API_KEY", "apiforge-prod-key-2025")
LOG_FILE = os.path.join(os.path.dirname(__file__), "logs", "scraper.log")

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

KNOWN_MODELS = [
    {"provider_slug": "openai",       "name": "GPT-4o",           "slug": "gpt-4o",           "input_cost_per_m": 2.50,  "output_cost_per_m": 10.00, "context_window": 128000,  "latency_score": 0.8},
    {"provider_slug": "openai",       "name": "GPT-4o mini",      "slug": "gpt-4o-mini",      "input_cost_per_m": 0.15,  "output_cost_per_m": 0.60,  "context_window": 128000,  "latency_score": 0.5},
    {"provider_slug": "openai",       "name": "GPT-4.1",          "slug": "gpt-4-1",          "input_cost_per_m": 2.00,  "output_cost_per_m": 8.00,  "context_window": 1000000, "latency_score": 1.0},
    {"provider_slug": "openai",       "name": "o3",               "slug": "o3",               "input_cost_per_m": 10.00, "output_cost_per_m": 40.00, "context_window": 200000,  "latency_score": 3.0},
    {"provider_slug": "openai",       "name": "o4-mini",          "slug": "o4-mini",          "input_cost_per_m": 1.10,  "output_cost_per_m": 4.40,  "context_window": 200000,  "latency_score": 2.0},
    {"provider_slug": "anthropic",    "name": "Claude 3.5 Sonnet", "slug": "claude-3-5-sonnet", "input_cost_per_m": 3.00,  "output_cost_per_m": 15.00, "context_window": 200000,  "latency_score": 1.0},
    {"provider_slug": "anthropic",    "name": "Claude 3.5 Haiku",  "slug": "claude-3-5-haiku",  "input_cost_per_m": 0.80,  "output_cost_per_m": 4.00,  "context_window": 200000,  "latency_score": 0.4},
    {"provider_slug": "anthropic",    "name": "Claude Opus 4",     "slug": "claude-opus-4",      "input_cost_per_m": 15.00, "output_cost_per_m": 75.00, "context_window": 200000,  "latency_score": 2.0},
    {"provider_slug": "anthropic",    "name": "Claude Sonnet 4",   "slug": "claude-sonnet-4",    "input_cost_per_m": 3.00,  "output_cost_per_m": 15.00, "context_window": 200000,  "latency_score": 1.0},
    {"provider_slug": "google",       "name": "Gemini 2.5 Pro",   "slug": "gemini-2-5-pro",   "input_cost_per_m": 1.25,  "output_cost_per_m": 10.00, "context_window": 1000000, "latency_score": 1.2},
    {"provider_slug": "google",       "name": "Gemini 2.5 Flash", "slug": "gemini-2-5-flash", "input_cost_per_m": 0.15,  "output_cost_per_m": 0.60,  "context_window": 1000000, "latency_score": 0.3},
    {"provider_slug": "google",       "name": "Gemini 2.0 Flash", "slug": "gemini-2-0-flash", "input_cost_per_m": 0.10,  "output_cost_per_m": 0.40,  "context_window": 1000000, "latency_score": 0.2},
    {"provider_slug": "groq",         "name": "Llama 3.3 70B",    "slug": "llama-3-3-70b",    "input_cost_per_m": 0.59,  "output_cost_per_m": 0.79,  "context_window": 128000,  "latency_score": 0.1},
    {"provider_slug": "groq",         "name": "Mixtral 8x7B",     "slug": "mixtral-8x7b",     "input_cost_per_m": 0.27,  "output_cost_per_m": 0.27,  "context_window": 32768,   "latency_score": 0.1},
    {"provider_slug": "groq",         "name": "Gemma 2 9B",       "slug": "gemma-2-9b",       "input_cost_per_m": 0.20,  "output_cost_per_m": 0.20,  "context_window": 8192,    "latency_score": 0.05},
    {"provider_slug": "mistral",      "name": "Mistral Large",    "slug": "mistral-large",    "input_cost_per_m": 2.00,  "output_cost_per_m": 6.00,  "context_window": 128000,  "latency_score": 1.5},
    {"provider_slug": "mistral",      "name": "Mistral Small",    "slug": "mistral-small",    "input_cost_per_m": 0.20,  "output_cost_per_m": 0.60,  "context_window": 32000,   "latency_score": 0.3},
    {"provider_slug": "mistral",      "name": "Codestral",        "slug": "codestral",        "input_cost_per_m": 0.30,  "output_cost_per_m": 0.90,  "context_window": 256000,  "latency_score": 0.4},
    {"provider_slug": "deepseek",     "name": "DeepSeek-V3",      "slug": "deepseek-v3",      "input_cost_per_m": 0.27,  "output_cost_per_m": 1.10,  "context_window": 128000,  "latency_score": 0.8},
    {"provider_slug": "deepseek",     "name": "DeepSeek-R1",      "slug": "deepseek-r1",      "input_cost_per_m": 0.55,  "output_cost_per_m": 2.19,  "context_window": 128000,  "latency_score": 3.0},
]


def main():
    logger.info("=" * 60)
    logger.info("APIForge Price Scraper — Run started")

    payload = {
        "models": KNOWN_MODELS,
        "scraped_at": datetime.now(timezone.utc).isoformat(),
    }
    headers = {
        "Authorization": f"Bearer {API_KEY}",
        "Content-Type": "application/json",
    }

    for attempt in range(1, 4):
        try:
            logger.info(f"POST {WEBHOOK_URL} (attempt {attempt}/3) — {len(KNOWN_MODELS)} models")
            resp = requests.post(WEBHOOK_URL, json=payload, headers=headers, timeout=60)
            resp.raise_for_status()
            result = resp.json()
            logger.info(f"Success: {result}")
            return
        except requests.exceptions.RequestException as e:
            logger.error(f"Attempt {attempt} failed: {e}")
            if attempt < 3:
                time.sleep(5)

    logger.critical("All retries exhausted.")


if __name__ == "__main__":
    main()
