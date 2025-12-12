<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Message;
use App\Http\Requests\ChatRequest;
use App\Mail\TradeCompletedMail;
use Illuminate\Support\Facades\Mail;

class ChatController extends Controller
{
    // 取引チャット画面表示
    public function chat($id)
    {
        $purchase = Purchase::with('item.user.profile')->findOrFail($id);
        $user = auth()->user();

        // 取引中購入商品取得
        $buyingTradingItems = Purchase::where('user_id', auth()->id())
            ->where('status', 'trading')
            ->with('item')
            ->get();

        // 取引中出品商品取得
        $sellingTradingItems = Purchase::whereHas('item', function($query){
            $query->where('user_id', auth()->id());
            })
            ->where('status', 'trading')
            ->with('item')
            ->get();

        $purchases = $buyingTradingItems->merge($sellingTradingItems);

        // 取引中商品に関連するメッセージ取得
        $messages = Message::where('purchase_id', $id)
        ->orderBy('created_at', 'asc')
        ->get();

        Message::where('purchase_id', $id)
        ->where('user_id', '!=', auth()->id())
        ->update(['is_read' => true]);

        // 購入者の場合
        if ($purchase->user_id === $user->id){
        return view('profile.buyer_trading_chat', compact('purchase', 'purchases', 'messages'));}
        // 出品者の場合
        if ($purchase->item->user_id === $user->id){
        return view('profile.seller_trading_chat', compact('purchase', 'purchases', 'messages'));}
        // どちらでもない → 不正アクセス防止
        abort(403, 'アクセス権がありません');
    }


    // 取引チャット
    public function message(ChatRequest $request, $id)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('chat_images', 'public');
}
        Message::create([
            'user_id' => auth()->id(),
            'purchase_id' => $id,
            'comment' => $data['comment'],
            'image' => $data['image'] ?? null
        ]);

        return back();
    }


    // 取引チャット編集処理
    public function edit(Request $request, $messageId)
    {
        Message::where('id', $messageId)
        ->first()
        ->update([
            'comment' => $request->comment
        ]);

        return back();
    }


    // 取引チャット削除処理
    public function destroy($messageId)
    {
        Message::where('id', $messageId)
        ->first()
        ->delete();

        return back();
    }


    // 取引後評価処理
    public function store(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $purchase = Purchase::findOrFail($id);
        $user = auth()->user();
        $buyer = $purchase->user;
        $seller = $purchase->item->user;

        // 評価保存
        if ($user->id === $buyer->id) {
            // 購入者が出品者を評価
            $purchase->rating_buyer = $request->rating;
        } else {
            // 出品者が購入者を評価
            $purchase->rating_seller = $request->rating;
        }

        $purchase->status = 'completed';
        $purchase->save();

        // 出品者へ送信
        if($purchase->user_id === $user->id){
        Mail::to($seller->email)->send(new TradeCompletedMail($purchase));
        }

        return redirect()->route('mypage', ['page' => 'sell'])->with('message', '評価を送信しました！');
    }

}
