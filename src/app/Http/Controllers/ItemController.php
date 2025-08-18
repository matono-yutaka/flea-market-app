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
    public function index(Request $request)
    {
        $tab = $request->query('tab');
        $keyword = $request->query('keyword');
        $user = auth()->user();

        if ($tab === 'mylist') {
            if($user){
                $items = $user->goods()
                ->where('items.user_id', '!=', $user->id);
                // キーワードがあればフィルター
                if (!empty($keyword)) {
                $items->where('items.name', 'like', '%' . $keyword . '%');
                }
                $items = $items->get();

            }else{
                $items = collect();//空のコレクション
            }

            $purchasedItemIds = Purchase::pluck('item_id')->toArray();

            return view('index_mylist', compact('items', 'tab', 'purchasedItemIds'));
        }

        $userId = auth()->id();
        $items = Item::where('user_id', '!=', $userId);

        if (!empty($keyword)) {
            $items->where('name', 'like', '%' . $keyword . '%');
        }

        $items = $items->get();

        $purchasedItemIds = Purchase::pluck('item_id')->toArray();

        return view('index', compact('items', 'tab', 'purchasedItemIds'));
    }


    public function sell()
    {
        $categories = Category::all();

        return view('product.sell', compact('categories'));
    }


    public function item($id)
    {
        $item = Item::with(['likedUsers', 'comments', 'categories'])->findOrFail($id);
        $comments = $item->comments;

        return view('product.item',compact('item', 'comments',));
    }


    public function purchase($itemId)
    {
        $user = auth()->user();
        $item = Item::find($itemId);
        $profile = $user->profile;

        $shippingAddress = ShippingAddress::where('user_id', auth()->id())->where('item_id', $itemId)->latest()->first();

        return view('product.purchase',compact('item', 'profile','shippingAddress'));
    }


    public function address($id)
    {
        $user = auth()->user();
        $item = Item::find($id);

        return view('product.purchase_address', compact('item'));
    }


    public function update(AddressRequest $request, $itemId)
    {
        $user = auth()->user();
        $item = Item::find($itemId);

        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $data['item_id'] = $itemId;

        ShippingAddress::create($data);

        return redirect('/purchase/' . $itemId);
    }


    public function exhibit(ExhibitionRequest $request)
    {
        $data = $request->only('name', 'price', 'brand_name', 'description', 'condition');
        $data['user_id'] = auth()->id();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('images/', 'public');
            session()->put('temp_item_image_path', $path);
            $data['image'] = basename($path);
        } elseif ($request->filled('temp_item_image_path')) {
        $tempPath = $request->input('temp_item_image_path');
        $newPath = str_replace('temp/', 'images/', $tempPath);

            if (\Storage::disk('public')->exists($tempPath)) {
                \Storage::disk('public')->move($tempPath, $newPath);
                $data['image'] = basename($newPath);
                session()->forget('temp_item_image_path');
            } else {
            return back()->withErrors(['image' => '画像ファイルが見つかりません'])->withInput();
            }
        } else {
        return back()->withErrors(['image' => '画像をアップロードしてください'])->withInput();
        }

        $item = Item::create($data);
        $item->categories()->attach($request->input('categories'));

        return redirect()->route('mypage', ['page' => 'sell']);
    }


    public function store(CommentRequest $request,$itemId )
    {
        $validated = $request->validated();
        $validated['user_id'] = auth()->id(); // user_id を追加
        $validated['item_id'] = $itemId;

        Comment::create($validated); // 1つの配列だけしか渡せない
        return redirect()->back();
    }


    public function good($itemId)
    {
        $user = auth()->user();
        $item = Item::findOrFail($itemId);

        if ($user->goods()->where('item_id', $itemId)->exists()) {
            // すでにいいねしてたら解除
            $user->goods()->detach($itemId);
            $message = 'いいねを取り消しました😭';
        } else {
            // いいねしてなければ追加
            $user->goods()->attach($itemId);
            $message = 'いいねしました😀';
        }
        return redirect()->back()->with('message', $message);
    }
}

