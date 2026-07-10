<?php
// app/Http/Controllers/PaymentWebhookController.php

namespace App\Http\Controllers;

use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Receives inbound webhooks from mobile money providers when a
 * payment succeeds or fails, then hands off to PaymentService
 * which marks the matching Order as paid.
 *
 * IMPORTANT: in production, verify the request signature/IP per
 * provider docs before trusting the payload (left as a TODO marker
 * for the specific provider credentials you configure).
 */
class PaymentWebhookController extends Controller
{
    public function __construct(protected PaymentService $payments) {}

    public function handle(Request $request, string $provider)
    {
        Log::info("Payment webhook received from {$provider}", $request->all());

        $this->payments->handleWebhook($provider, $request->all());

        // Mobile money providers expect a 200 OK with no specific body
        return response()->json(['received' => true]);
    }
}
