<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 名前が入力されていない場合、バリデーションメッセージが表示される()
    {
        $response = $this->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])->post('/register', [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['name']);
        $this->get('/register')->assertSee('お名前を入力してください');
    }

    /** @test */
    public function メールアドレスが入力されていない場合、バリデーションメッセージが表示される()
    {
        $response = $this->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])->post('/register', [
            'name' => 'テストユーザー',
            'email' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->get('/register')->assertSee('メールアドレスを入力してください');
    }

    /** @test */
    public function パスワードが入力されていない場合、バリデーションメッセージが表示される()
    {
        $response = $this->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->get('/register')->assertSee('パスワードを入力してください');
    }

    /** @test */
    public function パスワードが7文字以下の場合、バリデーションメッセージが表示される()
    {
        $response = $this->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'short7',
            'password_confirmation' => 'short7',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->get('/register')->assertSee('パスワードは8文字以上で入力してください');
    }

    /** @test */
    public function パスワードが確認用パスワードと一致しない場合、バリデーションメッセージが表示される()
    {
        $response = $this->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'mismatch456',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->get('/register')->assertSee('パスワードと一致しません');
    }

    /** @test */
    public function 全ての項目が入力されている場合、会員情報が登録され、プロフィール設定画面に遷移される()
    {
        $response = $this->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/email/verify'); // 登録後のリダイレクト先に応じて変更
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);
    }
}
