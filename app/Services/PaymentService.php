<?php
// app/Services/PaymentService.php

namespace App\Services;

use App\Events\OrderStatusChanged;
use App\Jobs\BroadcastNotification;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * PaymentService — Mobile Money payment integration (Airtel Money / TNM Mpamba / MTN MoMo).
 *
 * Flow:
 *  1. initiatePayment()  → calls the provider, returns a reference + checkout instructions
 *  2. provider sends a webhook to PaymentWebhookController when payment completes
 *  3. handleWebhook()    → verifies signature, marks the order paid via Order::markAsPaid()
 */
class PaymentService
{
    /**
     * Kick off a mobile money payment request (STK push / USSD prompt)
     * for the given order and payment method.
     *
     * @return array{reference: string, status: string, instructions: string}
     */
    public function initiatePayment(Order $order, string $method, string $phone): array
    {
        return match ($method) {
            'airtel_money' => $this->initiateAirtelMoney($order, $phone),
            'mtn_momo'     => $this->initiateMtnMomo($order, $phone),
            'tnm_mpamba'   => $this->initiateTnmMpamba($order, $phone),
            default => throw new \Exception("Unsupported payment method: {$method}"),
        };
    }

    protected function initiateAirtelMoney(Order $order, string $phone): array
    {
        $reference = 'AM-' . $order->order_number . '-' . Str::random(6);

        if (app()->environment(['local', 'testing'])) {
            Log::info("Airtel Money [simulated] push to {$phone} for {$order->total} {$order->currency}");
            return [
                'reference'    => $reference,
                'status'       => 'pending',
                'instructions' => "A payment prompt has been sent to {$phone}. Enter your Airtel Money PIN to confirm.",
            ];
        }

        $response = Http::withToken(config('services.airtel.api_key'))
            ->post(config('services.airtel.base_url') . '/merchant/v1/payments/', [
                'reference'   => $reference,
                'subscriber'  => ['country' => 'MW', 'currency' => $order->currency, 'msisdn' => $phone],
                'transaction' => ['amount' => $order->total, 'country' => 'MW', 'currency' => $order->currency, 'id' => $reference],
            ]);

        if (!$response->successful()) {
            throw new \Exception('Airtel Money request failed: ' . $response->body());
        }

        $order->update(['payment_reference' => $reference]);

        return [
            'reference'    => $reference,
            'status'       => 'pending',
            'instructions' => "A payment prompt has been sent to {$phone}. Enter your Airtel Money PIN to confirm.",
        ];
    }

    protected function initiateMtnMomo(Order $order, string $phone): array
    {
        $reference = 'MOMO-' . $order->order_number . '-' . Str::random(6);

        if (app()->environment(['local', 'testing'])) {
            Log::info("MTN MoMo [simulated] push to {$phone} for {$order->total} {$order->currency}");
        }

        $order->update(['payment_reference' => $reference]);

        return [
            'reference'    => $reference,
            'status'       => 'pending',
            'instructions' => "Dial *211# or check your MTN MoMo app to approve the payment of {$order->total} {$order->currency}.",
        ];
    }

    protected function initiateTnmMpamba(Order $order, string $phone): array
    {
        $reference = 'MPAMBA-' . $order->order_number . '-' . Str::random(6);
        $order->update(['payment_reference' => $reference]);

        return [
            'reference'    => $reference,
            'status'       => 'pending',
            'instructions' => "Dial *444# to complete your TNM Mpamba payment of {$order->total} {$order->currency}.",
        ];
    }

    /**
     * Handle an inbound payment-confirmation webhook from any provider.
     * Call this from a dedicated PaymentWebhookController route.
     */
    public function handleWebhook(string $provider, array $payload): void
    {
        $reference = $payload['reference'] ?? $payload['transaction_id'] ?? null;
        $isSuccessful = ($payload['status'] ?? '') === 'success' || ($payload['status'] ?? '') === 'TS'; // TS = Airtel "TransactionSuccess"

        if (!$reference) {
            Log::warning("Payment webhook missing reference", $payload);
            return;
        }

        $order = Order::where('payment_reference', $reference)->first();

        if (!$order) {
            Log::warning("Payment webhook: no order found for reference {$reference}");
            return;
        }

        if ($isSuccessful) {
            $oldStatus = $order->status;
            $order->markAsPaid($reference);

            // Broadcast payment confirmed to buyer
            try {
                BroadcastNotification::dispatch(
                    $order->buyer_id,
                    "💰 Payment Confirmed — " . $order->order_number,
                    "Your payment of MWK " . number_format($order->total) . " has been confirmed. Your order is being processed!",
                    "order",
                    "fas fa-check-circle",
                    "#16a34a",
                    route("marketplace.my-orders"),
                );
            } catch (\Throwable $e) {}

            // Broadcast payment confirmed to seller
            try {
                BroadcastNotification::dispatch(
                    $order->seller_id,
                    "💰 Payment Received — " . $order->order_number,
                    "Payment of MWK " . number_format($order->total) . " received for order " . $order->order_number . ". Prepare items for dispatch.",
                    "order",
                    "fas fa-money-bill-wave",
                    "#16a34a",
                    route("marketplace.my-listings"),
                );
            } catch (\Throwable $e) {}

            // Broadcast order status change event
            event(new OrderStatusChanged($order->refresh(), $oldStatus));
        } else {
            $order->update(['payment_status' => 'failed']);

            // Notify buyer of payment failure
            try {
                BroadcastNotification::dispatch(
                    $order->buyer_id,
                    "❌ Payment Failed — " . $order->order_number,
                    "Your payment for order " . $order->order_number . " was not successful. Please try again.",
                    "order",
                    "fas fa-times-circle",
                    "#ef4444",
                    route("marketplace.my-orders"),
                );
            } catch (\Throwable $e) {}
        }
    }
}
