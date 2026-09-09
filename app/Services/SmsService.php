<?php
// app/Services/SmsService.php

namespace App\Services;

use App\Models\Order;
use App\Models\Delivery;
use App\Models\DiseaseAlert;
use App\Models\SmsCampaign;
use App\Models\SmsLog;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * SmsService — single point of integration with the SMS gateway
 * (Africa's Talking by default; swap the sendRaw() method for Twilio if needed).
 *
 * All SMS sent anywhere in the app MUST go through this service so that
 * every message is logged in sms_logs for auditing and cost tracking.
 */
class SmsService
{
    protected string $gateway;
    protected ?string $apiKey;
    protected ?string $username;
    protected string $senderId;

    public function __construct()
    {
        $this->gateway = config("services.sms.gateway", "africas_talking");
        $this->apiKey = config("services.sms.api_key");
        $this->username = config("services.sms.username");
        $this->senderId = config("services.sms.sender_id", "AgriTech");
    }

    // ── Public API — Transactional Messages ─────────────────────────────

    public function sendOrderStatusUpdate(Order $order): void
    {
        $messages = [
            "confirmed"  => "Order #{$order->order_number} confirmed! Payment received. We're preparing your items.",
            "packing"    => "Order #{$order->order_number} is being packed at the warehouse. Tracking sticker assigned.",
            "dispatched" => "Your order #{$order->order_number} has been dispatched. Tracking: agritechpro.zm/track",
            "on_the_way" => "🚛 Order #{$order->order_number} is on the way! Driver is heading to {$order->delivery_district}. Track live: agritechpro.zm/track",
            "delivered"  => "✅ Order #{$order->order_number} has been delivered. Thank you for using AgriTech Pro!",
            "cancelled"  => "Order #{$order->order_number} has been cancelled. Refund processing if applicable.",
        ];

        if (!isset($messages[$order->status])) {
            return;
        }

        $this->send(
            phone: $order->buyer->phone,
            message: $messages[$order->status],
            type: "order_dispatched",
            recipient: $order->buyer,
            related: $order,
        );
    }

    public function sendPaymentConfirmation(Order $order): void
    {
        $this->send(
            phone: $order->buyer->phone,
            message: "Payment of " .
                number_format($order->total) .
                " {$order->currency} received for order #{$order->order_number}. Thank you!",
            type: "payment_received",
            recipient: $order->buyer,
            related: $order,
        );

        // Notify seller too
        $this->send(
            phone: $order->seller->phone,
            message: "You received a payment of " .
                number_format($order->subtotal) .
                " {$order->currency} for order #{$order->order_number}.",
            type: "payment_received",
            recipient: $order->seller,
            related: $order,
        );
    }

    public function sendNearDestinationAlert(Delivery $delivery): void
    {
        $dist = $delivery->distance_remaining_km ?? 2;
        $town = $delivery->destination_district;
        $driverName = $delivery->driver?->user?->name ?? 'your driver';
        $driverPhone = $delivery->driver?->user?->phone ?? '';

        $this->send(
            phone: $delivery->order->buyer->phone,
            message: "📍 Your order #{$delivery->order->order_number} is {$dist}km from {$town}! {$driverName} is arriving soon. Driver contact: {$driverPhone}. Track: agritechpro.zm/track",
            type: "order_arrived",
            recipient: $delivery->order->buyer,
            related: $delivery->order,
        );
    }

    public function sendLessonAvailable(User $user, string $courseTitle): void
    {
        $this->send(
            phone: $user->phone,
            message: "New lesson available in \"{$courseTitle}\"! Login to agritechpro.zm to continue learning.",
            type: "lesson_available",
            recipient: $user,
        );
    }

    public function sendOtp(User $user, string $code): void
    {
        $this->send(
            phone: $user->phone,
            message: "Your AgriTech Pro verification code is: {$code}. Valid for 10 minutes. Do not share this code.",
            type: "otp",
            recipient: $user,
        );
    }

    // ── Broadcast Messages ────────────────────────────────────────────

    /**
     * Broadcast a disease alert SMS to every farmer in the affected districts.
     * Returns the number of farmers notified.
     */
    public function broadcastDiseaseAlert(DiseaseAlert $alert): int
    {
        $recipients = User::active()
            ->farmers()
            ->where("sms_alerts", true)
            ->whereIn("district", $alert->affected_districts)
            ->whereNotNull("phone")
            ->get();

        $message =
            "⚠️ DISEASE ALERT: {$alert->title}. " .
            ($alert->recommended_action
                ? "Action: {$alert->recommended_action}. "
                : "") .
            "Details: agritechpro.zm/diseases";

        foreach ($recipients as $farmer) {
            $this->send(
                phone: $farmer->phone,
                message: $message,
                type: "disease_alert",
                recipient: $farmer,
                related: $alert,
            );
        }

        return $recipients->count();
    }

    /**
     * Execute a full SMS broadcast campaign (admin "Send Now" action).
     * Sends to every user matching the campaign's target filters.
     */
    public function executeCampaign(SmsCampaign $campaign): void
    {
        $campaign->update(["status" => "sending"]);

        $recipients = $campaign->targetAudience()->get();
        $sent = 0;
        $failed = 0;
        $totalCost = 0;

        foreach ($recipients as $user) {
            $log = $this->send(
                phone: $user->phone,
                message: $campaign->message,
                type: "custom",
                recipient: $user,
            );

            if ($log && $log->status !== "failed") {
                $sent++;
                $totalCost += (float) $log->cost;
            } else {
                $failed++;
            }
        }

        $campaign->update([
            "status" => "sent",
            "sent_at" => now(),
            "total_recipients" => $recipients->count(),
            "total_sent" => $sent,
            "total_failed" => $failed,
            "total_cost" => $totalCost,
        ]);
    }

    // ── Core Send Method ──────────────────────────────────────────────

    /**
     * Send a single SMS and log it. This is the only method that talks
     * to the actual gateway — every other method funnels through here.
     */
    public function send(
        ?string $phone,
        string $message,
        string $type,
        ?User $recipient = null,
        ?\Illuminate\Database\Eloquent\Model $related = null,
    ): ?SmsLog {
        if (!$phone) {
            Log::warning(
                "SmsService: attempted to send '{$type}' SMS but recipient has no phone.",
            );
            return null;
        }

        $log = SmsLog::create([
            "type" => $type,
            "message" => $message,
            "recipient_phone" => $phone,
            "recipient_id" => $recipient?->id,
            "status" => "pending",
            "gateway" => $this->gateway,
            "related_type" => $related ? get_class($related) : null,
            "related_id" => $related?->id,
        ]);

        try {
            $result = $this->sendRaw($phone, $message);

            $log->update([
                "status" => $result["success"] ? "sent" : "failed",
                "gateway_message_id" => $result["message_id"] ?? null,
                "cost" => $result["cost"] ?? 0.012, // ~$0.012/SMS avg
                "sent_at" => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error("SmsService send failed: {$e->getMessage()}");
            $log->update(["status" => "failed"]);
        }

        return $log;
    }

    /**
     * Raw gateway call — Africa's Talking REST API.
     * Swap this method's body to integrate Twilio or another provider.
     */
    protected function sendRaw(string $phone, string $message): array
    {
        // In local/testing environment, just simulate success without an API call
        if (app()->environment(["local", "testing"])) {
            Log::info("SMS [simulated] to {$phone}: {$message}");
            return [
                "success" => true,
                "message_id" => "sim_" . uniqid(),
                "cost" => 0.012,
            ];
        }

        $response = Http::asForm()
            ->withHeaders([
                "apiKey" => $this->apiKey,
                "Accept" => "application/json",
            ])
            ->post("https://api.africastalking.com/version1/messaging", [
                "username" => $this->username,
                "to" => $this->normalizePhone($phone),
                "message" => $message,
                "from" => $this->senderId,
            ]);

        $data = $response->json();
        $recipient = $data["SMSMessageData"]["Recipients"][0] ?? null;

        return [
            "success" => $recipient && $recipient["status"] === "Success",
            "message_id" => $recipient["messageId"] ?? null,
            "cost" => isset($recipient["cost"])
                ? (float) str_replace(["MWK", " "], "", $recipient["cost"])
                : 0.012,
        ];
    }

    /** Ensure phone numbers are in international format, e.g. +265... */
    protected function normalizePhone(string $phone): string
    {
        $phone = preg_replace("/\D/", "", $phone);

        if (str_starts_with($phone, "0")) {
            $phone = "265" . substr($phone, 1); // Malawi country code
        }

        return "+" . ltrim($phone, "+");
    }
}
