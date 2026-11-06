# ParentingHub SmartCart AI — Hackathon Feature Plan & Execution Roadmap

**Project:** ParentingHub — PayPal Hackathon  
**Document:** Feature Planning & Wave Breakdown (Phase 3)  
**Date:** 2026-11-03  
**Status:** Approved for Execution  

---

## 1. Feature Specifications

### Feature A: AI Parenting Shopping Assistant
- **User Story**: As a busy parent, I want to describe my baby's age, stage, and needs in natural Urdu/English so I can get instant recommendations grounded in genuine ParentingHub products.
- **Components**:
  - Backend: `backend/api/ai/assistant.php` (Intent recognition, context retention, database hydration).
  - Frontend: `frontend/js/smartcart-ai.js` (Floating & modal shopping assistant, responsive message feed, product micro-cards).
- **Verification**: Query "anti-colic bottle for newborn" returns Philips Avent & Tommee Tippee bottles with exact prices from the database.

### Feature B: Smart Product Recommendations & Stage Matching
- **User Story**: As a parent on a budget, I want to receive tailored product suggestions that respect my budget and child's developmental milestone.
- **Components**:
  - Backend: Stage classifier mapping months to developmental stages; SQL filter enforcing `in_stock = 1` and `price <= budget`.
  - Frontend: "Why recommended" badge and direct product link.
- **Verification**: Query with "budget Rs 5,000" only returns products with price <= 5000.

### Feature C: Smart Product Comparison
- **User Story**: As a parent undecided between two strollers or feeding bottles, I want a clean factual comparison to make an informed choice.
- **Components**:
  - Backend: `backend/api/ai/compare.php` (Fetches DB columns: brand, price, rating, options, features, tags; prompts LLM for concise comparative verdict).
  - Frontend: Interactive comparison drawer/table rendered with ParentingHub brand colors.
- **Verification**: Selecting two bottles displays side-by-side specs with verified database prices and key differences.

### Feature D: Permission-Based Smart Cart
- **User Story**: As a customer, I want the AI to propose a ready-to-order starter pack, but I must review and approve it before any item is added to my cart.
- **Components**:
  - Backend: `backend/api/ai/cart.php` (Generates temporary proposal ticket with expiration; validates approved items against catalog).
  - Frontend: Review sheet with item checkboxes, quantities, and explicit "Approve & Add to Cart" CTA.
- **Verification**: Clicking approve correctly updates `S.cart` and localStorage without any unauthorized payment or cart mutation.

### Feature E: Secure PayPal Sandbox Integration
- **User Story**: As an international or local customer, I want to pay safely using PayPal with transparent currency conversion and instant order confirmation.
- **Components**:
  - Backend: `backend/api/paypal/orders.php`, `backend/api/paypal/capture.php`, `backend/api/paypal/webhook.php`.
  - Frontend: Hosted PayPal JS SDK buttons integrated in checkout modal.
  - Shared Engine: `backend/api/lib/pricing.php` with server-side PKR-to-USD conversion.
- **Verification**: End-to-end sandbox purchase with PayPal buyer credentials creates `Confirmed` order in MySQL with verified capture ID.

---

## 2. Implementation Waves

| Wave | Phase | Scope | Dependencies | Output / Deliverable |
|---|---|---|---|---|
| **Wave 1** | Phase 4 | **Core Backend & DB Foundation**<br>• Local dev DB initialization script (287 products, schema)<br>• Additive schema migrations (`ph_payments`, etc.)<br>• Shared pricing & validation engine (`pricing.php`)<br>• Refactor `/api/orders` to use shared engine | None | Reproducible local database passing `verify_database.sql`, unit-tested pricing engine |
| **Wave 2** | Phase 5 & 6 | **SmartCart AI Assistant & Recommendations**<br>• `backend/api/ai/assistant.php`<br>• `backend/api/ai/compare.php`<br>• Rate limiting & security guardrails<br>• Assistant UI widget (`smartcart-ai.js`, `smartcart.css`) | Wave 1 | Natural language shopping assistant returning verified product cards and comparison tables |
| **Wave 3** | Phase 7 | **Permission-Based Smart Cart**<br>• Proposal creation & approval APIs<br>• Interactive review UI in assistant drawer<br>• Seamless sync with `app.js` cart state | Wave 2 | AI-generated cart bundles approved by user and loaded into cart |
| **Wave 4** | Phase 8 | **PayPal Sandbox Integration**<br>• PayPal Orders v2 REST client (`paypal_client.php`)<br>• Create & capture endpoints with server amount validation<br>• Frontend PayPal SDK button integration in checkout modal<br>• Dual-currency display (PKR + USD) | Wave 1, Wave 3 | Verified end-to-end payment capture in sandbox mode |
| **Wave 5** | Phase 9, 10 & 11 | **Polish, Security QA & Demo Package**<br>• Mobile responsive polish & ParentingHub branding review<br>• End-to-end testing (COD, Bank, PayPal, AI flows)<br>• Security hardening (rate limits, input sanitization)<br>• Hackathon documentation, video script, demo instructions | Waves 1–4 | Fully functional prototype, test reports, and hackathon presentation |

---

## 3. Risk Mitigation & Guardrails Checklist

- [x] **No live database touches**: All development stays strictly in local workspace.
- [x] **No hardcoded secrets**: `.env` with dummy sandbox credentials only; `.env.example` committed.
- [x] **Zero LLM hallucinations on pricing/stock**: Prices and stock status always fetched and rendered directly from database records.
- [x] **Idempotency on payments**: Unique indexes on `paypal_order_id` and `capture_id` to prevent double-charging.
- [x] **Entropy upgrade on order IDs**: Server-side random alphanumeric order IDs replacing 5-digit client random numbers.
- [x] **Clean separation of concerns**: Existing files untouched except for non-breaking delegation hooks to shared engines.

