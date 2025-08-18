<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Checkout\Session;
use App\Models\Item;
use App\Models\ShippingAddress;
use App\Models\Purchase;


class PaymentController extends Controller
{
    public function checkout($itemId)
    {
        $item = Item::findOrFail($itemId);

        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $checkoutSession = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => [[
            'price_data' => [
                'currency' => 'jpy',
                'product_data' => [
                    'name' => $item->name,
                ],
                'unit_amount' => $item->price,
            ],
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'success_url' => route('purchase.success', ['itemId' => $item->id]),
        'cancel_url' => route('purchase.detail', ['itemId' => $item->id]),
        ]);

        return redirect($checkoutSession->url);
    }


    public function konbiniCheckout(Request $request, $itemId)
    {
        $user = auth()->user();
        $item = Item::findOrFail($itemId);

        Stripe::setApiKey(config('services.stripe.secret'));

        $intent = PaymentIntent::create([
            'amount' => $item->price, // 円単位で指定（コンビニ払いは 100未満はNG）
            'currency' => 'jpy',
            'payment_method_types' => ['konbini'],
            'description' => $item->name,
            'payment_method_options' => [
                'konbini' => [
                    'expires_after_days' => 3, // 支払い期限（最大14日）
                ],
            ],
            'receipt_email' => auth()->user()->email,
            'confirm' => true, // ←これでnext_actionが返ってくる
            'payment_method_data' => [
                'type' => 'konbini',
                'billing_details' => [
                    'name' => $user->name ?? 'Test User', // ← ユーザー名が必要
                    'email' => $user->email,
                ],
            ],
        ]);
        return redirect($intent->next_action->konbini_display_details->hosted_voucher_url);
    }


    public function handleSuccess(Request $request, $itemId)
    {
        $user = auth()->user();
        // プロフィール未登録なら購入処理させない
    if (!$user->profile) {
        return redirect()->route('profile.edit')
            ->with('error', '購入にはプロフィール登録が必要です。');
    }
        $item = Item::findOrFail($itemId);
        // 1. 住所情報の取得
        $shipping = ShippingAddress::where('user_id', $user->id)
            ->where('item_id', $itemId)
            ->first();
        // なければプロフィール住所を使う
        if (!$shipping) {
            $shipping = new ShippingAddress([
                'user_id' => $user->id,
                'item_id' => $itemId,
                'post_code' => $user->profile->post_code,
                'address' => $user->profile->address,
                'building' => $user->profile->building,
            ]);
            $shipping->save();
        }
        // 2. 購入情報の保存
        Purchase::create([
            'user_id' => $user->id,
            'item_id' => $itemId,
            'shipping_address_id' => $shipping->id, // 必要ならリレーション用に
        ]);
        // 3. トップページへリダイレクト
        return redirect('/')->with('message', '購入が完了しました。');
    }
}


