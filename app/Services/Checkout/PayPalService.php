<?php

namespace App\Services\Checkout;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class PayPalService
{
    public function isConfigured(): bool
    {
        return $this->clientId() !== '' && $this->clientSecret() !== '';
    }

    public function isSandbox(): bool
    {
        return config('paypal.mode', 'sandbox') !== 'live';
    }

    public function clientId(): string
    {
        return (string) config('paypal.client_id', '');
    }

    public function clientSecret(): string
    {
        return (string) config('paypal.client_secret', '');
    }

    public function currency(): string
    {
        $currency = strtoupper((string) config('paypal.currency', 'USD'));

        return in_array($currency, ['USD', 'GBP', 'EUR'], true) ? $currency : 'USD';
    }

    public function locale(): string
    {
        return match ($this->currency()) {
            'GBP' => 'en-GB',
            'EUR' => 'en-GB',
            default => 'en-US',
        };
    }

    /** Amount sent to PayPal API (formatted). */
    public function chargeAmount(float $cartTotal): string
    {
        return number_format($this->resolvePayPalAmount($cartTotal), 2, '.', '');
    }

    public function sdkScriptBaseUrl(): string
    {
        return 'https://www.sandbox.paypal.com/sdk/js';
    }

    public function baseUrl(): string
    {
        $configured = rtrim((string) config('paypal.base_url', ''), '/');

        if ($configured === '' || str_contains($configured, 'api-m.paypal.com')) {
            return 'https://api-m.sandbox.paypal.com';
        }

        return $configured;
    }

    /**
     * Full-page PayPal checkout (sandbox redirect flow).
     *
     * @return array{id: string, status: string, amount: string, currency: string, approve_url: string, raw: array}
     */
    public function createCheckoutOrderForRedirect(float $cartTotal, string $returnUrl, string $cancelUrl): array
    {
        $payload = $this->buildOrderPayload($cartTotal, [
            'application_context' => [
                'brand_name' => 'GoGROCERY',
                'locale' => $this->locale(),
                'landing_page' => 'LOGIN',
                'user_action' => 'PAY_NOW',
                'shipping_preference' => 'NO_SHIPPING',
                'payment_method_preference' => 'UNRESTRICTED',
                'return_url' => $returnUrl,
                'cancel_url' => $cancelUrl,
            ],
        ]);

        $response = $this->postOrder($payload);

        $data = $response->json();
        $id = (string) ($data['id'] ?? '');
        $approveUrl = $this->extractApprovalUrl($data);
        $unit = $payload['purchase_units'][0]['amount'];

        if ($id === '' || $approveUrl === '') {
            throw new RuntimeException('PayPal did not return an approval URL');
        }

        return [
            'id' => $id,
            'status' => (string) ($data['status'] ?? ''),
            'amount' => $unit['value'],
            'currency' => $unit['currency_code'],
            'approve_url' => $approveUrl,
            'raw' => $data,
        ];
    }

    public function extractApprovalUrl(array $orderResponse): string
    {
        foreach ($orderResponse['links'] ?? [] as $link) {
            if (($link['rel'] ?? '') === 'approve') {
                return (string) ($link['href'] ?? '');
            }
        }

        return '';
    }

    /**
     * @return array{id: string, status: string, amount: string, currency: string, raw: array}
     */
    public function createCheckoutOrder(float $cartTotal): array
    {
        $payload = $this->buildOrderPayload($cartTotal);
        $response = $this->postOrder($payload);

        $data = $response->json();
        $id = (string) ($data['id'] ?? '');
        $unit = $payload['purchase_units'][0]['amount'];

        if ($id === '') {
            throw new RuntimeException('PayPal create order returned no order ID');
        }

        return [
            'id' => $id,
            'status' => (string) ($data['status'] ?? ''),
            'amount' => $unit['value'],
            'currency' => $unit['currency_code'],
            'raw' => $data,
        ];
    }

    public function captureOrder(string $paypalOrderId): array
    {
        Log::info('PayPal capture request', ['order_id' => $paypalOrderId]);

        $response = Http::withToken($this->accessToken())
            ->acceptJson()
            ->withBody('{}', 'application/json')
            ->post($this->baseUrl().'/v2/checkout/orders/'.$paypalOrderId.'/capture');

        if (!$response->successful()) {
            Log::error('PayPal capture failed', [
                'order_id' => $paypalOrderId,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new RuntimeException('PayPal capture failed: '.$response->body());
        }

        $data = $response->json();
        Log::info('PayPal capture response', [
            'order_id' => $paypalOrderId,
            'status' => $data['status'] ?? null,
        ]);

        return $data;
    }

    public function extractCapturedAmount(array $captureResponse): float
    {
        $unit = $captureResponse['purchase_units'][0] ?? [];
        $capture = $unit['payments']['captures'][0] ?? [];

        return (float) ($capture['amount']['value'] ?? 0);
    }

    public function extractCaptureId(array $captureResponse): string
    {
        $unit = $captureResponse['purchase_units'][0] ?? [];
        $capture = $unit['payments']['captures'][0] ?? [];

        return (string) ($capture['id'] ?? '');
    }

    public function isCaptureCompleted(array $captureResponse): bool
    {
        return strtoupper((string) ($captureResponse['status'] ?? '')) === 'COMPLETED';
    }

    /**
     * @param  array<string, mixed>  $extra
     * @return array<string, mixed>
     */
    protected function buildOrderPayload(float $cartTotal, array $extra = []): array
    {
        $currency = $this->currency();
        $value = $this->chargeAmount($cartTotal);

        $payload = array_merge([
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'reference_id' => 'GG-'.time(),
                'description' => 'GoGROCERY order',
                'amount' => [
                    'currency_code' => $currency,
                    'value' => $value,
                ],
            ]],
        ], $extra);

        Log::info('PayPal create order payload', $payload);

        return $payload;
    }

    protected function postOrder(array $payload): \Illuminate\Http\Client\Response
    {
        $response = Http::withToken($this->accessToken())
            ->acceptJson()
            ->post($this->baseUrl().'/v2/checkout/orders', $payload);

        $responseData = $response->json() ?? ['raw' => $response->body()];

        if (!$response->successful()) {
            Log::error('PayPal create order failed', [
                'status' => $response->status(),
                'response' => $responseData,
                'payload' => $payload,
            ]);
            throw new RuntimeException('PayPal create order failed: '.$response->body());
        }

        Log::info('PayPal create order response', $responseData);

        return $response;
    }

    protected function resolvePayPalAmount(float $cartTotal): float
    {
        return max(0.01, round($cartTotal, 2));
    }

    protected function accessToken(): string
    {
        $cacheKey = 'paypal_access_token_'.substr($this->clientId(), 0, 12);

        return Cache::remember($cacheKey, 3000, function (): string {
            $response = Http::asForm()
                ->withBasicAuth($this->clientId(), $this->clientSecret())
                ->post($this->baseUrl().'/v1/oauth2/token', [
                    'grant_type' => 'client_credentials',
                ]);

            if (!$response->successful()) {
                Log::error('PayPal authentication failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                throw new RuntimeException('PayPal authentication failed: '.$response->body());
            }

            return (string) $response->json('access_token');
        });
    }
}
