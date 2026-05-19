#!/usr/bin/env python3
"""
APIForge Price Scraper — Fetches LIVE pricing from OpenRouter model API.
One call covers all major providers with real-time rates. No hardcoded fallbacks.
"""

import logging
import os
import sys
import time
from datetime import datetime, timezone

import requests

WEBHOOK_URL = os.getenv("APIFORGE_WEBHOOK_URL", "https://apiforge-production.up.railway.app/api/update-prices")
API_KEY = os.getenv("APIFORGE_API_KEY", "apiforge-prod-key-2025")

logging.basicConfig(level=logging.INFO, format="%(asctime)s [%(levelname)s] %(message)s", handlers=[logging.StreamHandler(sys.stdout)])
logger = logging.getLogger(__name__)
AUTH = {"Authorization": f"Bearer {API_KEY}", "Content-Type": "application/json"}

# Provider name normalization
PROVIDER_MAP = {
    "openai": "openai", "anthropic": "anthropic", "google": "google",
    "meta": "groq", "mistral": "mistral", "deepseek": "deepseek",
    "cohere": "cohere", "together": "together-ai", "fireworks": "fireworks-ai",
    "nvidia": "replicate", "qwen": "replicate", "amazon": "together-ai",
}

OR_PRICE_URL = "https://openrouter.ai/api/v1/models"


def fetch_openrouter():
    """Fetch all models with live pricing from OpenRouter's public API."""
    logger.info("Fetching live model data from OpenRouter...")
    try:
        r = requests.get(OR_PRICE_URL, timeout=60, headers={
            "User-Agent": "APIForge/1.0",
            "Accept": "application/json",
        })
        r.raise_for_status()
        data = r.json()

        models = []
        for m in data:
            name = m.get("name", "") or m.get("id", "")
            or_id = m.get("id", "")

            # Use the first segment as provider slug
            provider_slug = or_id.split("/")[0].lower()
            if provider_slug in PROVIDER_MAP:
                provider_slug = PROVIDER_MAP[provider_slug]

            pricing = m.get("pricing", {})
            prompt_cost = max(0, float(pricing.get("prompt", 0)) * 1000000)
            completion_cost = max(0, float(pricing.get("completion", 0)) * 1000000)

            ctx = int(m.get("context_length", 0))

            # Build slug from id
            model_slug = or_id.replace("/", "-").lower().replace(" ", "-")

            models.append({
                "provider_slug": provider_slug,
                "name": name,
                "slug": model_slug[:100],
                "input_cost_per_m": round(prompt_cost, 6),
                "output_cost_per_m": round(completion_cost, 6),
                "context_window": ctx,
                "latency_score": 1.5,
                "category": "text",
            })

        # Deduplicate by slug
        seen = set()
        unique = []
        for m in models:
            if m["slug"] not in seen:
                seen.add(m["slug"])
                unique.append(m)

        logger.info(f"OpenRouter: {len(unique)} models across {len(set(m['provider_slug'] for m in unique))} providers")
        return unique

    except Exception as e:
        logger.error(f"OpenRouter fetch failed: {e}")
        return []


# ─── Manual supplements for providers not on OpenRouter ─────────────────────

def supplement_media_models():
    """Add image, video, audio models not covered by OpenRouter."""
    return [
        {"provider_slug":"openai","name":"DALL-E 3","slug":"dall-e-3","input_cost_per_m":0.00,"output_cost_per_m":0.04,"context_window":0,"latency_score":8.0,"category":"image"},
        {"provider_slug":"openai","name":"Whisper","slug":"whisper","input_cost_per_m":0.00,"output_cost_per_m":0.006,"context_window":0,"latency_score":3.0,"category":"audio"},
        {"provider_slug":"stability-ai","name":"Stable Diffusion 3.5","slug":"stable-diffusion-3-5","input_cost_per_m":0.00,"output_cost_per_m":0.004,"context_window":0,"latency_score":3.5,"category":"image"},
        {"provider_slug":"stability-ai","name":"Stable Image Ultra","slug":"stable-image-ultra","input_cost_per_m":0.00,"output_cost_per_m":0.008,"context_window":0,"latency_score":5.0,"category":"image"},
        {"provider_slug":"stability-ai","name":"Stable Video Diffusion","slug":"stable-video-diffusion","input_cost_per_m":0.00,"output_cost_per_m":0.05,"context_window":0,"latency_score":30.0,"category":"video"},
        {"provider_slug":"runway","name":"Gen-3 Alpha","slug":"gen-3-alpha","input_cost_per_m":0.00,"output_cost_per_m":0.10,"context_window":0,"latency_score":25.0,"category":"video"},
        {"provider_slug":"runway","name":"Gen-2","slug":"gen-2","input_cost_per_m":0.00,"output_cost_per_m":0.05,"context_window":0,"latency_score":20.0,"category":"video"},
        {"provider_slug":"midjourney","name":"Midjourney V6","slug":"midjourney-v6","input_cost_per_m":0.00,"output_cost_per_m":0.00,"context_window":0,"latency_score":15.0,"category":"image"},
        {"provider_slug":"midjourney","name":"Midjourney Niji 6","slug":"midjourney-niji-6","input_cost_per_m":0.00,"output_cost_per_m":0.00,"context_window":0,"latency_score":15.0,"category":"image"},
        {"provider_slug":"ideogram","name":"Ideogram 2.0","slug":"ideogram-2","input_cost_per_m":0.00,"output_cost_per_m":0.008,"context_window":0,"latency_score":6.0,"category":"image"},
        {"provider_slug":"elevenlabs","name":"Eleven Multilingual v2","slug":"eleven-multilingual-v2","input_cost_per_m":0.00,"output_cost_per_m":0.015,"context_window":0,"latency_score":1.5,"category":"audio"},
        {"provider_slug":"elevenlabs","name":"Eleven Turbo 2.5","slug":"eleven-turbo-2-5","input_cost_per_m":0.00,"output_cost_per_m":0.005,"context_window":0,"latency_score":0.5,"category":"audio"},
        {"provider_slug":"replicate","name":"Flux Pro","slug":"flux-pro","input_cost_per_m":0.00,"output_cost_per_m":0.003,"context_window":0,"latency_score":5.0,"category":"image"},
        {"provider_slug":"replicate","name":"SDXL Turbo","slug":"sdxl-turbo","input_cost_per_m":0.00,"output_cost_per_m":0.001,"context_window":0,"latency_score":1.0,"category":"image"},
        {"provider_slug":"hugging-face","name":"Mistral 7B (HF)","slug":"hf-mistral-7b","input_cost_per_m":0.00,"output_cost_per_m":0.00,"context_window":32768,"latency_score":1.5,"category":"open-source"},
        {"provider_slug":"hugging-face","name":"Falcon 40B (HF)","slug":"hf-falcon-40b","input_cost_per_m":0.00,"output_cost_per_m":0.00,"context_window":8192,"latency_score":3.0,"category":"open-source"},
        {"provider_slug":"hugging-face","name":"StarCoder 2","slug":"starcoder-2","input_cost_per_m":0.00,"output_cost_per_m":0.00,"context_window":16384,"latency_score":2.0,"category":"open-source"},
        {"provider_slug":"cohere","name":"Embed v4","slug":"embed-v4","input_cost_per_m":0.10,"output_cost_per_m":0.00,"context_window":512,"latency_score":0.05,"category":"embedding"},
    ]


def main():
    logger.info("=" * 60)
    logger.info("Price Scraper — Run started (OpenRouter API)")

    models = fetch_openrouter()

    if not models:
        logger.critical("OpenRouter fetch returned nothing. Aborting.")
        sys.exit(1)

    models.extend(supplement_media_models())

    logger.info(f"Collecting {len(models)} models total.")
    payload = {"models": models, "scraped_at": datetime.now(timezone.utc).isoformat()}

    for attempt in range(1, 4):
        try:
            logger.info(f"POST {WEBHOOK_URL} (attempt {attempt}/3)")
            r = requests.post(WEBHOOK_URL, json=payload, headers=AUTH, timeout=60)
            r.raise_for_status()
            result = r.json()
            logger.info(f"Success: updated={result.get('updated',0)}, created={result.get('created',0)}")
            return
        except Exception as e:
            logger.error(f"Attempt {attempt} failed: {e}")
            if attempt < 3:
                time.sleep(5)

    logger.critical("All retries exhausted.")


if __name__ == "__main__":
    main()
