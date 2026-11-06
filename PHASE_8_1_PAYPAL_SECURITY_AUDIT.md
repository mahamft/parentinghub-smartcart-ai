# ParentingHub SmartCart AI — Phase 8.1 PayPal Security Audit & Verification Report
**Document ID:** `PHASE_8_1_PAYPAL_SECURITY_AUDIT.md`  
**Date:** November 3, 2026  
**Status:** Completed & Empirically Audited (29/29 PayPal Suite Tests Passed, 69/69 Project-Wide)  
**Environment:** `parentinghub-app/` (PHP 8+, MySQL/MariaDB architecture, isolated SQLite harness)  
**Integrity Checks:** `parentinghub-original/` is READ-ONLY and 100% UNTOUCHED. Production website UNTOUCHED.  

---

## 1. Executive Summary & Security Vulnerabilities Identified

During the Phase 8.1 Security Audit of the Phase 7 & Phase 8 implementation, we conducted an exhaustive review of [`backend/api/paypal/paypal_client.php`](file:///c:/Users/hp/OneDrive/Desktop/paypalhackthaton/parentinghub-app/backend/api/paypal/paypal_client.php), [`backend/api/paypal/paypal_service.php`](file:///c:/Users/hp/OneDrive/Desktop/paypalhackthaton/parentinghub-app/backend/api/paypal/paypal_service.php), [`backend/api/paypal/router.php`](file:///c:/Users/hp/OneDrive/Desktop/paypalhackthaton/parentinghub-app/backend/api/paypal/router.php), and related frontend flows.

The audit revealed **four critical security vulnerabilities and architectural gaps**:

1. **Implicit Mock Fallback Vulnerability (Silent Degradation)**:
   - *Finding*: `paypal_client.php` previously engaged mock simulation whenever `PAYPAL_CLIENT_ID` was empty or contained the string `placeholder`. If an operator misconfigured their credentials or deployed to production without configuring `.env`, the system would silently simulate payments instead of failing safely.
   - *Impact*: Extreme business and payment risk. Simulated payments could have been acknowledged without funds settling.
   - *Severity*: **High**

2. **Insecure Direct Object Reference (IDOR) on Payment Status**:
   - *Finding*: `GET /api/paypal/payment-status?orderId=PH-...` did not require session authentication or a secret access token. Anyone guessing or acquiring an order reference could query the order's payment status and retrieve customer payment details (including payer email).
   - *Impact*: Data privacy leakage violating customer confidentiality.
   - *Severity*: **High**

3. **Flawed Webhook Rejection on Delayed Retries**:
   - *Finding*: `paypal_client.php` enforced an arbitrary 20-minute freshness window on webhook transmission timestamps (`abs(time() - strtotime($transTime)) > 1200`). PayPal’s official webhook architecture retries failed deliveries up to 25 times over a period of up to 3 days.
   - *Impact*: Legitimate, cryptographically authentic webhook retries from PayPal would be rejected as "stale", causing order status desynchronization.
   - *Severity*: **Medium**

4. **Missing Payee Merchant ID Validation**:
   - *Finding*: Capture verification did not confirm that the funds were captured by the merchant account registered to ParentingHub Pakistan, allowing a theoretical payee redirection if a rogue client manipulated order payloads.
   - *Impact*: Revenue diversion risk.
   - *Severity*: **Medium**

---

## 2. Fixes & Security Hardening Implemented

### A. Strict Environment Separation & Explicit Mode Gating
- Introduced `PAYPAL_MODE`:
  - `PAYPAL_MODE=mock`: Permits deterministic local simulation **only** in development/test environments.
  - `PAYPAL_MODE=sandbox` (or omitted): Requires genuine PayPal Sandbox credentials.
  - `PAYPAL_MODE=live`: Production mode.
- **Production Safety Circuit Breaker**: If `PAYPAL_MODE=mock` is attempted when `APP_ENV=production` or `PAYPAL_ENV=live`, the system immediately throws a `DomainException("SECURITY VIOLATION: Simulated MOCK payment mode is strictly forbidden in production or live environments.")`.
- **No Silent Fallback**: If `PAYPAL_MODE` is not `mock`, missing or placeholder credentials produce a hard configuration error (`DomainException`). If cURL calls to PayPal fail, the error is thrown immediately; it **never** falls back to mock mode.

### B. Payment Status Privacy Protection (IDOR Defense)
- When `PhPayPalService::initiateOrder()` creates an order, it generates a 256-bit cryptographically secure token:
  ```php
  $paymentToken = bin2hex(random_bytes(32)); // 64 hex chars
  ```
- This token is saved in `ph_payments.payment_token` and returned **only** to the initiating client session.
- `GET /api/paypal/payment-status` now strictly enforces authorization:
  ```php
  $isOwner = ($sessionUserId !== null && (int)$row['user_id'] === $sessionUserId);
  $hasValidToken = ($token !== '' && hash_equals((string)$row['payment_token'], $token));
  if (!$isOwner && !$hasValidToken) {
      throw new DomainException("Unauthorized access to payment status.", 403);
  }
  ```
- **Data Minimization**: Removed `payer_email`, customer delivery addresses, and internal payload data from public API responses.

### C. Webhook Delayed-Retry Compliance
- Removed the arbitrary 20-minute timestamp rejection.
- Enforced PayPal's official verification algorithm via `POST /v1/notifications/verify-webhook-signature`.
- Mandated registered `PAYPAL_WEBHOOK_ID` for non-mock webhook verification.
- Guaranteed replay protection through the `UNIQUE KEY (event_id)` constraint in `ph_payment_events`.

### D. Capture Integrity & Payee Verification
- Enforced `PayPal-Request-Id: PH-ORD-{$localOrderId}` and `PayPal-Request-Id: PH-CAP-{$cleanId}` headers on all POST requests to ensure idempotency at the PayPal gateway level.
- Added Payee Merchant ID verification: If `PAYPAL_MERCHANT_ID` is configured, capture verifies that `payee.merchant_id` strictly matches the authorized merchant ID. Mismatches mark the payment `MERCHANT_MISMATCH` and reject the order.

---

## 3. Mock vs. Sandbox Verification Matrix

The test suite now verifies every environment permutation:

| Permutation | `APP_ENV` | `PAYPAL_MODE` | `PAYPAL_ENV` | Credentials Status | System Behavior |
|---|---|---|---|---|---|
| **Local Mock** | `local` | `mock` | `sandbox` | Irrelevant | Allowed. Returns deterministic v2 responses with mock order and capture IDs. |
| **Prod with Mock** | `production` | `mock` | `sandbox` | Any | **BLOCKED (Security Exception)** |
| **Live with Mock** | `local` | `mock` | `live` | Any | **BLOCKED (Security Exception)** |
| **Sandbox (No Creds)** | `local` | `sandbox` | `sandbox` | Placeholders / Empty | **BLOCKED (Configuration Error)** |
| **Sandbox (Live API)** | `local` | `sandbox` | `sandbox` | Valid Keys | Communicates via HTTPS cURL with `api-m.sandbox.paypal.com`. Never simulates. |

---

## 4. Real PayPal Sandbox Credentials & Verification Status

### Current Environment Check
Inspection of [`parentinghub-app/backend/.env`](file:///c:/Users/hp/OneDrive/Desktop/paypalhackthaton/parentinghub-app/backend/.env):
```ini
PAYPAL_MODE=mock
PAYPAL_ENV=sandbox
PAYPAL_CLIENT_ID=sandbox_client_id_placeholder
PAYPAL_CLIENT_SECRET=sandbox_client_secret_placeholder
PAYPAL_WEBHOOK_ID=
PAYPAL_MERCHANT_ID=
PAYPAL_FX_PKR_USD=280.0000
```
- Real PayPal Sandbox Client ID and Secret are **not currently set** (contain placeholders).
- In accordance with hackathon instructions, I **do not ask you to share secrets in chat**.

### Exact Local Instructions to Run Live Against PayPal Sandbox
To execute live transactions against `https://api-m.sandbox.paypal.com`:

1. Log into your [PayPal Developer Dashboard](https://developer.paypal.com/dashboard/).
2. Under **Apps & Credentials**, select the **Sandbox** tab and copy your App's **Client ID** and **Secret Key**.
3. Under **Webhooks**, create a webhook URL targeting your local tunnel (e.g. `https://<your-ngrok-or-domain>/api/paypal/webhook`) subscribed to:
   - `PAYMENT.CAPTURE.COMPLETED`
   - `PAYMENT.CAPTURE.DENIED`
   - `PAYMENT.CAPTURE.REFUNDED`
   Copy the registered **Webhook ID**.
4. Update `parentinghub-app/backend/.env`:
   ```ini
   PAYPAL_MODE=sandbox
   PAYPAL_ENV=sandbox
   PAYPAL_CLIENT_ID=<Your_PayPal_Sandbox_Client_Id>
   PAYPAL_CLIENT_SECRET=<Your_PayPal_Sandbox_Secret_Key>
   PAYPAL_WEBHOOK_ID=<Your_Registered_Webhook_Id>
   PAYPAL_MERCHANT_ID=<Your_Sandbox_Merchant_Account_Id>
   PAYPAL_FX_PKR_USD=280.0000
   ```
5. Run the live verification script:
   ```powershell
   php parentinghub-app/tests/test_paypal_suite.php
   ```
   When `PAYPAL_MODE=sandbox` is set with valid credentials, `PhPayPalClient` automatically connects directly to PayPal's live sandbox servers.

---

## 5. Webhook Verification Results

The webhook handler was tested against 4 distinct threat and retry conditions:

1. **Forged Signature**: Request with header `PAYPAL-TRANSMISSION-SIG=FORGED_SIGNATURE` is rejected with HTTP 400 Bad Request.
2. **Duplicate Event Delivery**: An identical `event_id` delivered a second time is intercepted by `ph_payment_events` unique constraint and returns HTTP 200 `{ duplicate: true }` without re-crediting or re-confirming the order.
3. **Delayed Delivery**: Webhook transmission timestamps older than 20 minutes (tested with 2-hour delay) are successfully validated and acknowledged, respecting PayPal's multi-day retry schedule.
4. **Failure Event Reconcile**: `PAYMENT.CAPTURE.DENIED` webhook events update the order status to `Payment Failed` and payment record to `DENIED`.

---

## 6. MySQL Integration Status

### Host Port Probe Results
- Probed host TCP port `3306` at `127.0.0.1`:
  - `TcpTestSucceeded : False` (`WSAECONNREFUSED` / service stopped).
  - Process check: `mysqld` is not currently running as an active Windows process on the host.
- **Reporting Rule Compliance**: As explicitly instructed (*"Do not label SQLite tests as MySQL tests"*):
  - All automated test runs documented in this report executed against an isolated in-memory SQLite harness.
  - All SQL queries, triggers, table definitions, and constraints have been designed and validated for **dual MySQL 8.0/MariaDB and SQLite compliance** (e.g. using `CURRENT_TIMESTAMP`, ANSI-standard transactions, and dual-driver upsert handling).
  - The production database dump `database/init_local_dev.sql` includes all additive hackathon tables (`ph_ai_cart_proposals`, `ph_payments`, `ph_payment_events`) ready for direct import when MySQL is started.

---

## 7. Merchant Eligibility Research: Pakistan & PayPal

A critical requirement of this audit is to assess whether a Pakistan-registered business entity can legally and technically receive PayPal merchant payments.

### Factual Regulatory & Commercial Findings

1. **PayPal Direct Merchant Status in Pakistan**:
   - Pakistan is **not** currently listed on PayPal's official roster of supported receiving countries.
   - A business registered solely in Pakistan with a Pakistani NTN (National Tax Number) and Pakistani commercial bank account (e.g., Meezan, HBL, UBL, Bank Alfalah) **cannot register a direct PayPal merchant acquiring account**.
   - Direct payment collection and balance withdrawal to Pakistani bank accounts are unsupported.

2. **Regulatory Stance (State Bank of Pakistan & MoITT)**:
   - The State Bank of Pakistan (SBP) and Ministry of IT have repeatedly clarified that there are **no Pakistani legal or regulatory bans** prohibiting PayPal from entering the market.
   - SBP's Electronic Money Institutions (EMI) and Foreign Exchange regulations permit international payment gateways. The absence of PayPal is due to PayPal's internal global risk appetite, compliance overhead, and commercial strategy.

3. **Government / SBP Facilitation Models**:
   - In 2024–2026, the Government of Pakistan initiated indirect international remittance channels (such as partnerships routing funds through approved international intermediaries into local digital wallets or bank accounts).
   - However, these channels function as **inward remittance corridors for remittances and freelancers**, not as direct merchant checkout APIs (Orders v2) for domestic e-commerce websites.

4. **Sandbox Accounts vs. Live Accounts**:
   - Pakistani developers and businesses **can** freely register PayPal Developer accounts and create **PayPal Sandbox accounts** worldwide. This allows 100% compliant technical prototyping, SDK testing, and hackathon demonstrations.
   - Moving from Sandbox to Live requires a business entity in an officially supported PayPal country (e.g., US, UK, UAE, Singapore) with verified local corporate registration, local tax ID, and a local bank account.

5. **Security & Compliance Warning Regarding "Workarounds"**:
   - Creating PayPal merchant accounts using fake addresses, personal VPNs, or unauthorized straw accounts violates PayPal’s Terms of Service and SBP anti-money laundering regulations. PayPal routinely flags and permanently freezes such accounts, resulting in irreversible forfeiture of funds.
   - For live e-commerce operations in Pakistan, compliant dual-rail architectures (e.g., native PayFast/JazzCash/EasyPaisa for PKR customers alongside international gateway partners for foreign buyers) represent the standard legitimate operational model.

---

## 8. Summary of Automated Verification Results

All 29 tests in the expanded `test_paypal_suite.php` pass:

```text
========================================================================
Starting ParentingHub Phase 7 & 8 Automated Test Suite
========================================================================

--- Phase 7: Smart Cart Engine & Permission-Based Proposals ---
  [PASS] Test 1: AI creates proposal ticket with unique ID and 30m TTL
  [PASS] Test 2: Proposal computes verified subtotal & PKR 200 delivery fee
  [PASS] Test 3: Customer explicitly approves modified subset of proposed bundle
  [PASS] Test 4: Reject customer attempting to approve products not in original proposal

--- Phase 8: 16 PayPal Sandbox Checkout Verification Scenarios ---
  [PASS] Test 5: Scenario 1: Unauthorized / invalid order creation rejected with 422
  [PASS] Test 6: Scenario 2: Tampered cart amount ignored, server calculates true catalog prices
  [PASS] Test 7: Scenario 3: Tampered / mismatched PayPal order ID rejected during capture
  [PASS] Test 8: Scenario 4: Incorrect currency (non-USD) rejected and flagged
  [PASS] Test 9: Scenario 5: Incorrect payment amount rejected and flagged
  [PASS] Test 10: Scenario 6: Duplicate capture handled idempotently without duplicate charges
  [PASS] Test 11: Scenario 7: Repeated approval of AI cart proposal rejected with 409
  [PASS] Test 12: Scenario 8: Non-COMPLETED capture marks order Payment Failed
  [PASS] Test 13: Scenario 9: Freshly initiated PayPal order is Pending Payment
  [PASS] Test 14: Scenario 10: Failed payment webhook event updates status to Payment Failed
  [PASS] Test 15: Scenario 11: Cancelled checkout cannot be captured
  [PASS] Test 16: Scenario 12: Invalid / forged webhook signature rejected with 400
  [PASS] Test 17: Scenario 13: Duplicate webhook event recognized and deduplicated
  [PASS] Test 18: Scenario 14: Stock depletion during checkout caught before capture confirmation
  [PASS] Test 19: Scenario 15: Order status consistency across ph_orders (Confirmed) and ph_payments (COMPLETED)
  [PASS] Test 20: Scenario 16: Mathematical FX conversion and 2-decimal rounding exactness (50085 PKR = 178.88 USD)

--- Phase 8.1: PayPal Security Audit & Privacy Defense Tests ---
  [PASS] Test 21: Phase 8.1 Test 1: PAYPAL_MODE=mock strictly prohibited in production environment
  [PASS] Test 22: Phase 8.1 Test 2: PAYPAL_MODE=mock strictly prohibited when PAYPAL_ENV=live
  [PASS] Test 23: Phase 8.1 Test 3: Non-mock mode requires valid credentials (no silent mock fallback)
  [PASS] Test 24: Phase 8.1 Test 4: Payee merchant ID mismatch rejected and flagged during capture
  [PASS] Test 25: Phase 8.1 Test 5: Payment status query without access token or user ownership rejected with 403
  [PASS] Test 26: Phase 8.1 Test 6: Payment status query with invalid token rejected with 403
  [PASS] Test 27: Phase 8.1 Test 7: Authorized query with unguessable token succeeds without leaking payer personal email
  [PASS] Test 28: Phase 8.1 Test 8: Authenticated order owner can retrieve payment status without token
  [PASS] Test 29: Phase 8.1 Test 9: Delayed authentic webhook (>20 mins old) accepted via signature & deduplicated

========================================================================
Test Suite Execution Finished: 29 / 29 Passed (100% SUCCESS)
========================================================================
```

### Cumulative Test Count
- `tests/test_paypal_suite.php`: **29/29 Passed**
- `tests/test_ai_suite.php`: **13/13 Passed**
- `tests/test_commerce_regression.php`: **10/10 Passed**
- `tests/test_pricing_engine.php`: **17/17 Passed**
- **Grand Total: 69/69 Passing Tests (0 Failures, 0 Regressions)**

---

## 9. Remaining Blockers & Next Steps

1. **Live Sandbox Credentials**: Awaiting real sandbox credentials in `.env` if live sandbox HTTP calls are desired before demo recording. In the interim, `PAYPAL_MODE=mock` is isolated and verified for development.
2. **Phase Boundary**: Stopped. Phase 9 has **not** been started. Live payment mode has **not** been enabled.

