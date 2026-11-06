<?php
declare(strict_types=1);

/**
 * ParentingHub Official PayPal Orders v2 REST API Client
 * 
 * Strict Environment Separation:
 * 1. PAYPAL_MODE=mock: Local development simulation ONLY (strictly blocked in production/live).
 * 2. PAYPAL_MODE=sandbox (default): Real HTTPS cURL requests to https://api-m.sandbox.paypal.com.
 *    Requires genuine PAYPAL_CLIENT_ID and PAYPAL_CLIENT_SECRET.
 *    Failed API calls NEVER fall back to simulated/mock payments.
 * 3. PAYPAL_MODE=live: Real HTTPS cURL requests to https://api-m.paypal.com.
 * 
 * PayPal Orders v2 Lifecycle & Expiration Notes:
 * - Orders created via POST /v2/checkout/orders start in CREATED status.
 * - Documented default CREATED-state lifetime is 3 hours if unapproved by the customer.
 * - An extended lifetime of up to 72 hours requires an eligible account/integration extension.
 * - PayPal Orders v2 provides no DELETE or CANCEL endpoint for unapproved CREATED orders.
 * - ParentingHub blocks orphan capture locally by marking orders 'Reservation Failed' / 'CANCELLED'.
 */

if (!defined('PH_ENTRY') && !defined('PH_TEST_SUITE')) {
    http_response_code(404);
    exit;
}

class PhPayPalClient {

    private string $mode = 'mock';
    private string $env = 'sandbox';
    private string $clientId = '';
    private string $clientSecret = '';
    private string $webhookId = '';
    private string $merchantId = '';
    private float $fxRate = 279.0;
    private bool $mockMode = true;
    private string $apiBaseUrl = '';
    private static ?string $cachedToken = null;
    private static int $tokenExpiresAt = 0;
    private static array $mockOrderStore = [];

    public static function setMockOrder(string $paypalOrderId, array $orderData): void {
        self::$mockOrderStore[trim($paypalOrderId)] = $orderData;
    }

    public static function getMockOrder(string $paypalOrderId): ?array {
        return self::$mockOrderStore[trim($paypalOrderId)] ?? null;
    }

    public static function resetMockStore(): void {
        self::$mockOrderStore = [];
    }

    public function __construct(?array $overrideConfig = null) {
        $appEnv = strtolower(trim((string)($overrideConfig['APP_ENV'] ?? ph_env('APP_ENV', 'local'))));
        $this->mode = strtolower(trim((string)($overrideConfig['PAYPAL_MODE'] ?? ph_env('PAYPAL_MODE', 'sandbox'))));
        $this->env = strtolower(trim((string)($overrideConfig['PAYPAL_ENV'] ?? ph_env('PAYPAL_ENV', 'sandbox'))));
        $this->clientId = trim((string)($overrideConfig['PAYPAL_CLIENT_ID'] ?? ph_env('PAYPAL_CLIENT_ID', '')));
        $this->clientSecret = trim((string)($overrideConfig['PAYPAL_CLIENT_SECRET'] ?? ph_env('PAYPAL_CLIENT_SECRET', '')));
        $this->webhookId = trim((string)($overrideConfig['PAYPAL_WEBHOOK_ID'] ?? ph_env('PAYPAL_WEBHOOK_ID', '')));
        $this->merchantId = trim((string)($overrideConfig['PAYPAL_MERCHANT_ID'] ?? ph_env('PAYPAL_MERCHANT_ID', '')));
        
        $fx = (float)($overrideConfig['PAYPAL_FX_PKR_USD'] ?? ph_env('PAYPAL_FX_PKR_USD', '280.0000'));
        $this->fxRate = $fx > 0 ? $fx : 280.0;

        // Security Rule 1: MOCK mode is strictly prohibited in production or live environments
        if ($this->mode === 'mock') {
            if ($appEnv === 'production' || $this->env === 'live') {
                throw new DomainException("SECURITY VIOLATION: Simulated MOCK payment mode is strictly forbidden in production or live environments.");
            }
            $this->mockMode = true;
        } else {
            $this->mockMode = false;
        }

        // Security Rule 2: In Sandbox or Live mode, credentials MUST be provided and valid
        if (!$this->mockMode) {
            if ($this->env !== 'sandbox' && $this->env !== 'live') {
                throw new InvalidArgumentException("Invalid PAYPAL_ENV '{$this->env}'. Allowed values are 'sandbox' or 'live'.");
            }
            if (empty($this->clientId) || empty($this->clientSecret) 
                || str_contains(strtolower($this->clientId), 'placeholder') 
                || str_contains(strtolower($this->clientSecret), 'placeholder')) {
                throw new DomainException("PayPal configuration error: Valid PAYPAL_CLIENT_ID and PAYPAL_CLIENT_SECRET are required when PAYPAL_MODE is '{$this->mode}'. Simulated fallback is not permitted.");
            }
        }

        $this->apiBaseUrl = ($this->env === 'live')
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';
    }

    public function isMockMode(): bool {
        return $this->mockMode;
    }

    public function getMode(): string {
        return $this->mode;
    }

    public function getClientId(): string {
        return $this->clientId;
    }

    public function getMerchantId(): string {
        return $this->merchantId;
    }

    public function getFxRate(): float {
        return $this->fxRate;
    }

    public function getEnv(): string {
        return $this->env;
    }

    /**
     * Retrieve OAuth2 Bearer Access Token using HTTP Basic Auth (POST /v1/oauth2/token)
     */
    public function getAccessToken(): string {
        if ($this->mockMode) {
            return 'A21AA-MOCK-SANDBOX-TOKEN-' . strtoupper(bin2hex(random_bytes(16)));
        }

        if (self::$cachedToken !== null && time() < (self::$tokenExpiresAt - 60)) {
            return self::$cachedToken;
        }

        $url = $this->apiBaseUrl . '/v1/oauth2/token';
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => 'grant_type=client_credentials',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_USERPWD => $this->clientId . ':' . $this->clientSecret,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Accept-Language: en_US',
                'Content-Type: application/x-www-form-urlencoded'
            ],
            CURLOPT_SSL_VERIFYPEER => true
        ]);

        $res = curl_exec($ch);
        $err = curl_error($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($err !== '' || $code !== 200 || !is_string($res)) {
            throw new RuntimeException("PayPal OAuth authentication failed (HTTP {$code}): {$err} {$res}");
        }

        $data = json_decode($res, true);
        if (!isset($data['access_token'])) {
            throw new RuntimeException("PayPal OAuth response did not contain access_token.");
        }

        self::$cachedToken = (string)$data['access_token'];
        self::$tokenExpiresAt = time() + (int)($data['expires_in'] ?? 3600);

        return self::$cachedToken;
    }

    /**
     * Create PayPal Order v2 (POST /v2/checkout/orders)
     * 
     * @param string $localOrderId Local order reference (e.g. PH-20261103-9A4F8B2C)
     * @param float $totalUsd Total order amount in USD
     * @param float $subtotalUsd Subtotal items in USD
     * @param float $shippingUsd Shipping fee in USD
     * @param array $items Array of items with name, unitPriceUsd, qty
     * @return array Standardized PayPal order response
     */
    public function createOrder(
        string $localOrderId,
        float $totalUsd,
        float $subtotalUsd,
        float $shippingUsd,
        array $items = []
    ): array {
        $formattedTotal = number_format($totalUsd, 2, '.', '');
        $formattedSubtotal = number_format($subtotalUsd, 2, '.', '');
        $formattedShipping = number_format($shippingUsd, 2, '.', '');

        if ($this->mockMode) {
            $paypalOrderId = '8XY' . strtoupper(bin2hex(random_bytes(7)));
            $merchantId = $this->merchantId !== '' ? $this->merchantId : 'MOCK-MERCHANT-01';
            $mockOrder = [
                'id' => $paypalOrderId,
                'status' => 'APPROVED',
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'reference_id' => $localOrderId,
                        'custom_id' => $localOrderId,
                        'description' => "ParentingHub Verified Essentials Order {$localOrderId}",
                        'payee' => [
                            'merchant_id' => $merchantId,
                            'email_address' => 'merchant@parentinghub.pk'
                        ],
                        'amount' => [
                            'currency_code' => 'USD',
                            'value' => $formattedTotal,
                            'breakdown' => [
                                'item_total' => ['currency_code' => 'USD', 'value' => $formattedSubtotal],
                                'shipping' => ['currency_code' => 'USD', 'value' => $formattedShipping]
                            ]
                        ]
                    ]
                ],
                'payer' => [
                    'name' => [
                        'given_name' => 'ParentingHub',
                        'surname' => 'Shopper'
                    ],
                    'email_address' => 'sb-shopper@parentinghub.pk',
                    'payer_id' => 'SANDBOX-PAYER-01'
                ],
                'links' => [
                    [
                        'href' => "https://www.sandbox.paypal.com/checkoutnow?token={$paypalOrderId}",
                        'rel' => 'approve',
                        'method' => 'GET'
                    ],
                    [
                        'href' => "{$this->apiBaseUrl}/v2/checkout/orders/{$paypalOrderId}/capture",
                        'rel' => 'capture',
                        'method' => 'POST'
                    ]
                ],
                'isMock' => true
            ];
            self::$mockOrderStore[$paypalOrderId] = $mockOrder;
            return $mockOrder;
        }

        $token = $this->getAccessToken();

        $purchaseUnitItems = [];
        foreach ($items as $it) {
            $unitVal = number_format((float)($it['unitPriceUsd'] ?? 0), 2, '.', '');
            $qty = (int)($it['qty'] ?? 1);
            $purchaseUnitItems[] = [
                'name' => mb_substr((string)($it['name'] ?? 'Product'), 0, 127),
                'quantity' => (string)$qty,
                'unit_amount' => [
                    'currency_code' => 'USD',
                    'value' => $unitVal
                ],
                'category' => 'PHYSICAL_GOODS'
            ];
        }

        $payload = [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'reference_id' => $localOrderId,
                    'custom_id' => $localOrderId,
                    'description' => "ParentingHub Order {$localOrderId}",
                    'amount' => [
                        'currency_code' => 'USD',
                        'value' => $formattedTotal,
                        'breakdown' => [
                            'item_total' => ['currency_code' => 'USD', 'value' => $formattedSubtotal],
                            'shipping' => ['currency_code' => 'USD', 'value' => $formattedShipping]
                        ]
                    ]
                ]
            ],
            'application_context' => [
                'brand_name' => 'ParentingHub Pakistan',
                'landing_page' => 'NO_PREFERENCE',
                'shipping_preference' => 'NO_SHIPPING',
                'user_action' => 'PAY_NOW'
            ]
        ];

        if (!empty($purchaseUnitItems)) {
            $payload['purchase_units'][0]['items'] = $purchaseUnitItems;
        }

        $url = $this->apiBaseUrl . '/v2/checkout/orders';
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_SLASHES),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token,
                'PayPal-Request-Id: PH-ORD-' . $localOrderId
            ],
            CURLOPT_SSL_VERIFYPEER => true
        ]);

        $res = curl_exec($ch);
        $err = curl_error($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($err !== '' || ($code !== 200 && $code !== 201) || !is_string($res)) {
            throw new RuntimeException("PayPal Order creation failed (HTTP {$code}): {$err} {$res}");
        }

        $result = json_decode($res, true);
        if (!isset($result['id'])) {
            throw new RuntimeException("Invalid PayPal Order response structure.");
        }

        return $result;
    }

    /**
     * Capture Payment for an authorized PayPal Order (POST /v2/checkout/orders/{id}/capture)
     */
    public function captureOrder(string $paypalOrderId, ?string $mockExpectedAmount = null): array {
        $cleanId = trim($paypalOrderId);
        if ($cleanId === '') {
            throw new InvalidArgumentException("PayPal Order ID is required for capture.");
        }

        if ($this->mockMode) {
            if (isset(self::$mockOrderStore[$cleanId]) && strtoupper((string)(self::$mockOrderStore[$cleanId]['status'] ?? '')) === 'COMPLETED') {
                throw new DomainException("PayPal Order capture failed (HTTP 422): ORDER_ALREADY_CAPTURED - The order {$cleanId} has already been captured.");
            }

            $captureId = 'CAPTURE-' . strtoupper(bin2hex(random_bytes(8)));
            $amountVal = $mockExpectedAmount !== null ? number_format((float)$mockExpectedAmount, 2, '.', '') : '10.00';
            $merchantId = $this->merchantId !== '' ? $this->merchantId : 'MOCK-MERCHANT-01';
            $captureResult = [
                'id' => $cleanId,
                'status' => 'COMPLETED',
                'payment_source' => [
                    'paypal' => [
                        'email_address' => 'sb-shopper@parentinghub.pk',
                        'account_id' => 'SANDBOX-ACC-01',
                        'name' => [
                            'given_name' => 'ParentingHub',
                            'surname' => 'Shopper'
                        ]
                    ]
                ],
                'purchase_units' => [
                    [
                        'reference_id' => 'default',
                        'payee' => [
                            'merchant_id' => $merchantId,
                            'email_address' => 'merchant@parentinghub.pk'
                        ],
                        'payments' => [
                            'captures' => [
                                [
                                    'id' => $captureId,
                                    'status' => 'COMPLETED',
                                    'amount' => [
                                        'currency_code' => 'USD',
                                        'value' => $amountVal
                                    ],
                                    'final_capture' => true,
                                    'seller_protection' => [
                                        'status' => 'ELIGIBLE'
                                    ],
                                    'create_time' => gmdate('Y-m-d\TH:i:s\Z'),
                                    'update_time' => gmdate('Y-m-d\TH:i:s\Z')
                                ]
                            ]
                        ]
                    ]
                ],
                'payer' => [
                    'name' => [
                        'given_name' => 'ParentingHub',
                        'surname' => 'Shopper'
                    ],
                    'email_address' => 'sb-shopper@parentinghub.pk',
                    'payer_id' => 'SANDBOX-PAYER-01'
                ],
                'isMock' => true
            ];

            self::$mockOrderStore[$cleanId] = $captureResult;
            return $captureResult;
        }

        $token = $this->getAccessToken();
        $url = "{$this->apiBaseUrl}/v2/checkout/orders/{$cleanId}/capture";

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => '{}',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 25,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token,
                'PayPal-Request-Id: PH-CAP-' . $cleanId
            ],
            CURLOPT_SSL_VERIFYPEER => true
        ]);

        $res = curl_exec($ch);
        $err = curl_error($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($err !== '' || ($code !== 200 && $code !== 201) || !is_string($res)) {
            $parsed = is_string($res) ? json_decode($res, true) : null;
            $msg = $parsed['message'] ?? $parsed['name'] ?? $err;
            throw new DomainException("PayPal Order capture failed (HTTP {$code}): {$msg}");
        }

        $data = json_decode($res, true);
        if (!isset($data['status'])) {
            throw new RuntimeException("Invalid capture response structure from PayPal.");
        }

        return $data;
    }

    /**
     * Retrieve Order Details (GET /v2/checkout/orders/{id})
     */
    public function getOrder(string $paypalOrderId): array {
        $cleanId = trim($paypalOrderId);
        if ($this->mockMode) {
            if (isset(self::$mockOrderStore[$cleanId])) {
                return self::$mockOrderStore[$cleanId];
            }
            return [
                'id' => $cleanId,
                'status' => 'APPROVED',
                'intent' => 'CAPTURE',
                'isMock' => true
            ];
        }

        $token = $this->getAccessToken();
        $url = "{$this->apiBaseUrl}/v2/checkout/orders/{$cleanId}";

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token
            ],
            CURLOPT_SSL_VERIFYPEER => true
        ]);

        $res = curl_exec($ch);
        $err = curl_error($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($err !== '' || $code !== 200 || !is_string($res)) {
            throw new RuntimeException("Failed to fetch PayPal order {$cleanId} (HTTP {$code}): {$err}");
        }

        return json_decode($res, true) ?: [];
    }

    /**
     * Verify Webhook Signature according to official PayPal specifications.
     * Uses POST /v1/notifications/verify-webhook-signature
     */
    public function verifyWebhookSignature(array $headers, string $rawBody): bool {
        // Normalize headers
        $h = [];
        foreach ($headers as $k => $v) {
            $h[strtoupper(str_replace('-', '_', (string)$k))] = is_array($v) ? $v[0] : (string)$v;
        }

        $transId = $h['PAYPAL_TRANSMISSION_ID'] ?? $h['HTTP_PAYPAL_TRANSMISSION_ID'] ?? '';
        $transTime = $h['PAYPAL_TRANSMISSION_TIME'] ?? $h['HTTP_PAYPAL_TRANSMISSION_TIME'] ?? '';
        $transSig = $h['PAYPAL_TRANSMISSION_SIG'] ?? $h['HTTP_PAYPAL_TRANSMISSION_SIG'] ?? '';
        $certUrl = $h['PAYPAL_CERT_URL'] ?? $h['HTTP_PAYPAL_CERT_URL'] ?? '';
        $authAlgo = $h['PAYPAL_AUTH_ALGO'] ?? $h['HTTP_PAYPAL_AUTH_ALGO'] ?? '';

        if ($transId === '' || $transTime === '' || $transSig === '' || $certUrl === '' || $authAlgo === '') {
            return false;
        }

        // Strict certificate URL validation (must be paypal.com domain over HTTPS)
        $parsedUrl = parse_url($certUrl);
        $host = strtolower($parsedUrl['host'] ?? '');
        $scheme = strtolower($parsedUrl['scheme'] ?? '');
        if ($scheme !== 'https' || (!str_ends_with($host, '.paypal.com') && $host !== 'paypal.com')) {
            return false;
        }

        if ($this->mockMode) {
            // In mock mode, if signature is "INVALID_TEST_SIG" or "FORGED_SIGNATURE", reject
            if (str_contains($transSig, 'INVALID') || str_contains($transSig, 'FORGED')) {
                return false;
            }
            return true;
        }

        if (empty($this->webhookId)) {
            throw new DomainException("PayPal configuration error: PAYPAL_WEBHOOK_ID is required for webhook signature verification.");
        }

        // Live API signature verification
        try {
            $token = $this->getAccessToken();
            $webhookId = $this->webhookId;

            $verificationPayload = [
                'transmission_id' => $transId,
                'transmission_time' => $transTime,
                'cert_url' => $certUrl,
                'auth_algo' => $authAlgo,
                'transmission_sig' => $transSig,
                'webhook_id' => $webhookId,
                'webhook_event' => json_decode($rawBody, true) ?: []
            ];

            $url = $this->apiBaseUrl . '/v1/notifications/verify-webhook-signature';
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($verificationPayload, JSON_UNESCAPED_SLASHES),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 15,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $token
                ],
                CURLOPT_SSL_VERIFYPEER => true
            ]);

            $res = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($code === 200 && is_string($res)) {
                $verData = json_decode($res, true);
                return ($verData['verification_status'] ?? '') === 'SUCCESS';
            }
        } catch (Throwable $e) {
            error_log('PayPal webhook verification exception: ' . $e->getMessage());
        }

        return false;
    }
}
