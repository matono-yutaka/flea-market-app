<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Good;

class ItemSearchTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function 「商品名」で部分一致検索ができる()
    {
        $item1 = Item::factory()->create(['name' => 'レッドシューズ']);
        $item2 = Item::factory()->create(['name' => 'ブルーシューズ']);
        $item3 = Item::factory()->create(['name' => 'グリーンバッグ']);

        $response = $this->get('/?keyword=シューズ');

        $response->assertSee('レッドシューズ');
        $response->assertSee('ブルーシューズ');
        $response->assertDontSee('グリーンバッグ');
    }

    /** @test */
    public function 検索状態がマイリストでも保持されている()
    {
        $user = User::factory()->create();

        $matchItem = Item::factory()->create(['name' => '赤いシャツ']);
        $nonMatchItem = Item::factory()->create(['name' => '青いズボン']);
        $notLikedItem = Item::factory()->create(['name' => '黄色い帽子']);

        // いいねした商品だけを対象にする
        Good::factory()->create([
            'user_id' => $user->id,
            'item_id' => $matchItem->id,
        ]);

        Good::factory()->create([
            'user_id' => $user->id,
            'item_id' => $nonMatchItem->id,
        ]);

        $response = $this->actingAs($user)->get('/?tab=mylist&keyword=シャツ');

        $response->assertSee('赤いシャツ');
        $response->assertDontSee('青いズボン');
        $response->assertDontSee('黄色い帽子');
    }

}
