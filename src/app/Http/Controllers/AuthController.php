<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\ProfileRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // 新規登録処理
    public function register(RegisterRequest $request)
    {
        // ユーザー登録処理
        $user=User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password)
        ]);
        // 登録完了後、いちいちログインフォームに行かなくてもいいようになる便利な処理
        Auth::login($user);
        // 認証メール送信（必ず必要）
        $user->sendEmailVerificationNotification();

        return redirect('/email/verify');
    }


    // ログイン処理
    public function login(LoginRequest $request)
    {
        // フォーム入力情報取得
        $credentials=$request->only('email', 'password');

        // ログイン試行
        if(Auth::attempt($credentials)){
            // 現在ログイン中のユーザー情報を取得
            $user = Auth::user();
            // メール未認証の場合,メール認証画面へ
            if (!$user->hasVerifiedEmail()) {
                return redirect()->route('verification.notice');
            }
            // 認証済みの場合はトップページへ
            return redirect('/');
        }
        // ログイン失敗時
        return back()->withInput()->withErrors([
        'email' => 'ログイン情報が登録されていません',
        ]);
    }
}

