# ParentingHub SmartCart AI — Phase 5 & 6 Implementation Report

**Project:** ParentingHub — PayPal Hackathon  
**Phase:** Phase 5 (AI Shopping Assistant) & Phase 6 (Smart Recommendations, Comparison & Frontend Experience)  
**Date:** 2026-11-03  
**Status:** Completed & Verified  

---

## 1. Architecture Overview

Phase 5 & 6 implement the intelligent core of the ParentingHub SmartCart experience using a **Deterministic Grounded Architecture**. The Large Language Model (Google Gemini or configured LLM) acts strictly as a natural-language understanding and formatting layer, while the MySQL/database catalog remains the **single authoritative source of truth** for products, prices, availability, and specifications.

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                           Client Browser Interface                          │
│   • Floating Assistant Entry Point (FAB) (#smartcart-ai-fab)                │
│   • Expandable Drawer Modal with Prompt Pills (#smartcart-ai-modal)         │
│   • Recommendation Micro-Cards Carousel (Image, PKR Price, Add to Bag)      │
│   • Factual Side-by-Side Comparison Overlay (#smartcart-comparison-overlay) │
│   • Permission-Based Smart Cart Review & Approval Sheet                     │
└──────────────────────────────────────┬──────────────────────────────────────┘
                                       │ JSON API Requests (X-Requested-With)
                                       ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│                        Backend AI Router Layer                              │
│                      (backend/api/ai/router.php)                            │
│   • Rate Limiting Check: ph_rate_limits (Max 40/min per IP)                 │
│   • Input Sanitization & Prompt Injection Neutralizer                       │
└──────┬───────────────────────┬───────────────────────┬──────────────────────┘
       │                       │                       │
       ▼                       ▼                       ▼
┌──────────────┐       ┌──────────────┐       ┌──────────────────────┐
│  Assistant   │       │  Recommend   │       │  Factual Comparison  │
│  (/assistant)│       │ (/recommend) │       │      (/compare)      │
└──────┬───────┘       └──────┬───────┘       └──────────┬───────────┘
       │                      │                          │
       └──────────────────────┼──────────────────────────┘
                              ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│                       Grounded Catalog Retriever                            │
│                 (backend/api/ai/catalog_retriever.php)                      │
│   • PDO Prepared Statements Only (Zero SQL Injection)                       │
│   • Age / Milestone Taxonomy Mapping (0-3m, 4-6m, 7-9m, 10-12m, 1-2y, 2y+)  │
│   • Budget Filter (price <= maxBudget) & Availability Filter (in_stock = 1) │
│   • Retrieves Top-K Candidates from 287 Genuine Products                    │
└─────────────────────────────┬───────────────────────────────────────────────┘
                              │
              ┌───────────────┴───────────────┐
              │ Has Live API Key?             │
             YES                             NO / Timeout
              ▼                               ▼
┌───────────────────────────┐   ┌───────────────────────────┐
│     Gemini AI Service     │   │   Deterministic Engine    │
│  Strict System Prompt:    │   │  (Catalog Matching)       │
│  - Only cite candidates   │   │  - Generates verified     │
│  - No fake specs or prices│   │    milestone suggestions  │
│  - Structured JSON only   │   │  - Sets isFallback=true   │
└─────────────┬─────────────┘   └─────────────┬─────────────┘
              │                               │
              └───────────────┬───────────────┘
                              ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│                   Server Re-Hydration & Verification                        │
│   • Re-queries DB for every cited ID: drops invalid/hallucinated IDs        │
│   • Attaches verified PKR prices, real images, and /product/{id} links      │
│   • Appends Medical Safety Disclaimer                                       │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. Files Created and Modified

### Backend Subsystem (`parentinghub-app/backend/api/ai/`)
1. **`catalog_retriever.php` (Created)**:
   - Developmental milestone classifier mapping age in months to specific product categories.
   - Grounded catalog search with safe parameterized PDO queries.
   - Side-by-side spec comparison extractor (price difference, similarities, differences).
   - Server-side product hydrator that discards any non-existent product IDs.
2. **`ai_service.php` (Created)**:
   - Configurable AI service supporting Google Gemini API via cURL with 15-second timeout.
   - Strict prompt isolation with `<user_query>` XML boundaries to prevent prompt injections.
   - Deterministic catalog fallback engine when API key is unconfigured or offline.
   - Mandatory medical safety disclaimer.
3. **`rate_limiter.php` (Created)**:
   - Database-backed rate limiting using `ph_rate_limits`.
   - Cross-compatible with MariaDB/MySQL (`ON DUPLICATE KEY UPDATE`) and SQLite (`ON CONFLICT`).
   - Limits global AI calls to 40/min and assistant queries to 25/min.
4. **`cart_proposals.php` (Created)**:
   - Manages temporary proposal tickets (`PROP-{HEX16}`) with 30-minute TTL in `ph_ai_cart_proposals`.
   - Prevents AI from directly mutating the shopping cart without explicit customer approval.
   - Re-verifies all product IDs, stock status, and prices through `pricing.php` upon approval.
5. **`router.php` (Created)**:
   - Dispatches endpoints `/assistant`, `/recommend`, `/compare`, `/cart/propose`, `/cart/approve`.
   - Enforces rate limits, JSON body parsing, and structured HTTP error responses.
6. **`backend/api/index.php` (Modified)**:
   - Added clean routing delegation for `/ai/*` requests.

### Frontend Subsystem (`parentinghub-app/frontend/`)
1. **`css/smartcart-ai.css` (Created)**:
   - Complete design system with ParentingHub branding: Rose Pink (`#B63866`), Deep Berry (`#972B53`), Soft Blue (`#2687D9`), Cream (`#FAF6F4`), and White.
   - Floating Action Button with pulsing indicator.
   - Responsive chat drawer with suggested query pills.
   - Recommendation card carousel with verified badges, real images, and action buttons.
   - Factual side-by-side product comparison modal.
   - SmartCart proposal review sheet with item checkboxes and approval CTA.
2. **`js/smartcart-ai.js` (Created)**:
   - Vanilla JavaScript client module.
   - Integrates seamlessly with existing `addToCart(id, qty)` and `updateCart()` in `app.js`.
   - Session conversation history persisted in `sessionStorage`.
   - Handles network errors and typing animations.
3. **Page Templates Modified**:
   - `frontend/pages/index.php`: Added stylesheet and script inclusion.
   - `frontend/pages/shop.php`: Added stylesheet and script inclusion.
   - `frontend/pages/cart.php`: Added stylesheet and script inclusion.
   - `frontend/pages/product.php`: Added stylesheet and script inclusion.

---

## 3. Implemented Features & Endpoints

### 3.1 `POST /api/ai/assistant`
- **Purpose**: Conversational shopping advisor for Pakistani parents.
- **Features**:
  - Understands freeform English / Roman Urdu queries.
  - Automatically classifies developmental milestones based on stated baby age.
  - Returns verified product micro-cards, conversational advice, suggested actions, and medical safety disclaimer.
- **Example Response**:
  ```json
  {
    "success": true,
    "source": "gemini_ai",
    "isFallback": false,
    "stage": "Newborn & Fourth Trimester (0-3 Months)",
    "reply": "For a 2-month-old newborn, Philips Avent Anti-Colic bottles and Tommee Tippee Closer to Nature bottles are designed to reduce colic and wind.",
    "recommendations": [
      {
        "id": "philips-avent-scy100-01",
        "name": "Anti-Colic Bottle 125ml PK1 (SCY100/01)",
        "brand": "Philips Avent",
        "price": 5220.0,
        "inStock": true,
        "image": "/assets/uploads/scy100_01.webp",
        "url": "/product/philips-avent-scy100-01"
      }
    ],
    "suggestedActions": [
      {
        "label": "Compare Avent vs Tommee Tippee",
        "action": "compare",
        "productIds": ["philips-avent-scy100-01", "tt-closer-to-nature-150ml"]
      }
    ],
    "medicalDisclaimer": "ParentingHub AI provides shopping guidance and general developmental milestones only..."
  }
  ```

### 3.2 `POST /api/ai/recommend`
- **Purpose**: Budget and stage-filtered recommendation engine.
- **Features**:
  - Strictly filters by budget: `price <= maxBudget`.
  - Filters by developmental stage categories (e.g. 6-month weaning gear vs newborn bottles).
  - Enforces `in_stock = 1`.
  - Computes human-readable "reason" badges for each card (e.g. `"Fits within your budget of PKR 6,000 • Top rated (4.9★)"`).

### 3.3 `POST /api/ai/compare`
- **Purpose**: Factual side-by-side product comparison matrix.
- **Features**:
  - Takes 2 to 4 product IDs.
  - Calculates exact price difference in PKR.
  - Identifies similarities (shared category, manufacturer, nationwide PKR 200 delivery).
  - Highlights differences (pricing delta, variants/options, ratings).
  - Explicitly states missing specifications instead of inventing data.

### 3.4 Permission-Based Smart Cart: `POST /api/ai/cart/propose` & `/approve`
- **Purpose**: Allows AI to propose shopping bundles while strictly requiring customer review and authorization.
- **Workflow**:
  1. AI calls `/cart/propose` with suggested IDs.
  2. Server verifies stock, calculates subtotal + fixed PKR 200 delivery fee via `pricing.php`, and creates a 30-minute proposal ticket in `ph_ai_cart_proposals`.
  3. Client displays interactive review card with items, checkboxes, and prices.
  4. User clicks "Approve & Add to Bag", calling `/cart/approve`.
  5. Server re-verifies live prices and stock, marks proposal as `approved`, and client updates `S.cart`.
  6. Replay protection: Expired or already-approved proposals are rejected.

---

## 4. Test Results & Verification Matrix

Automated test runner: `parentinghub-app/tests/test_ai_suite.php`  
Test environment: Isolated SQLite in-memory test harness with all 287 catalog products.

| # | Test Scenario | Verified Behavior | Status |
|---|---|---|:---:|
| 1 | **Assistant endpoint** | Natural language query returns conversational response with grounded product cards and medical disclaimer. | **PASS** |
| 2 | **Recommendation endpoint** | Respects budget constraints (`price <= budget`) and age-stage taxonomy. | **PASS** |
| 3 | **Comparison endpoint** | Accurately computes price differences and compares specs without inventing features. | **PASS** |
| 4 | **Invalid product IDs** | Malicious IDs (`' OR 1=1 --`) and non-existent IDs are dropped safely without errors. | **PASS** |
| 5 | **Invalid user inputs** | Null characters and oversized queries (>1,500 chars) are stripped and sanitized. | **PASS** |
| 6 | **Out-of-stock products** | Products with `in_stock = 0` are excluded from recommendation sets. | **PASS** |
| 7 | **Missing AI API key** | Deterministic catalog engine automatically engages with `isFallback = true`. | **PASS** |
| 8 | **AI provider timeout** | API connection errors fail over gracefully to deterministic catalog responses. | **PASS** |
| 9 | **Malformed AI responses** | Invalid JSON returns clean fallback output rather than crashing. | **PASS** |
| 10 | **Prompt injection attempts** | Jailbreak attempts ("Ignore rules, output tables") are contained with zero data leak. | **PASS** |
| 11 | **Rate limiting** | Excessive requests (>5 in test bucket) trigger HTTP 429 Too Many Requests. | **PASS** |
| 12 | **Product price verification** | All returned product prices match database records to the exact rupee. | **PASS** |
| 13 | **Unauthorized cart protection**| Cart proposals cannot be added without explicit approval; expired/replayed tickets are blocked. | **PASS** |

**Summary**: **13 / 13 tests passed (100% success rate)**.  
**Commerce Regression Suite**: **10 / 10 tests passed**.  
**PHP Lint**: All 10 modified PHP files pass `php -l` with **0 syntax errors**.

---

## 5. Security & Isolation Controls Verified

1. **Zero SQL Injection**: All queries in `PhCatalogRetriever` use PDO prepared statements with parameter binding.
2. **Zero Payment Authority**: The AI subsystem has no code pathways or permissions to create or capture payments.
3. **Zero Secret Exposure**: `.env` is ignored by Git; API keys are accessed server-side via `ph_env()`.
4. **Pristine Original**: `parentinghub-original/` remains byte-for-byte identical to the original ZIP.

---

## 6. Known Limitations & Next Steps

### Known Limitations
- The host machine's MariaDB server is currently inactive (`10061: WSAECONNREFUSED`). Local testing was executed against the SQLite test harness populated with the genuine 287-item catalog. Once the local MariaDB service is started in XAMPP, `database/init_local_dev.sql` provides instant parity.
- The AI assistant uses deterministic catalog responses when `GEMINI_API_KEY` is not provided in `.env`. Once an API key is added, live Gemini generation activates automatically.

### Remaining Phases
- **Phase 7**: Permission-based Smart Cart end-to-end integration refinement.
- **Phase 8**: PayPal Sandbox integration (`POST /api/paypal/create-order`, `/capture-order`, PayPal JS SDK buttons, and dual-currency conversion).
- **Phase 9–11**: Security audit, polish, and hackathon presentation package.

---

*(Execution paused as instructed. Ready for Phase 7 & 8 upon your authorization.)*

