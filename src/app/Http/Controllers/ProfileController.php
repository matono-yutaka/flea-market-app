<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProfileRequest;
use App\Models\UserProfile;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Message;

class ProfileController extends Controller
{
    // プロフィール編集画面表示
    public function edit()
    {
        // ログイン中ユーザー情報取得
        $user = auth()->user();
        // ログイン中ユーザーのプロフィール情報取得、なければ新規作成用の空オブジェクト
        $profile = $user->profile  ?? new UserProfile;

        return view('profile.save', compact('profile', 'user'));
    }


    // マイページ画面表示
    public function mypage(Request $request)
    {
        // クエリパラメータからページ情報取得
        $page = $request->query('page');
        // ログイン中ユーザー情報取得
        $user = auth()->user();
        // プロフィール情報を取得（存在しない場合は新しいインスタンスを用意）
        $profile = $user->profile  ?? new UserProfile;

        // 出品者として受け取った評価（rating_buyer）
        $sellerRatings = Purchase::whereHas('item', function($query){
            $query->where('user_id', auth()->id());
            })
            ->whereNotNull('rating_buyer')
            ->pluck('rating_buyer');

        // 購入者として受け取った評価（rating_seller）
        $buyerRatings = Purchase::where('user_id', auth()->id())
            ->whereNotNull('rating_seller')
            ->pluck('rating_seller');

        // 全部まとめるだけ
        $allRatings = $sellerRatings->merge($buyerRatings);

        // 平均を計算（評価が1件もない場合は null）
        $totalAvg = $allRatings->avg();

        // 四捨五入（小数点を丸める）
        // $totalAvg が null の可能性があるのでチェックする
        $roundedAvg = $totalAvg ? round($totalAvg) : null;

        // 購入側
        $buyingTradingItems = Purchase::where('user_id', auth()->id())
            ->with('item', 'messages')
            ->get();

        // 出品側
        $sellingTradingItems = Purchase::whereHas('item', function($query){
            $query->where('user_id', auth()->id());
            })
            ->with('item', 'messages')
            ->get();

        // マージ
        $tradingItems = $buyingTradingItems->merge($sellingTradingItems);

        // ★未読の合計を数える → 初期化が必要
        $totalUnread = 0;

        foreach($tradingItems as $tradingItem)
        {
            $tradingItem->unread_count = Message::where('purchase_id', $tradingItem->id)
            ->where('user_id', '!=', auth()->id()) // 自分以外からのメッセージ
            ->where('is_read', false) ->count();

            $totalUnread += $tradingItem->unread_count;
        }

        // 最新メッセージが新しい順に並べ替え
        $tradingItems = $tradingItems->sortByDesc(function($purchase){
            return optional($purchase->messages->last())->created_at;
            });

        // 出品商品一覧
        if ($page === 'sell'){
            // ログイン中ユーザーの商品取得
            $items = Item::where('user_id', $user->id)->get();

            // 購入済みの商品のID一覧を取得（購入済みアイテムを判定するために使用）
            $purchasedItemIds = Purchase::pluck('item_id')->toArray();

            return view('profile.mypage', compact('profile', 'items', 'purchasedItemIds', 'page', 'roundedAvg', 'totalUnread'));

        // 取引中商品
        } elseif ($page === 'trading'){

            return view('profile.mypage_trading', compact('profile', 'tradingItems', 'page', 'roundedAvg', 'totalUnread'));

        } else{
            // ログインユーザーが購入した商品取得(item同時取得:bladeでリレーション使っているため(N+1問題回避のため))
            $purchases = Purchase::where('user_id', $user->id)
            ->with('item')
            ->get();

            return view('profile.mypage_purchase', compact('profile', 'purchases','page', 'roundedAvg', 'totalUnread'));
        }
    }


    // プロフィール情報登録処理
    public function saveProfile(ProfileRequest $request)
    {
        // ログイン中ユーザーの情報取得
        $user = auth()->user();
        // フォーム入力情報取得
        $data = $request->only('name', 'post_code', 'address', 'building');
        // ログインユーザーidを追加
        $data['user_id'] = auth()->id();

        // 画像アップロード処理
        // ProfileRequestで一時保存された画像がセッションに存在する場合
        if (session()->has('temp_profile_image_path')) {
            // セッションから一時保存パス取得
            $tempPath = session('temp_profile_image_path');
            // 一時フォルダから正式な保存フォルダに移動するための新パスを生成
            $newPath = 'images/national_flag/' . basename($tempPath);
            // temp → images へ移動
            \Storage::disk('public')->move($tempPath, $newPath);

            // 保存データにファイル名を設定
            $data['image'] = basename($newPath);
            // 一時パス情報をセッションから削除
            session()->forget('temp_profile_image_path');
        }
        // プロフィールが既にあるかどうかで初回か更新かを判定
        $isFirstTime = $user->profile === null;
        // 既存のプロフィールがあれば更新、なければ新規作成
        // （検索条件は空配列、すでにリレーションでuser_idが紐づいている）
        $profile = $user->profile()
            ->updateOrCreate([], $data);
        // ────────────────────
        // 条件に応じてリダイレクト先を分岐
        // ────────────────────
        return $isFirstTime
            ? redirect('/')   // 初回はトップページへ
            : redirect()->route('mypage', ['page' => 'sell']);  // 2回目以降はマイページへ
    }
}
