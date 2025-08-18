<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Good;

class ItemLikeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function いいねアイコンを押下することによって、いいねした商品として登録することができる()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])->actingAs($user)->post('/good/' . $item->id);

        $this->assertDatabaseHas('goods', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }

    /** @test */
    public function 追加済みのアイコンは色が変化する()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // 明示的に user_id を渡す
        Good::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $response = $this->actingAs($user)->get('/item/' . $item->id);
        $response->assertSee('good-yes');
    }

    /** @test */
    public function 再度いいねアイコンを押下することによって、いいねを解除することができる()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // 1回目：いいねする
        $this->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])->actingAs($user)
            ->post('/good/' . $item->id);

        // 2回目：解除する
        $this->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])->actingAs($user)
            ->post('/good/' . $item->id);

        $this->assertDatabaseMissing('goods', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }
}
