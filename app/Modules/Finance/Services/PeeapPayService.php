<?php

namespace App\Modules\Finance\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PeeapPayService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $merchantId;
    protected string $secretKey;

    public function __construct()
    {
        $this->baseUrl = config('services.peeappay.base_url', 'https://api.peeappay.com/v1');
        $this->apiKey = config('services.peeappay.api_key', '');
        $this->merchantId = config('services.peeappay.merchant_id', '');
        $this->secretKey = config('services.peeappay.secret_key', '');
    }

    /**
     * Initialize a payment transaction
     */
    public function initializePayment(array $data): array
    {
        try {
            $payload = [
                'merchant_id' => $this->merchantId,
                'amount' => $data['amount'],
                'currency' => $data['currency'] ?? 'NLE',
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'description' => $data['description'] ?? 'College Fee Payment',
                'reference' => $data['reference'],
                'callback_url' => $data['callback_url'],
                'return_url' => $data['return_url'],
                'metadata' => [
                    'invoice_id' => $data['invoice_id'] ?? null,
                    'student_id' => $data['student_id'] ?? null,
                    'institution_id' => $data['institution_id'] ?? null,
                    'payment_type' => $data['payment_type'] ?? 'tuition',
                ],
            ];

            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                    'X-Merchant-Id' => $this->merchantId,
                ])
                ->post("{$this->baseUrl}/transactions/initialize", $payload);

            if ($response->successful()) {
                $result = $response->json();
                return [
                    'success' => true,
                    'transaction_id' => $result['data']['transaction_id'] ?? $result['transaction_id'] ?? null,
                    'checkout_url' => $result['data']['checkout_url'] ?? $result['checkout_url'] ?? null,
                    'reference' => $data['reference'],
                    'raw' => $result,
                ];
            }

            Log::warning('PeeapPay init failed', ['status' => $response->status(), 'body' => $response->body()]);
            return [
                'success' => false,
                'message' => $response->json()['message'] ?? 'Payment initialization failed',
                'status_code' => $response->status(),
            ];
        } catch (\Exception $e) {
            Log::error('PeeapPay init exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Payment gateway unreachable: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Verify a transaction by reference
     */
    public function verifyTransaction(string $reference): array
    {
        try {
            $response = Http::timeout(15)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'X-Merchant-Id' => $this->merchantId,
                ])
                ->get("{$this->baseUrl}/transactions/verify/{$reference}");

            if ($response->successful()) {
                $result = $response->json();
                $txData = $result['data'] ?? $result;
                return [
                    'success' => true,
                    'status' => $txData['status'] ?? 'unknown',
                    'amount' => $txData['amount'] ?? 0,
                    'currency' => $txData['currency'] ?? 'NLE',
                    'reference' => $reference,
                    'transaction_id' => $txData['transaction_id'] ?? null,
                    'paid_at' => $txData['paid_at'] ?? $txData['completed_at'] ?? null,
                    'channel' => $txData['channel'] ?? $txData['payment_method'] ?? null,
                    'metadata' => $txData['metadata'] ?? [],
                    'raw' => $result,
                ];
            }

            return [
                'success' => false,
                'status' => 'verification_failed',
                'message' => 'Could not verify transaction',
                'status_code' => $response->status(),
            ];
        } catch (\Exception $e) {
            Log::error('PeeapPay verify exception: ' . $e->getMessage());
            return [
                'success' => false,
                'status' => 'error',
                'message' => 'Verification failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Generate a unique payment reference
     */
    public static function generateReference(string $prefix = 'OC'): string
    {
        return $prefix . '-' . strtoupper(bin2hex(random_bytes(4))) . '-' . time();
    }

    /**
     * Validate webhook signature
     */
    public function validateWebhookSignature(string $payload, string $signature): bool
    {
        $computed = hash_hmac('sha512', $payload, $this->secretKey);
        return hash_equals($computed, $signature);
    }
}
