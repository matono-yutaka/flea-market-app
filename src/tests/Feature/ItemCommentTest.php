<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Comment;

class ItemCommentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ログイン済みのユーザーはコメントを送信できる()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $response = $this->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])->actingAs($user)->post('/comment/' . $item->id, [
            'comment' => 'とても良い商品でした！',
        ]);

        $response->assertRedirect(); // リダイレクトされる想定
        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'comment' => 'とても良い商品でした！',
        ]);
    }

    /** @test */
    public function ログイン前のユーザーはコメントを送信できない()
    {
        $item = Item::factory()->create();

        $response = $this->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])->post('/comment/' . $item->id, [
            'comment' => 'ゲストユーザーのコメント',
        ]);

        $response->assertRedirect('/login'); // 未ログインならログイン画面にリダイレクト
        $this->assertDatabaseMissing('comments', [
            'comment' => 'ゲストユーザーのコメント',
        ]);
    }

    /** @test */
    public function コメントが入力されていない場合、バリデーションメッセージが表示される()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $response = $this->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])->actingAs($user)->post('/comment/' . $item->id, [
            'comment' => '', // 空文字
        ]);

        $response->assertSessionHasErrors(['comment']);
        $this->assertDatabaseCount('comments', 0);
    }

    /** @test */
    public function コメントが255字以上の場合、バリデーションメッセージが表示される()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $longComment = str_repeat('あ', 256);

        $response = $this->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])->actingAs($user)->post('/comment/' . $item->id, [
            'comment' => $longComment,
        ]);

        $response->assertSessionHasErrors(['comment']);
        $this->assertDatabaseCount('comments', 0);
    }
}
