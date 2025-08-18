<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProfileRequest;
use App\Models\UserProfile;
use App\Models\Item;
use App\Models\Purchase;


class ProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user();
        $profile = $user->profile  ?? new UserProfile;

        return view('profile.save', compact('profile', 'user'));
    }


    public function mypage(Request $request)
    {
        $page = $request->query('page');
        $user = auth()->user();
        $profile = $user->profile  ?? new UserProfile;

        if ($page === 'sell'){
            $items = Item::where('user_id', $user->id)->get();

            $purchasedItemIds = Purchase::pluck('item_id')->toArray();

            return view('profile.mypage', compact('profile', 'items', 'purchasedItemIds', 'page'));

        } else{
            $purchases = Purchase::where('user_id', $user->id)->get();

            return view('profile.mypage_purchase', compact('profile', 'purchases','page'));
        }
    }


    public function saveProfile(ProfileRequest $request)
    {
        $user = auth()->user();

        $data = $request->only('name', 'post_code', 'address', 'building'); // 入力データを取得
        $data['user_id'] = auth()->id();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('images/national_flag', 'public');
            session()->put('temp_profile_image_path', $path);
            $data['image'] = basename($path);

        } elseif ($request->filled('temp_profile_image_path')) {
            $tempPath = $request->input('temp_profile_image_path');
            // フォルダを直接指定して basename で組み立て（スラッシュ問題回避）
            $newPath = 'images/national_flag/' . basename($tempPath);

            if (\Storage::disk('public')->exists($tempPath) && !\Storage::disk('public')->exists($newPath)) {
                \Storage::disk('public')->move($tempPath, $newPath);

                $data['image'] = basename($newPath);
                session()->forget('temp_profile_image_path');
            }
        }
        // プロフィールが既にあるかどうかで初回か更新かを判定
        $isFirstTime = $user->profile === null;
        // updateOrCreate で保存
        $profile = $user->profile()
            ->updateOrCreate([], $data);
        // ────────────────────
        // 条件に応じてリダイレクト先を分岐
        // ────────────────────
        return $isFirstTime
            ? redirect('/')    // 初回はトップページへ
            : redirect()->route('mypage', ['page' => 'sell']);  // 2 回目以降はマイページへ
    }
}
