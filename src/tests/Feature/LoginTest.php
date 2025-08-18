<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function メールアドレスが入力されていない場合、バリデーションメッセージが表示される()
    {
        $response = $this->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])->post('/login', [
            'email' => '',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->get('/login')->assertSee('メールアドレスを入力してください');
    }

     /** @test */
    public function パスワードが入力されていない場合、バリデーションメッセージが表示される()
    {
        $response = $this->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])->post('/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->get('/login')->assertSee('パスワードを入力してください');
    }

    /** @test */
    public function 入力情報が間違っている場合、バリデーションメッセージが表示される()
    {
        $response = $this->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])->post('/login', [
            'email' => 'fake@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors(['email']); // Laravel BreezeやFortifyの挙動により異なる
        $this->get('/login')->assertSee('ログイン情報が登録されていません');
    }

    /** @test */
    public function 正しい情報が入力された場合、ログイン処理が実行される()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $response = $this->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect('/'); // 遷移先に合わせて変更
        $this->assertAuthenticatedAs($user);
    }
}
