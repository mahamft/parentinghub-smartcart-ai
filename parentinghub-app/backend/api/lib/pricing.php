<?php
declare(strict_types=1);

/**
 * ParentingHub Shared Pricing, Cart Calculation & Validation Engine
 * 
 * Central source of truth for:
 * 1. Product price & option verification against database
 * 2. In-stock validation
 * 3. Coupon code validation & discount calculation
 * 4. Fixed delivery fee calculation (PKR 200 per non-empty order)
 * 5. High-entropy order ID generation
 * 6. Dual-currency FX conversion (PKR to USD for PayPal)
 */

if (!defined('PH_ENTRY') && !defined('PH_TEST_SUITE')) {
    // Direct access protection
}

/**
 * Generate a cryptographically secure, high-entropy ParentingHub Order ID.
 * Replaces the previous client-side "PH-" + 5-digit number (which only had 90,000 combinations).
 * Format: PH-{Ymd}-{HEX8} (e.g. PH-20261103-9A4F8B2C)
 */
function ph_generate_order_id(): string {
    $datePart = gmdate('Ymd');
    $randPart = strtoupper(bin2hex(random_bytes(4)));
    return "PH-{$datePart}-{$randPart}";
}

/**
 * Standard fixed shipping fee across Pakistan: PKR 200 for non-empty carts.
 */
if (!function_exists('ph_fixed_shipping_fee')) {
    function ph_fixed_shipping_fee(float $subtotal): float {
        return $subtotal > 0 ? 200.0 : 0.0;
    }
}

/**
 * Get current configured USD/PKR FX exchange rate for PayPal conversion.
 * Default: 1 USD = 280 PKR. Configurable via PAYPAL_FX_PKR_USD in server .env.
 */
function ph_paypal_fx_rate(): float {
    $envRate = ph_env('PAYPAL_FX_PKR_USD', '280.0000');
    $rate = (float)$envRate;
    return $rate > 0 ? $rate : 280.0;
}

/**
 * Convert PKR to USD with 2-decimal ceiling/standard rounding for payment processors.
 */
function ph_pkr_to_usd(float $amountPkr, ?float $fxRate = null): float {
    $rate = $fxRate ?? ph_paypal_fx_rate();
    if ($amountPkr <= 0 || $rate <= 0) return 0.00;
    return round($amountPkr / $rate, 2);
}

/**
 * Authoritative cart calculation & server-side validation.
 * 
 * @param PDO $pdo Active database connection
 * @param array $requestedItems Array of [{id: string, qty: int, option?: string}]
 * @param string|null $couponCode Optional promo code
 * @return array Standardized result dictionary or throws InvalidArgumentException / DomainException
 */
function ph_calculate_cart_totals(PDO $pdo, array $requestedItems, ?string $couponCode = null): array {
    if (empty($requestedItems) || !is_array($requestedItems)) {
        throw new InvalidArgumentException('Shopping bag is empty.');
    }

    if (count($requestedItems) > 50) {
        throw new InvalidArgumentException('Maximum 50 items allowed per order.');
    }

    // 1. Collect and validate unique product IDs
    $requestedProductIds = [];
    foreach ($requestedItems as $requested) {
        if (!is_array($requested)) {
            throw new InvalidArgumentException('Invalid cart item format.');
        }
        $pid = trim((string)($requested['product']['id'] ?? $requested['id'] ?? ''));
        if ($pid === '' || !preg_match('/^[A-Za-z0-9_-]{1,190}$/', $pid)) {
            throw new InvalidArgumentException('One or more cart items are invalid.');
        }
        $requestedProductIds[$pid] = true;
    }

    $ids = array_keys($requestedProductIds);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $pst = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $pst->execute($ids);
    $rows = $pst->fetchAll();

    $productMap = [];
    foreach ($rows as $r) {
        $arr = static function($v): array {
            $d = is_string($v) ? json_decode($v, true) : $v;
            return is_array($d) ? array_values($d) : [];
        };
        $images = $arr($r['images'] ?? '[]');
        $image = trim((string)($r['image'] ?? ''));
        if ($image !== '' && !in_array($image, $images, true)) {
            array_unshift($images, $image);
        }
        $productMap[(string)$r['id']] = [
            'id' => (string)$r['id'],
            'sku' => (string)($r['sku'] ?? ''),
            'name' => (string)$r['name'],
            'price' => (float)($r['price'] ?? 0),
            'compareAt' => isset($r['compare_at']) && $r['compare_at'] !== null ? (float)$r['compare_at'] : null,
            'image' => $image,
            'img' => $image,
            'images' => $images,
            'category' => (string)($r['category'] ?? ''),
            'cat' => (string)($r['category'] ?? ''),
            'brand' => (string)($r['brand'] ?? ''),
            'description' => (string)($r['description'] ?? ''),
            'desc' => (string)($r['description'] ?? ''),
            'hasOptions' => (bool)($r['has_options'] ?? false),
            'options' => $arr($r['options_json'] ?? '[]'),
            'rating' => (float)($r['rating'] ?? 0),
            'reviews' => (int)($r['reviews'] ?? 0),
            'inStock' => (bool)($r['in_stock'] ?? true),
            'weightKg' => isset($r['weight_kg']) && $r['weight_kg'] !== null ? (float)$r['weight_kg'] : null,
            'tags' => $arr($r['tags'] ?? '[]'),
            'sourceUrl' => (string)($r['source_url'] ?? ''),
            'video' => (string)($r['video_url'] ?? '')
        ];
    }

    if (count($productMap) !== count($ids)) {
        throw new DomainException('One or more selected products are no longer available in the catalog.');
    }

    // 2. Validate line items, stock & option constraints
    $items = [];
    $subtotal = 0.0;
    foreach ($requestedItems as $requested) {
        $productId = (string)($requested['product']['id'] ?? $requested['id'] ?? '');
        $qty = (int)($requested['qty'] ?? 0);
        if ($productId === '' || $qty < 1 || $qty > 99 || !isset($productMap[$productId])) {
            throw new InvalidArgumentException('Invalid item quantity or product reference.');
        }

        $product = $productMap[$productId];
        if (empty($product['inStock'])) {
            throw new DomainException("Product '{$product['name']}' is currently out of stock.");
        }

        $price = max(0.0, (float)($product['price'] ?? 0));
        $lineTotal = $price * $qty;
        $subtotal += $lineTotal;

        $item = [
            'id' => $product['id'],
            'name' => $product['name'],
            'sku' => $product['sku'] ?? '',
            'price' => $price,
            'product' => $product,
            'qty' => $qty,
            'lineTotal' => round($lineTotal, 2)
        ];

        // Validate selectable options against server options_json
        $definedOptions = is_array($product['options'] ?? null) ? $product['options'] : [];
        $definedOptions = array_values(array_filter($definedOptions, static function($g) {
            return is_array($g) && trim((string)($g['name'] ?? '')) !== '' && is_array($g['values'] ?? null) && count($g['values']) > 0;
        }));

        if (count($definedOptions) > 0) {
            $requestedOption = is_string($requested['option'] ?? null) ? trim((string)$requested['option']) : '';
            if ($requestedOption === '') {
                throw new InvalidArgumentException("Please select all required options for '{$product['name']}'.");
            }
            $selected = [];
            foreach (explode(' / ', $requestedOption) as $part) {
                $pos = strpos($part, ':');
                if ($pos === false) continue;
                $name = trim(substr($part, 0, $pos));
                $value = trim(substr($part, $pos + 1));
                if ($name !== '' && $value !== '') $selected[$name] = $value;
            }
            $normalized = [];
            foreach ($definedOptions as $group) {
                $name = trim((string)$group['name']);
                $allowed = array_values(array_filter(array_map(static fn($v) => trim((string)$v), $group['values']), static fn($v) => $v !== ''));
                $value = (string)($selected[$name] ?? '');
                if ($value === '' || !in_array($value, $allowed, true)) {
                    throw new InvalidArgumentException("Invalid option selected for '{$product['name']}': {$name}.");
                }
                $normalized[] = $name . ': ' . $value;
            }
            $item['option'] = mb_substr(implode(' / ', $normalized), 0, 240);
        }

        $items[] = $item;
    }

    $subtotal = round($subtotal, 2);

    // 3. Delivery fee: fixed PKR 200 per order across Pakistan
    $shipping = ph_fixed_shipping_fee((float)$subtotal);

    // 4. Coupon discount calculation
    $discount = 0.0;
    $appliedCoupon = null;
    $cleanCouponCode = $couponCode !== null ? strtoupper(trim($couponCode)) : '';

    if ($cleanCouponCode !== '') {
        $catSt = $pdo->prepare("SELECT payload FROM ph_catalog_state WHERE state_key='catalog' LIMIT 1");
        $catSt->execute();
        $catRow = $catSt->fetch();
        $catalog = $catRow ? json_decode((string)$catRow['payload'], true) : [];
        if (!is_array($catalog)) $catalog = [];

        foreach (($catalog['coupons'] ?? []) as $coupon) {
            if (!is_array($coupon) || strtoupper((string)($coupon['code'] ?? '')) !== $cleanCouponCode) continue;
            if (empty($coupon['active'])) break;
            $expiry = (string)($coupon['expiryDate'] ?? '');
            if ($expiry !== '' && strtotime($expiry . ' 23:59:59') !== false && strtotime($expiry . ' 23:59:59') < time()) break;
            $minOrder = max(0.0, (float)($coupon['minOrder'] ?? 0));
            if ($subtotal < $minOrder) break;
            $value = max(0.0, (float)($coupon['value'] ?? 0));
            $discount = (($coupon['type'] ?? '') === 'percent') ? ($subtotal * min(100.0, $value) / 100.0) : min($subtotal, $value);
            $discount = round($discount, 2);
            $appliedCoupon = $cleanCouponCode;
            break;
        }

        if ($appliedCoupon === null) {
            throw new InvalidArgumentException('Coupon is invalid, inactive or does not meet the minimum order requirement.');
        }
    }

    $total = round(max(0.0, $subtotal - $discount) + $shipping, 2);
    $fxRate = ph_paypal_fx_rate();
    $totalUsd = ph_pkr_to_usd($total, $fxRate);

    return [
        'items' => $items,
        'itemCount' => count($items),
        'totalQuantity' => array_sum(array_column($items, 'qty')),
        'subtotal' => $subtotal,
        'shipping' => $shipping,
        'discount' => $discount,
        'total' => $total,
        'coupon' => $appliedCoupon,
        'currency' => 'PKR',
        'fxRate' => $fxRate,
        'totalUsd' => $totalUsd
    ];
}

