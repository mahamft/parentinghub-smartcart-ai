# 🎥 ParentingHub SmartCart AI: 2-Minute Demo Video Script

**Target Duration**: 2 minutes (120 seconds)  
**Resolution**: 1080p Full HD  
**Audio**: Clear voiceover (English or Urdu)

---

### ⏱️ Timeline & Scene Breakdown

#### Scene 1: Introduction & The Problem (0:00 - 0:25)
- **Visual**: Show the ParentingHub Storefront homepage (`http://127.0.0.1:8080/`), scrolling past baby essentials.
- **Voiceover**:
  > *"First-time parents often face overwhelming anxiety late at night when their infant cries from colic or feeding issues. Standard e-commerce stores only display endless product grids without any pediatric guidance, while chatbots only offer walls of generic text. This is ParentingHub SmartCart AI—the first intelligent parenting advisor that combines clinical pediatric guidance with transactional dual-currency PayPal checkout."*

#### Scene 2: SmartCart AI in Roman Urdu / English (0:25 - 0:50)
- **Visual**: Click on the pink **SmartCart AI Parenting Advisor** button in the bottom right corner. Type in Roman Urdu:
  `mera 2 month ka baby hai doodh peete hue rota hai gas ki wajah se`
  and hit send.
- **Voiceover**:
  > *"Parents can ask questions in their natural everyday language—like Roman Urdu. Powered by Google Gemini and a Qdrant Vector Database with 317 clinical knowledge chunks, SmartCart AI understands the developmental stage and retrieves anti-colic feeding bottles with AirFree vents, complete with flow recommendations and a verified pediatric disclaimer."*

#### Scene 3: Server-Enforced AI Cart Proposal (0:50 - 1:15)
- **Visual**: The chat shows the structured proposal card. Click **"Approve & Add to Bag"**. The button turns green (*"✓ Approved & In Your Shopping Bag!"*). Then click **"Proceed to Checkout"**.
- **Voiceover**:
  > *"To ensure safety, the AI acts as a read-only advisor. It generates a structured proposal that the parent must explicitly approve before any cart mutation occurs. Once approved, clicking Proceed to Checkout takes us directly to our secure checkout page."*

#### Scene 4: Dual-Currency PayPal Orders v2 Checkout (1:15 - 1:45)
- **Visual**: On the `/checkout` page, select **PayPal Sandbox (USD / Cards)**. The USD total (`$9.40 USD` evaluated at fixed 280 PKR/USD) and official PayPal button appear. Click the PayPal button to launch the PayPal Sandbox popup. Complete the payment.
- **Voiceover**:
  > *"Our checkout engine seamlessly converts PKR catalog pricing to USD for global compliance via PayPal Orders v2 API. In the backend, MariaDB InnoDB row locks atomically reserve the stock with a 15-minute TTL to prevent overselling. Once payment is authorized, the order confirms instantly with an immutable transaction receipt."*

#### Scene 5: 100% Verified Architecture & Wrap-Up (1:45 - 2:00)
- **Visual**: Show the terminal running the test suite: `php parentinghub-app/tests/run_all_tests.php` displaying **23/23 Suites Passed (100%)**.
- **Voiceover**:
  > *"Backed by 23 passing regression suites covering unit pricing, vector search, and PayPal idempotency, ParentingHub SmartCart AI bridges clinical guidance with frictionless commerce. Thank you!"*

