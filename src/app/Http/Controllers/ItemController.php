<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AddressRequest;
use App\Http\Requests\PurchaseRequest;
use App\Http\Requests\ExhibitionRequest;
use App\Http\Requests\CommentRequest;
use App\Models\Item;
use App\Models\UserProfile;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Purchase;
use App\Models\ShippingAddress;



class ItemController extends Controller
{
    // トップページ表示
    public function index(Request $request)
    {
        // クエリパラメータからtab情報取得
        $tab = $request->query('tab');
        // クエリパラメータからkeyword取得
        $keyword = $request->query('keyword');
        // ログイン中ユーザー情報取得
        $user = auth()->user();

        // --- マイリストタブ ---
        if ($tab === 'mylist') {
            // ログインユーザーの場合
            if($user){
                // ログインユーザーがいいねした商品
                $items = $user->goods()
                // ログインユーザーが出品した商品以外の商品
                ->where('items.user_id', '!=', $user->id);
                // キーワードがあればフィルター
                if (!empty($keyword)) {
                $items->where('items.name', 'like', '%' . $keyword . '%');
                }
                // 条件に合う商品を取得
                $items = $items->get();

            }else{
                // 未ログイン時は空のコレクションを返す
                $items = collect();
            }

            // 購入済みの商品のID一覧を取得（購入済みアイテムを判定するために使用）
            $purchasedItemIds = Purchase::pluck('item_id')->toArray();

            return view('index_mylist', compact('items', 'tab', 'purchasedItemIds'));
        }

        // --- おすすめタブ ---
        $userId = auth()->id();
        // ログインユーザー以外が出品した商品を取得
        $items = Item::where('user_id', '!=', $userId);

        // キーワード検索フィルター
        if (!empty($keyword)) {
            $items->where('name', 'like', '%' . $keyword . '%');
        }
        // 条件に合う商品を取得
        $items = $items->get();
        // 購入済みの商品のID一覧を取得（購入済みアイテムを判定するために使用）
        $purchasedItemIds = Purchase::pluck('item_id')->toArray();

        return view('index', compact('items', 'tab', 'purchasedItemIds'));
    }


    // 商品出品画面表示
    public function sell()
    {
        // カテゴリーを取得
        $categories = Category::all();

        return view('product.sell', compact('categories'));
    }


    // 商品詳細画面表示
    public function item($id)
    {
        // 対象のidの商品と,その商品に関連するいいねしたユーザー,コメント,カテゴリーを同時に取得
        $item = Item::with(['likedUsers', 'comments', 'categories'])->findOrFail($id);
        // bladeで$commentsを直接使うため
        $comments = $item->comments;

        return view('product.item',compact('item', 'comments',));
    }


    // 商品購入画面表示
    public function purchase($itemId)
    {
        // ログイン中ユーザー情報取得
        $user = auth()->user();
        // 対象のitemIdの商品取得
        $item = Item::find($itemId);
        // ログイン中ユーザーのプロフィール情報取得
        $profile = $user->profile;

        // ログイン中ユーザーの配送先住所取得
        $shippingAddress = ShippingAddress::where('user_id', auth()->id())
        // 対象のitemIdの商品の配送先住所取得
        ->where('item_id', $itemId)
        // 最新の配送先住所一件取得
        ->latest()->first();

        return view('product.purchase',compact('item', 'profile','shippingAddress'));
    }


    // 配送先住所変更画面表示
    public function address($id)
    {
        // どうやら必要ないらしい(viewで使ってない)
        $user = auth()->user();
        // 対象の商品を取得（フォームのactionで使用）
        $item = Item::find($id);

        return view('product.purchase_address', compact('item'));
    }


    // 配送先住所変更処理
    public function update(AddressRequest $request, $itemId)
    {
        // どうやら不要らしい
        $user = auth()->user();
        // 下の登録処理で使われていないのでなくてもいいが、商品の存在チェックのために残しておくのもありらしい
        $item = Item::findOrFail($itemId);

        // フォーム入力情報取得(バリデーション済み)
        $data = $request->validated();
        // ログイン中ユーザーのid追加
        $data['user_id'] = auth()->id();
        // 対象の商品のid追加
        $data['item_id'] = $itemId;

        // 配送先住所登録
        ShippingAddress::create($data);

        // 実際のURLへredirect,$itemIdには3などの数字が入る(/purchase/3)
        return redirect('/purchase/' . $itemId);
    }


    // 商品出品処理
    public function exhibit(ExhibitionRequest $request)
    {
        // 入力フォームから情報取得(必要なカラムのみ)
        $data = $request->only('name', 'price', 'brand_name', 'description', 'condition');
        // ログイン中ユーザーのid追加
        $data['user_id'] = auth()->id();

        // 画像アップロード処理(ExhibitionRequest参照)
        // 一時保存された画像（新規アップロード or バリデーション戻り）がある場合
        if (session()->has('temp_item_image_path')) {
            // セッションから一時保存パス取得
            $tempPath = session('temp_item_image_path');
            // 一時フォルダから正式な保存フォルダに移動するための新パスを生成
            $newPath = 'images/' . basename($tempPath);
            // temp → images へ移動
            \Storage::disk('public')->move($tempPath, $newPath);

            // 保存データにファイル名を設定
            $data['image'] = basename($newPath);
        } else {
        // 新規画像も一時パスもない場合
        return back()->withErrors(['image' => '画像をアップロードしてください'])->withInput();
        }

        // 一時パス情報をセッションから削除
        session()->forget('temp_item_image_path');

        // 商品出品登録
        $item = Item::create($data);
        // 出品商品に関連するカテゴリー登録(中間テーブル)
        $item->categories()->attach($request->input('categories'));
        // ☝️二つのコードの順番大事、商品出品登録しないとitem_idが出来ない→中間テーブルに登録できない

        // 出品完了後にマイページ（出品一覧）へリダイレクト
        return redirect()->route('mypage', ['page' => 'sell']);
    }


    // コメント登録処理
    public function store(CommentRequest $request,$itemId )
    {
        // バリデーション済み入力情報取得
        $validated = $request->validated();
        // ログイン中ユーザーのid追加
        $validated['user_id'] = auth()->id();
        // 対象のitem_id追加
        $validated['item_id'] = $itemId;

        // コメントをDBに登録
        // create() は連想配列を受け取り、新しいレコードを作成する
        Comment::create($validated); // 1つの配列だけしか渡せない
        return redirect()->back();
    }


    // グッドボタン処理
    public function good($itemId)
    {
        // ログイン中ユーザーを取得
        $user = auth()->user();
        // 対象の商品を取得(存在確認:存在しない商品にいいねしないため)
        Item::findOrFail($itemId);

        // すでに「いいね」しているか確認
        if ($user->goods()->where('item_id', $itemId)->exists()) {
            // 「いいね」してたら解除（中間テーブル）
            $user->goods()->detach($itemId);
            $message = 'いいねを取り消しました😭';
        } else {
            // 「いいね」してなければ追加（中間テーブル）
            $user->goods()->attach($itemId);
            $message = 'いいねしました😀';
        }
        // 元のページに戻ってメッセージを表示
        return redirect()->back()->with('message', $message);
    }
}

