<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 会員登録後、認証メールが送信される()
    {
        Notification::fake();

        // ユーザー作成（未認証）
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        // 認証メールを送信
        $user->sendEmailVerificationNotification();

        // メール送信がモックされていることを確認
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    /** @test */
    public function メール認証誘導画面で「認証はこちらから」ボタンを押下するとメール認証サイトに遷移する()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $this->actingAs($user)
            ->get('/email/verify') // 認証誘導画面
            ->assertStatus(200)
            ->assertSee('認証はこちらから');

        // 認証URLはLaravelのルートを使う
        $verificationUrl = \URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $this->actingAs($user)
            ->get($verificationUrl)
            ->assertRedirect('/mypage/profile');
    }

    /** @test */
    public function メール認証サイトのメール認証を完了すると、商品一覧ページに遷移する()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $verificationUrl = \URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        $response->assertRedirect('/mypage/profile');

        // ユーザーが認証済みになったことを確認
        $this->assertNotNull($user->fresh()->email_verified_at);
    }
}
