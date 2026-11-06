# Devpost Hackathon Submission: ParentingHub SmartCart AI

## 🏷️ Tagline
Intelligent Pediatric AI Advisor & Dual-Currency PayPal Orders v2 E-Commerce Platform for New Parents.

---

## 💡 Inspiration
Welcoming a newborn is one of life’s most joyful experiences, but for first-time parents in Pakistan, it often brings overwhelming anxiety. When an infant suffers from colic, reflux, or feeding difficulty late at night, parents face confusing choices:
- Generic e-commerce storefronts only display raw product listings with zero pediatric guidance.
- Chatbots provide general medical text without verified, purchasable solutions.
- International parents and diaspora families struggle with localized payment barriers.

We built **ParentingHub SmartCart AI** to bridge clinical guidance with transactional execution—allowing parents to describe symptoms in their natural everyday language (**Roman Urdu** or **English**) and immediately receive developmentally grounded pediatric advice and instant, dual-currency PayPal checkout.

---

## 🛠️ What It Does
1. **Bilingual Clinical Parenting Assistant**:
   Understands colloquial maternal inquiries in Roman Urdu (*"mera 2 month ka baby hai doodh peete hue rota hai gas ki wajah se"*) and English, mapping queries to developmental stages (0–3M Newborn, 3–6M Infant, 6–12M Weaning).
2. **Qdrant Hybrid Semantic Retrieval**:
   Leverages **Google Gemini Embedding 2** to convert queries into 768-dimensional normalized vectors and queries a local **Qdrant Vector Database** indexing 317 clinical parenting guides and authentic product specifications with sub-15ms latency.
3. **Server-Enforced SmartCart Proposal Contract**:
   Generates structured, cryptographically signed AI Cart proposals. The AI acts as a read-only advisor—the customer must explicitly inspect and click **"Approve & Add to Bag"** before any cart mutation occurs.
4. **Enterprise PayPal Orders v2 Sandbox Checkout**:
   Seamlessly converts catalog prices to USD using fixed real-time rate validation (280 PKR = $1 USD). Features atomic inventory reservations on MariaDB InnoDB (`uq_active_slot`) to prevent overselling, idempotency tracking, and official PayPal SDK checkout.

---

## ⚙️ How We Built It
- **AI & Retrieval Engine**: Google Gemini 3.5 Flash Lite for generative advice, Gemini Embedding 2 (768-dim) for semantic vectors, and standalone Qdrant Vector Store (v1.12.0) with HNSW indexing.
- **Backend Architecture**: Zero-bloat, high-performance native PHP 8.2 backend with strict typing and RESTful endpoints (`/api/ai/assistant`, `/api/ai/cart-proposal/approve`, `/api/paypal/create-order`, `/api/paypal/capture-order`).
- **Database Layer**: MariaDB / MySQL 8.0 with InnoDB storage engine, ACID row-level locks, and strict composite unique constraints.
- **Payment Processing**: PayPal REST API (Orders v2 API engine) supporting authenticated buyer approval, capture recovery, and simulated fallback.
- **Frontend Experience**: Vanilla ES6+ JavaScript with responsive CSS3 design tokens and zero heavy client framework bloat for maximum accessibility on mobile and desktop.

---

## 🥊 Challenges We Ran Into
1. **Cross-Language Semantic Gap**:
   Colloquial maternal terms in Roman Urdu (*"chuchi", "feeder", "jhoola"*) rarely match medical product labels (*"Anti-Colic Feeding Bottle with AirFree Vent"*). We solved this by pairing Gemini embeddings with domain-specific knowledge chunk synthesis in Qdrant, achieving >0.80 cosine similarity across languages.
2. **AI Price Hallucination & Cart Safety**:
   LLMs can fabricate prices or mutate shopping carts without user awareness. We implemented a strict Server-Side Proposal Contract: AI output is strictly read-only until the parent clicks "Approve", and all final totals are recalculated from primary database records.
3. **Database Concurrency & Overselling**:
   High-concurrency checkout can lead to race conditions. We engineered an atomic reservation system in MariaDB using a composite unique constraint on `active_slot`, ensuring simultaneous checkouts never double-allocate limited stock.

---

## 🏆 Accomplishments That We're Proud Of
- **100% Pass Rate Across 23 Test Suites**: Built and verified 23 automated regression and security suites covering unit pricing, vector retrieval, concurrency rollbacks, and PayPal idempotency.
- **Sub-15ms Retrieval on 317 Chunks**: Successfully embedded and synced the entire catalog and editorial corpus into Qdrant.
- **Dual-Currency E-Commerce Flow**: Flawless real-time transition from PKR catalog prices to compliant USD PayPal Orders v2 checkout.

---

## 📚 What We Learned
- How to implement zero-trust security patterns for AI-to-e-commerce handoffs.
- The power of Qdrant vector search in overcoming cross-language nuances between Roman Urdu and English.
- Best practices in PayPal Orders v2 idempotency, stock reservation lifecycles, and webhook signature verification.

---

## 🔮 What's Next for ParentingHub SmartCart AI
- Integrating multi-region pediatric guidelines with licensed healthcare practitioners in Pakistan.
- Automated WhatsApp notifications and courier dispatch tracking via Raast QR and PayPal.
- Expanding semantic knowledge ingestion to infant sleep, immunization schedules, and nutrition milestones.

---

## 🧰 Built With
- `php`
- `mysql`
- `mariadb`
- `paypal-api`
- `paypal-orders-v2`
- `google-gemini`
- `qdrant`
- `vector-search`
- `javascript`
- `rest-api`

