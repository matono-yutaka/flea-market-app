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
    public function register(RegisterRequest $request)
    {
        $user=User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password)
        ]);
        Auth::login($user);
         // メール認証メール送信（必ず必要）
        $user->sendEmailVerificationNotification();

        return redirect('/email/verify');
    }


    public function login(LoginRequest $request)
    {
        $credentials=$request->only('email', 'password');

        if(Auth::attempt($credentials)){
           //Auth::user() でログイン済みユーザーを取得
            $user = Auth::user();

            if (!$user->hasVerifiedEmail()) {
                return redirect()->route('verification.notice');
            }

            return redirect('/');
        }
        // ログイン失敗時
        return back()->withInput()->withErrors([
        'email' => 'ログイン情報が登録されていません',
        ]);
    }
}

