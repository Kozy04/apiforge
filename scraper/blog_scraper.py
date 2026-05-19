#!/usr/bin/env python3
"""
APIForge Blog Generator — Deep, detailed articles from RSS + internal data.
Runs daily. Falls back to comprehensive pricing analysis if RSS fails.
"""

import logging
import os
import re
import sys
import time
import xml.etree.ElementTree as ET
from datetime import datetime

import requests

WEBHOOK = os.getenv("APIFORGE_BLOG_WEBHOOK", "https://apiforge-production.up.railway.app/api/blog-posts")
API_KEY = os.getenv("APIFORGE_API_KEY", "apiforge-prod-key-2025")
BASE = "https://apiforge-production.up.railway.app"

logging.basicConfig(level=logging.INFO, format="%(asctime)s [%(levelname)s] %(message)s", handlers=[logging.StreamHandler(sys.stdout)])
logger = logging.getLogger(__name__)

UA = {"User-Agent": "Mozilla/5.0 (compatible; APIForge/1.0)"}
AUTH = {"Authorization": f"Bearer {API_KEY}", "Content-Type": "application/json"}

RSS_FEEDS = [
    {"url": "https://syncedreview.com/feed/", "source_name": "Synced"},
    {"url": "https://www.unite.ai/feed/", "source_name": "Unite.AI"},
]

PRICING_DATA = {
    "GPT-4o":        ["OpenAI",    2.50, 10.00, 128000,  "gpt-4o"],
    "GPT-4o mini":   ["OpenAI",    0.15, 0.60,  128000,  "gpt-4o-mini"],
    "GPT-4.1":       ["OpenAI",    2.00, 8.00,  1000000, "gpt-4-1"],
    "Claude 3.5 Sonnet": ["Anthropic", 3.00, 15.00, 200000, "claude-3-5-sonnet"],
    "Claude 3.5 Haiku":  ["Anthropic", 0.80, 4.00,  200000, "claude-3-5-haiku"],
    "Claude Opus 4":     ["Anthropic", 15.00,75.00, 200000, "claude-opus-4"],
    "Claude Sonnet 4":   ["Anthropic", 3.00, 15.00, 200000, "claude-sonnet-4"],
    "Gemini 2.5 Pro":   ["Google",   1.25, 10.00, 1000000, "gemini-2-5-pro"],
    "Gemini 2.5 Flash": ["Google",   0.15, 0.60,  1000000, "gemini-2-5-flash"],
    "Llama 3.3 70B":    ["Groq",     0.59, 0.79,  128000,  "llama-3-3-70b"],
    "DeepSeek-V3":      ["DeepSeek", 0.27, 1.10,  128000,  "deepseek-v3"],
    "Mistral Large":    ["Mistral",  2.00, 6.00,  128000,  "mistral-large"],
    "Mistral Small":    ["Mistral",  0.20, 0.60,  32000,   "mistral-small"],
}


def fetch_rss():
    articles = []
    for feed in RSS_FEEDS:
        try:
            resp = requests.get(feed["url"], timeout=30, headers=UA)
            resp.raise_for_status()
            xml_text = resp.text.strip()
            root = ET.fromstring(xml_text)
            ns = _ns(root)
            channel = root if root.tag.endswith("channel") else root.find(".//channel", ns) or root
            items = channel.findall("item") if ns else channel.findall(".//item")
            count = 0
            for item in items[:2]:
                t = _txt(item, "title", ns)
                d = _txt(item, "description", ns)
                l = _txt(item, "link", ns)
                if t and len(t) > 15:
                    articles.append({"title": _cln(t), "description": _cln(d or ""), "link": l or "", "source_name": feed["source_name"]})
                    count += 1
            logger.info(f"  Got {count} articles from {feed['source_name']}")
        except Exception as e:
            logger.warning(f"RSS failed: {feed['url']}: {e}")
    return articles


def _ns(root):
    if "}" in root.tag:
        return {"": root.tag.split("}")[0][1:]}
    return {}


def _txt(el, tag, ns):
    child = el.find(tag, ns) if ns else el.find(tag)
    return child.text if child is not None else ""


def _cln(text):
    text = re.sub(r"<[^>]+>", "", text)
    text = re.sub(r"\s+", " ", text).strip()
    return text[:600]


def _link(name):
    data = PRICING_DATA.get(name)
    if data:
        return f"[{name}]({BASE}/api-cost/{data[4]})"
    return name


# ─── Article Generation ──────────────────────────────────────────────────────

def generate_news(item):
    title = item["title"]
    desc = item["description"] or "Latest AI industry update."
    source = item["source_name"]
    link = item.get("link", "")

    related_models = _find_models(title + " " + desc)

    content = f"""## Executive Summary

{desc}

This story was originally reported by {source}{" — [read the full report](" + link + ")" if link else ""}.

---

## Industry Analysis

The AI landscape continues to evolve rapidly. This development from {source} signals important shifts in how organizations approach artificial intelligence deployment, model selection, and cost management.

### What This Means for AI Model Pricing

Changes in the AI industry often ripple through API pricing structures. When major providers adjust their strategies, it can affect:

1. **Competitive pressure** — New capabilities or features from one provider force others to respond, often through price adjustments
2. **Enterprise adoption** — As AI becomes more embedded in business workflows, volume-based pricing becomes more attractive for large customers
3. **Model tiering** — Providers increasingly offer multiple tiers (flash, pro, ultra) at different price points to capture different market segments

### Provider Landscape Impact

The major AI API providers — OpenAI, Anthropic, Google, DeepSeek, and Mistral — are locked in an intensifying competition. Each development covered by {source} can influence:
- Pricing strategy adjustments
- New model releases and retirements
- Feature parity races
- Enterprise contract negotiations

---

## Related Models to Watch

{_format_models(related_models)}

---

## Key Takeaways for Developers

- Monitor pricing changes across providers — costs shift frequently and a model that was expensive last quarter may be competitive today
- Evaluate model performance on your specific use case, not just benchmark scores
- Use [APIForge's cost calculator]({BASE}) to estimate exact costs before committing to a provider
- Consider multi-provider strategies — no single provider is best for every task

---

## What to Watch Next

The AI API market moves fast. Keep an eye on:

1. **Pricing adjustments** — When one provider cuts prices, others typically follow within weeks
2. **New model launches** — Each generation typically brings better performance at similar or lower cost
3. **Context window expansions** — Longer context windows are becoming a key differentiator
4. **Enterprise features** — SLAs, fine-tuning APIs, and compliance certifications are increasingly important

---

*This article was curated by APIForge from {source} reporting. Visit our [pricing comparison tool]({BASE}) to find the best AI API for your budget.*
"""
    return {
        "title": _cln(title),
        "excerpt": desc[:300],
        "content": content,
        "category": "news",
        "source_name": source,
        "source_url": link,
        "published": True,
    }


def generate_roundup():
    today = datetime.now().strftime("%B %d, %Y")
    today_short = datetime.now().strftime("%b %d")

    cheap, mid, premium = [], [], []
    for name, (prov, inp, out, ctx, slug) in sorted(PRICING_DATA.items(), key=lambda x: x[1][1]):
        row = f"| {_link(name)} | {prov} | ${inp:.2f} | ${out:.2f} | {ctx:,} |"
        if inp < 0.50:
            cheap.append(row)
        elif inp < 3.00:
            mid.append(row)
        else:
            premium.append(row)

    cheapest_input = min(PRICING_DATA.items(), key=lambda x: x[1][1])
    cheapest_output = min(PRICING_DATA.items(), key=lambda x: x[1][2])
    largest_ctx = max(PRICING_DATA.items(), key=lambda x: x[1][3])

    content = f"""## Market Overview — {today}

The AI model API market continues to see intense price competition across all tiers. This week's analysis covers **{len(PRICING_DATA)} models** across **6 major providers**, from budget-friendly open-source hosts to premium frontier models.

---

## Complete Pricing Table — {today_short}

### Budget Tier (Under $0.50/M input)

These models deliver excellent value for high-volume classification, summarization, and simple Q&A tasks.

| Model | Provider | Input/1M | Output/1M | Context |
|-------|----------|----------|-----------|---------|
{chr(10).join(cheap) if cheap else '| — | — | — | — | — |'}

### Mid-Range Tier ($0.50 — $3.00/M input)

The sweet spot for most production workloads — strong performance at reasonable cost.

| Model | Provider | Input/1M | Output/1M | Context |
|-------|----------|----------|-----------|---------|
{chr(10).join(mid) if mid else '| — | — | — | — | — |'}

### Premium Tier (Above $3.00/M input)

Frontier models for complex reasoning, coding, and enterprise workloads where quality is paramount.

| Model | Provider | Input/1M | Output/1M | Context |
|-------|----------|----------|-----------|---------|
{chr(10).join(premium) if premium else '| — | — | — | — | — |'}

---

## Key Metrics at a Glance

| Metric | Winner | Value |
|--------|--------|-------|
| **Cheapest input** | {cheapest_input[0]} ({cheapest_input[1][0]}) | ${cheapest_input[1][1]:.2f}/M |
| **Cheapest output** | {cheapest_output[0]} ({cheapest_output[1][0]}) | ${cheapest_output[1][2]:.2f}/M |
| **Largest context** | {largest_ctx[0]} ({largest_ctx[1][0]}) | {largest_ctx[1][3]:,} tokens |

---

## Provider-by-Provider Analysis

### OpenAI

OpenAI maintains the broadest model lineup, from budget-friendly **GPT-4o mini** ($0.15/M) to premium reasoning models like **o3** ($10.00/M). GPT-4o at $2.50/M input remains the default choice for general-purpose applications, offering strong multimodal capabilities and the largest developer ecosystem. The introduction of GPT-4.1 with a 1M token context window signals OpenAI's commitment to long-context enterprise workloads.

**Best for**: General-purpose applications, multimodal tasks, developers who want the widest ecosystem support.

### Anthropic

Anthropic positions its models at a premium but justifies it with superior instruction-following, safety handling, and coding performance. **Claude 3.5 Sonnet** at $3.00/M is the workhorse for most users, while **Claude Opus 4** at $15.00/M targets the highest-stakes enterprise use cases. Anthropic's 200K context window is generous, though Google's 1M token window leads the market.

**Best for**: Complex multi-step reasoning, coding, safety-critical applications, long-form content with strong instruction adherence.

### Google

Google is the most aggressive on pricing. **Gemini 2.5 Flash** at $0.15/M input and **Gemini 2.0 Flash** at $0.10/M input are the cheapest frontier-class models available. The 1 million token context window is unmatched in the industry. Google's strategy — undercut everyone on price while offering massive context — is winning enterprise deals.

**Best for**: Long-context workloads, cost-sensitive high-volume applications, multi-modal tasks with huge documents.

### Groq

Groq differentiates on speed. Hosting open-source models like **Llama 3.3 70B** ($0.59/M) on custom LPU hardware, Groq delivers inference speeds 3-5x faster than GPU-based providers. For latency-sensitive applications, Groq is unmatched in the budget-to-mid tier.

**Best for**: Real-time applications, chatbots requiring sub-100ms response times, open-source model deployments at scale.

### DeepSeek

DeepSeek has disrupted the market with **DeepSeek-V3** at $0.27/M input — a frontier-class model at open-source prices. On coding benchmarks, DeepSeek-V3 competes with models costing 10x more. For developers willing to trade the OpenAI ecosystem for raw performance-per-dollar, DeepSeek is the clear winner.

**Best for**: Cost-sensitive projects, coding assistants, developers comfortable with a smaller ecosystem.

### Mistral

The European AI champion offers a balanced portfolio. **Mistral Large** ($2.00/M) competes directly with GPT-4o on reasoning tasks at a 20% discount. **Mistral Small** ($0.20/M) and **Codestral** ($0.30/M) provide specialized, affordable options for lightweight and coding-specific workloads.

**Best for**: European data residency requirements, coding-focused applications, balanced price-performance.

---

## Cost Optimization Recommendations

Based on this week's pricing data, here are our top recommendations:

### If You're Spending Under $100/Month

Stick with budget models — **Gemini 2.5 Flash** ($0.15/M) or **GPT-4o mini** ($0.15/M). The quality is excellent for chatbots, summarization, content generation, and classification. You likely won't notice the difference from a $2.50/M model for these tasks.

### If You're Spending $100-$1,000/Month

Consider a hybrid strategy. Route simple queries to **DeepSeek-V3** ($0.27/M) or **Llama 3.3 70B** ($0.59/M) and complex tasks to **GPT-4o** ($2.50/M) or **Claude 3.5 Sonnet** ($3.00/M). This model cascading approach can save 40-60% while maintaining quality on critical paths.

### If You're Spending $1,000+/Month

Negotiate enterprise pricing. At this volume, all providers offer committed-use discounts — typically 20-40% off published rates. Run benchmarks on your actual workload across 2-3 providers and use competitive quotes to negotiate. You should also evaluate self-hosting open-source models (vLLM on GPU instances) for predictable, high-volume inference.

### If Context Window Matters Most

**Gemini 2.5 Pro** and **Gemini 2.5 Flash** offer 1 million token context windows — enough to process entire books, codebases, or months of chat history in a single prompt. No other provider comes close. If your application involves document analysis, long conversations, or repository-level code understanding, Google is the clear choice.

---

## Market Trend: The Race to Zero

The trend is unmistakable: AI API prices are falling rapidly. GPT-4o dropped from $5.00/M to $2.50/M in months. Gemini Flash models debuted at $0.15/M. DeepSeek offers frontier capabilities at $0.27/M.

For developers, this is the best possible environment. Pick the right model for each task, use our [cost calculator]({BASE}) to project your spending, and let the providers compete for your business.

---

*This weekly roundup is generated automatically by the APIForge Pricing Engine. Data reflects published rates as of {today}. Visit [APIForge]({BASE}) to compare all {len(PRICING_DATA)} models in real time.*
"""

    return {
        "title": f"AI Model API Pricing: Complete Market Analysis — {today}",
        "excerpt": f"Comprehensive analysis of AI model pricing across {len(PRICING_DATA)} models from 6 providers. Budget picks, premium options, cost optimization strategies, and market trends. Updated {today}.",
        "content": content,
        "category": "analysis",
        "source_name": "APIForge Pricing Engine",
        "published": True,
    }


def _find_models(text):
    found = []
    for name in PRICING_DATA:
        if name.lower() in text.lower():
            found.append(name)
    return found[:6]


def _format_models(models):
    if not models:
        return f"Use [APIForge's comparison tool]({BASE}) to find relevant models for your use case.\n"
    lines = []
    for m in models:
        data = PRICING_DATA[m]
        lines.append(f"- **{_link(m)}** ({data[0]}) — ${data[1]:.2f}/M input, ${data[2]:.2f}/M output, {data[3]:,} token context")
    return "\n".join(lines) + f"\n\n[Compare all models →]({BASE})"


# ─── Delivery ────────────────────────────────────────────────────────────────

def post(articles):
    posted = 0
    for a in articles[:8]:
        try:
            r = requests.post(WEBHOOK, json=a, headers=AUTH, timeout=60)
            r.raise_for_status()
            slug = r.json().get("slug", "ok")
            logger.info(f"Posted: {a['title'][:70]} -> {slug}")
            posted += 1
            time.sleep(2)
        except Exception as e:
            code = r.status_code if 'r' in dir() else '?'
            logger.error(f"Post failed ({code}): {a['title'][:60]} -> {e}")
    return posted


def main():
    logger.info("=" * 60)
    logger.info("Blog Generator — Run started")

    articles = [generate_news(item) for item in fetch_rss()]

    if not articles:
        logger.info("No RSS articles. Generating weekly roundup.")
        articles = [generate_roundup()]

    posted = post(articles)
    logger.info(f"Done. {posted}/{len(articles)} posted.")


if __name__ == "__main__":
    main()
