# ParentingHub SmartCart AI 🍼🤖
### Intelligent Parenting Advisor & Dual-Currency PayPal Orders v2 E-Commerce Platform

[![Tests](https://img.shields.io/badge/Regression%20Suites-23%2F23%20Passed%20(100%25)-success?style=for-the-badge&logo=checkmarx)](tests/)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL%20%2F%20MariaDB-InnoDB-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![PayPal](https://img.shields.io/badge/PayPal-Orders%20v2%20Sandbox-003087?style=for-the-badge&logo=paypal&logoColor=white)](https://developer.paypal.com/)
[![Gemini](https://img.shields.io/badge/Google%20Gemini-3.5%20Flash%20Lite-8E75B2?style=for-the-badge&logo=google&logoColor=white)](https://ai.google.dev/)
[![Qdrant](https://img.shields.io/badge/Qdrant-Vector%20Search%201.12-DC2626?style=for-the-badge&logo=qdrant&logoColor=white)](https://qdrant.tech/)
[![License](https://img.shields.io/badge/License-MIT-blue.svg?style=for-the-badge)](LICENSE)

---

## 🌟 Overview

**ParentingHub SmartCart AI** is an intelligent, pediatric-guided e-commerce platform designed to eliminate maternal and paternal anxiety for new parents in Pakistan. Combining **Google Gemini** generative intelligence, a **Qdrant Vector Database** indexing 317 clinical pediatric and developmental knowledge chunks, and an enterprise **PayPal Orders v2 Sandbox** engine, ParentingHub bridges conversational guidance with transactional execution.

Parents can converse naturally in **Roman Urdu** or **English** (e.g., *"mera 2 month ka baby hai doodh peete hue rota hai gas ki wajah se"*) and receive grounded, medically safe product recommendations (e.g., Philips Avent Anti-Colic bottles with AirFree vents), age-appropriate teat flow stages, and structured AI Cart proposals that can be added to bag and purchased in seconds.

---

## 🚀 Key Features

### 1. Contextual AI SmartCart Advisor
- **Bilingual Intent Understanding**: Deep comprehension of maternal inquiries in Roman Urdu (*"chuchi", "feeder", "jhoola", "pet mein gas"*) and English.
- **Pediatric Safety Gates**: Non-negotiable pediatric disclaimers and immediate referral for medical concerns (fever, lethargy, dehydration).
- **Developmental Stage Alignment**: Automatic developmental taxonomy mapping (0–3M Newborn, 3–6M Infant, 6–12M Weaning, 12M+ Toddler).
- **Price-Grounded Recommendations**: AI outputs are strictly constrained to genuine database catalog prices (no hallucinated pricing).

### 2. High-Performance Hybrid Vector Search (Qdrant + Gemini)
- **768-Dimensional Embeddings**: Real REST integration with `gemini-embedding-2` generating normalized vectors for maternal queries and catalog items.
- **Qdrant Collection**: 317 knowledge chunks indexed in collection `parentinghub_knowledge` running on `127.0.0.1:6333`.
- **Cross-Language Semantic Matching**: Cosine similarity retrieval bridges colloquial Roman Urdu symptoms with clinical English product specifications (Cosine similarity > 0.80).

### 3. Server-Enforced AI Cart Proposal Contract
- **Tamper-Proof Proposals**: AI generates cryptographically verifiable proposal tokens with structured line items, quantities, and verified prices.
- **User Approval Required**: Proposals are read-only until the customer explicitly clicks **"Approve & Add to Bag"**.
- **Server Verification**: The backend validates catalog availability, active stock, and pricing before cart mutation.

### 4. Enterprise PayPal Orders v2 Sandbox Checkout
- **Dual-Currency Conversion**: Fixed exchange rate conversion (280 PKR = $1 USD) compliant with PayPal Orders v2 USD requirements.
- **Atomic Stock Reservation**: MariaDB InnoDB `ph_inventory_reservations` with composite unique index `uq_active_slot` prevents overselling and race conditions during simultaneous checkouts.
- **Official PayPal SDK & Popup**: Official PayPal JS SDK modal popup for authenticated sandbox buyer approvals.
- **Zero-Trust Idempotency**: Order capture recovery, duplicate capture prevention, and webhook HMAC signature verification.

---

## 🏗️ System Architecture

```mermaid
sequenceDiagram
    autonumber
    actor Parent as Parent / Shopper
    participant UI as Responsive Storefront / Chat UI
    participant Backend as PHP Router & API Backend
    participant Qdrant as Qdrant Vector DB (Port 6333)
    participant Gemini as Google Gemini AI (3.5 Flash Lite)
    participant DB as MariaDB / MySQL (InnoDB)
    participant PayPal as PayPal Orders v2 Sandbox API

    Parent->>UI: Types Roman Urdu feeding query
    UI->>Backend: POST /api/ai/assistant
    Backend->>Gemini: Generate 768-dim query embedding
    Backend->>Qdrant: Hybrid Cosine search (317 chunks)
    Qdrant-->>Backend: Top relevant knowledge & product chunks
    Backend->>DB: Fetch verified prices & in_stock status
    Backend->>Gemini: Generate clinical advice & Cart Proposal
    Gemini-->>Backend: Structured proposal JSON + disclaimer
    Backend-->>UI: Displays recommendation card & proposal bubble
    Parent->>UI: Clicks "Approve & Add to Bag"
    UI->>Backend: POST /api/ai/cart-proposal/approve
    Backend->>DB: Atomic stock verification
    Backend-->>UI: Items added to cart
    Parent->>UI: Clicks "Proceed to Checkout"
    UI->>Backend: POST /api/paypal/create-order
    Backend->>PayPal: POST /v2/checkout/orders (USD conversion)
    PayPal-->>Backend: Order ID + Approval Link
    Backend->>DB: Record 'Pending Payment' + reserve inventory
    Backend-->>UI: Return PayPal order details
    UI->>PayPal: Launch PayPal Sandbox Modal / Approval
    Parent->>PayPal: Logs into Sandbox Buyer Account & Approves
    PayPal-->>UI: OnApprove callback
    UI->>Backend: POST /api/paypal/capture-order
    Backend->>PayPal: POST /v2/checkout/orders/{id}/capture
    PayPal-->>Backend: HTTP 201 COMPLETED + Capture ID
    Backend->>DB: Commit inventory reservation & Order 'Confirmed'
    Backend-->>UI: Order receipt & instant tracking token
```

---

## 🛠️ Tech Stack

| Layer | Technologies |
| :--- | :--- |
| **Frontend** | Vanilla ES6+ JavaScript, CSS3 Design Tokens, HTML5 Responsive Templates |
| **Backend** | Native PHP 8.2 (Strict Types, Zero Bloat, Dependency-Free Architecture) |
| **Database** | MariaDB / MySQL 8.0+ (InnoDB Engine, Row-Level Locks, ACID Transactions) |
| **Vector DB** | Qdrant v1.12.0 Standalone Daemon (HNSW Cosine Distance Indexing) |
| **AI Models** | Google Gemini 3.5 Flash Lite (Inference) & Gemini Embedding 2 (768-dim vectors) |
| **Payments** | PayPal Orders v2 REST API (Sandbox, Capture Flow, Webhooks) |
| **Security** | 256-bit SSL, X-Payment-Token Auth, SQL Parameterization, Input Sanitization |

---

## 💻 Local Setup & Quickstart

### Prerequisites
- PHP 8.2+ with `pdo_mysql`, `curl`, `mbstring`, `openssl`
- MySQL or MariaDB (via XAMPP or standalone)
- Qdrant Vector DB binary (`qdrant.exe`)
- PayPal Developer Account (Sandbox Credentials)
- Google Gemini API Key

### 1. Clone the Repository
```bash
git clone https://github.com/YOUR_USERNAME/parentinghub-smartcart-ai.git
cd parentinghub-smartcart-ai
```

### 2. Configure Environment Variables
```bash
cp .env.example parentinghub-app/backend/.env
```
Open `parentinghub-app/backend/.env` and insert your credentials:
```ini
APP_ENV=local
DB_HOST=127.0.0.1
DB_NAME=parentinghub_db
DB_USER=root
DB_PASS=

# PayPal Sandbox Credentials
PAYPAL_MODE=sandbox
PAYPAL_ENV=sandbox
PAYPAL_CLIENT_ID=YOUR_PAYPAL_SANDBOX_CLIENT_ID
PAYPAL_CLIENT_SECRET=YOUR_PAYPAL_SANDBOX_CLIENT_SECRET
PAYPAL_FX_PKR_USD=280.0000

# Google Gemini API Key
GEMINI_API_KEY=YOUR_GEMINI_API_KEY
AI_MODEL=gemini-3.5-flash-lite

# Qdrant Vector Store
QDRANT_HOST=127.0.0.1
QDRANT_PORT=6333
QDRANT_COLLECTION=parentinghub_knowledge
```

### 3. Initialize Database
Import the local development schema and catalog seed:
```bash
mysql -u root parentinghub_db < parentinghub-app/database/init_local_dev.sql
```

### 4. Start Background Daemons
In separate terminal windows:
```powershell
# 1. Start Qdrant Vector DB
cd parentinghub-app/backend/cache/qdrant
./qdrant.exe

# 2. Start PHP Application Server
cd parentinghub-app
php -S 127.0.0.1:8080 router.php
```

### 5. Access the Live Application
Open your web browser and navigate to:
- **Storefront**: [http://127.0.0.1:8080/](http://127.0.0.1:8080/)
- **Shopping Bag**: [http://127.0.0.1:8080/cart](http://127.0.0.1:8080/cart)
- **Secure Checkout**: [http://127.0.0.1:8080/checkout](http://127.0.0.1:8080/checkout)

---

## 🧪 Comprehensive Regression Test Suite

ParentingHub includes **23 comprehensive test suites** covering unit, integration, and security audits. Run all suites in a single command:

```powershell
php parentinghub-app/tests/run_all_tests.php
```

### Test Suite Summary:
```
========================================================================
Test Suite Execution Summary: 23 / 23 Suites Passed (100% Pass Rate)
========================================================================
 [PASS] test_pricing_engine.php                 (17 passed, 0 failed)
 [PASS] test_stage2_suite.php                   (12 passed, 0 failed)
 [PASS] test_stage2_targeted_qa.php             (16 passed, 0 failed)
 [PASS] test_stage3_security_audit.php          (19 passed, 0 failed)
 [PASS] test_paypal_suite.php                   (29 passed, 0 failed)
 [PASS] test_stage4_paypal_suite.php            (22 passed, 0 failed)
 [PASS] test_stage4_recovery_concurrency.php    (27 passed, 0 failed)
 [PASS] test_stage4_final_audit.php             (33 passed, 0 failed)
 [PASS] test_recommendation_accuracy.php        (15 passed, 0 failed)
 [PASS] test_embedding_provider.php             (7 passed, 0 failed)
 [PASS] test_qdrant_vector_store.php            (4 passed, 0 failed)
 [PASS] test_knowledge_vector_sync.php          (6 passed, 0 failed)
 [PASS] test_stage3_integration.php             (5 passed, 0 failed)
 [PASS] test_stage3_provider_integration.php    (3 provider suites)
 [PASS] test_stage3_cross_language_queries.php  (6 cross-language audits)
 [PASS] test_mysql_integration.php              (51 passed, 0 failed)
 [PASS] test_mysql_transaction_fix.php          (37 passed, 0 failed)
 ...
========================================================================
SUCCESS: ALL 23 TEST SUITES PASSED CLEANLY WITH ZERO REGRESSIONS.
========================================================================
```

---

## 🔒 Security & Compliance

- **No Sensitive Data in Logs**: Credit card details, PayPal Client Secrets, and Gemini API keys are never written to log files or terminal output.
- **Stock Reservation Atomicity**: MariaDB InnoDB row locks prevent race conditions; active reservations have a strict 15-minute TTL.
- **Strict Role Separation**: AI responses operate on read-only endpoints; checkout mutations require explicit client user action.
- **Tamper-Resistant Pricing**: AI models never calculate or alter product pricing; all totals are computed server-side from primary catalog database tables.

---

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

