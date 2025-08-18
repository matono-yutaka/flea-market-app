<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Webhook;
use App\Models\Purchase;

class StripeWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret'); // Stripe の Webhook Secret

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        if ($event->type === 'payment_intent.succeeded') {
            $intent = $event->data->object;
            // あなたのロジックに応じて item_id や user_id を取得して保存
            // 例: metadata を使って保存していた場合
            $userId = $intent->metadata->user_id;
            $itemId = $intent->metadata->item_id;
            // 購入保存処理
            Purchase::create([
                'user_id' => $userId,
                'item_id' => $itemId,
                'shipping_address_id' => ・・・ // 必要に応じて
            ]);
        }

        return response()->json(['status' => 'success']);
    }

}
