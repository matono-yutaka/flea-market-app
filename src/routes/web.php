<?php

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ChatController;
use Illuminate\Http\Request;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


//会員登録用
Route::post('/register', [AuthController::class, 'register']);

//未ログイン可
Route::get('/', [ItemController::class, 'index'])->name('index');

//未ログイン可
Route::get('/item/{itemId}', [ItemController::class, 'item'])->name('purchase.detail');

//ログイン用
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth')->group(function () {
    Route::post('/profile/save', [ProfileController::class, 'saveProfile'])->name('save.profile');

    Route::post('/purchase/address/{itemId}', [ItemController::class, 'update'])->name('purchase.address.update');

    Route::get('/purchase/{itemId}', [ItemController::class, 'purchase']);

    Route::get('/purchase/address/{itemId}', [ItemController::class, 'address']);

    Route::get('mypage/profile', [ProfileController::class, 'edit'])->name('profile.edit');

    Route::get('/sell', [ItemController::class, 'sell'])->name('sell.sell');

    Route::get('/mypage', [ProfileController::class, 'mypage'])->name('mypage');

    Route::post('/exhibit', [ItemController::class, 'exhibit'])->name('exhibit.exhibit');

    Route::post('/comment/{itemId}', [ItemController::class, 'store']);

    Route::post('/good/{itemId}', [ItemController::class, 'good']);

    Route::post('/checkout/{itemId}', [PaymentController::class, 'checkout'])->name('payment.checkout');

    Route::post('/konbini/{itemId}', [PaymentController::class, 'konbiniCheckout'])->name('konbini.checkout');

    Route::get('/purchase/success/{itemId}', [PaymentController::class, 'handleSuccess'])->name('purchase.success');

    Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook']);
    // 以下プロテスト⬇︎
    // 取引チャット画面
    Route::get('/chat/{id}', [ChatController::class, 'chat'])->name('chat.chat');
    // 取引チャット処理
    Route::post('/chat/{id}/message', [ChatController::class, 'message']);
    // 取引チャット編集処理
    Route::post('/chat/{id}/edit', [ChatController::class, 'edit']);
    // 取引チャット削除処理
    Route::delete('/chat/{id}/delete', [ChatController::class, 'destroy']);
    // modal
    Route::post('/trade/complete/store/{id}', [ChatController::class, 'store']);
});

// メール認証を促す画面
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// メール内のリンクをクリックしたときの処理
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill(); // 認証完了処理
    return redirect('/mypage/profile'); // 認証後のリダイレクト先
})->middleware(['auth', 'signed'])->name('verification.verify');

// 再送信リクエスト
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', '認証メールを再送しました！');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

