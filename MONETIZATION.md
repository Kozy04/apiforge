# APIForge — Monetization Stack & Revenue Streams

## Active Revenue Channels

### 1. Affiliate Click Tracking
- **Status**: ✅ LIVE
- **How**: Every "Get API Key" / provider link uses `trackedUrl()` → `GET /out/{provider}?model={slug}&from={page}` → records click → redirects to affiliate URL
- **Model**: `Click` (provider_id, api_model_id, ip_address, user_agent, referrer, page)
- **Dashboard**: Admin → Revenue Estimate + Top Providers by Clicks
- **16 providers** with real affiliate URLs (OpenAI, Anthropic, Google, Groq, Mistral, DeepSeek, Together, Cohere, Fireworks, Replicate, Stability AI, Midjourney, Runway, ElevenLabs, Hugging Face, Ideogram)
- **File**: `app/Http/Controllers/TrackClickController.php`

### 2. Lead Generation
- **Status**: ✅ LIVE
- **How**: "Get 3 Free API Quotes" form on every model page + comparison page → `POST /api/leads` → `Lead` model
- **Fields**: name, email, company, monthly_tokens, use_case, preferred_providers, status (new/contacted/sold)
- **Value**: $50-200 per qualified lead sold to API providers
- **Dashboard**: Admin → Recent Leads with status toggles
- **File**: `resources/views/partials/lead-form.blade.php`

### 3. Email Capture
- **Status**: ✅ LIVE
- **How**: "Get price drop alerts" widget → `POST /api/subscribe` → `Subscriber` model
- **Value**: Newsletter sponsorship $150/send, direct promotion to AI buyer audience
- **File**: `resources/views/partials/email-capture.blade.php`

### 4. Carbon Ads (Display Network)
- **Status**: ⏳ Awaiting publisher ID
- **Setup**: Sign up at https://carbonads.net → "Become a Publisher" → get `CARBON_ADS_ID`
- **Placements**: Footer (every page), Blog inline (mid-article), Model page inline (below FAQ)
- **Config**: Set `CARBON_ADS_ID` in Railway env vars
- **Files**: `resources/views/partials/ads.blade.php`, `ads-inline.blade.php`
- **RPM**: $10-25 (developer/tech audience pays premium)

### 5. Sponsored Provider Slots
- **Status**: ✅ Built, needs providers to pay
- **How**: Providers get premium card placement at top of homepage with "Sponsored" gold badge
- **Config**: Set `SPONSORED_PROVIDERS=openai,anthropic,stability-ai` in Railway env vars
- **Pricing**: $200/month per slot
- **Template**: `resources/views/model/index.blade.php` (Featured Providers section)
- **Action**: Email smaller providers (Groq, Together AI, Fireworks) offering slots

### 6. Advertiser Landing Page
- **Status**: ✅ LIVE at `/advertise`
- **Content**: Lists all 3 advertising options with pricing, why advertise here, CTA email
- **Link**: Footer → "Advertise"
- **Action**: Replace `advertise@apiforge.com` with your real email

---

## Setup Checklist

| # | Task | Done |
|---|------|------|
| 1 | Sign up at carbonads.net | ⬜ |
| 2 | Add `CARBON_ADS_ID` to Railway env vars | ⬜ |
| 3 | Update email on `/advertise` page | ⬜ |
| 4 | Set `ADMIN_PASSWORD` to strong value | ⬜ |
| 5 | Set `APP_URL` to https://apiforge-production.up.railway.app | ⬜ |
| 6 | Sign up at newsapi.org for blog content | ⬜ |
| 7 | Add `NEWSAPI_KEY` to Railway blog-scraper vars | ⬜ |
| 8 | Submit sitemap.xml to Google Search Console | ⬜ |
| 9 | Submit sitemap to Bing Webmaster Tools | ⬜ |
| 10 | Change cron schedule from `* * * * *` to `0 6 * * *` | ⬜ |

---

## Revenue Estimator (Targets)

| Channel | Monthly Target | How to reach |
|---------|---------------|-------------|
| Affiliates | $500-$2,000 | Drive traffic → more clicks → more conversions |
| Leads | $1,000-$5,000 | Sell qualified leads to providers at $50-200 each |
| Carbon Ads | $50-$500 | Need 5K-20K monthly pageviews |
| Sponsored Slots | $400-$1,200 | 2-3 providers at $200/mo each |
| Newsletter | $300-$900 | 2 sends/month at $150 each |
| **Total** | **$2,250-$9,600/mo** | |

---

## Traffic Growth Targets

| Traffic | AdSense RPM | Carbon RPM | Affiliate Clicks | Leads/Month |
|---------|-----------|-----------|-----------------|-------------|
| 1K/mo | — | $10-15 | 20-50 | 2-5 |
| 5K/mo | $5-8 | $15-20 | 100-250 | 10-25 |
| 10K/mo | $8-12 | $20-25 | 250-500 | 25-50 |
| 50K/mo | $12-18 | $25-30 | 1,000+ | 100+ |

---

## Technical Reference

### Env vars to configure on Railway
```
APP_URL = https://apiforge-production.up.railway.app
SCRAPER_API_KEY = apiforge-prod-key-2025
ADMIN_PASSWORD = (your-password)
CARBON_ADS_ID = (from carbonads.net)
NEWSAPI_KEY = (from newsapi.org)
SPONSORED_PROVIDERS = (comma-separated slugs)
```

### Cron jobs on Railway
| Name | Schedule | Command |
|------|----------|---------|
| blog-scraper | `0 6 * * *` | `python scraper/blog_scraper.py` |
| price-scraper | `0 3 * * 0` | `python scraper/scraper.py` |

### Key Files
| File | Purpose |
|------|---------|
| `app/Http/Controllers/TrackClickController.php` | Affiliate click tracking |
| `app/Http/Controllers/LeadController.php` | Lead + subscriber endpoints |
| `app/Models/Click.php` | Click data model |
| `app/Models/Lead.php` | Lead data model |
| `app/Models/Subscriber.php` | Email subscriber model |
| `resources/views/partials/ads.blade.php` | Footer ad slot |
| `resources/views/partials/ads-inline.blade.php` | In-content ad slot |
| `resources/views/partials/lead-form.blade.php` | Lead gen form |
| `resources/views/partials/email-capture.blade.php` | Email capture widget |
| `resources/views/advertise.blade.php` | Advertiser landing page |
| `resources/views/admin/dashboard.blade.php` | Admin revenue dashboard |
| `scraper/scraper.py` | Price scraper (OpenRouter API) |
| `scraper/blog_scraper.py` | Blog scraper (NewsAPI + RSS + roundup) |
