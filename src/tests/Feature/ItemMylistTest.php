<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Item;
use App\Models\Good;
use App\Models\Purchase;
use App\Models\ShippingAddress;

class ItemMylistTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function いいねした商品だけが表示される()
    {
        $user = User::factory()->create();

        $likedItem = Item::factory()->create(['name' => 'いいね済み']);
        $unlikedItem = Item::factory()->create(['name' => 'いいねしてない']);

        // いいねテーブルにだけ登録
        Good::factory()->create([
            'user_id' => $user->id,
            'item_id' => $likedItem->id,
        ]);

        $response = $this->actingAs($user)->get('/?tab=mylist');

        $response->assertSee('いいね済み');
        $response->assertDontSee('いいねしてない');
    }

    /** @test */
    public function 購入済み商品は「Sold」と表示される()
    {
        $user = User::factory()->create();

        $item = Item::factory()->create(['name' => '購入済み商品']);

        $shippingAddress = ShippingAddress::factory()->create([
            'user_id' => $user->id,
        ]);

        Purchase::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'shipping_address_id' => $shippingAddress->id,
        ]);

        Good::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $response = $this->actingAs($user)->get('/?tab=mylist');

        $response->assertSee('Sold');
    }

    /** @test */
    public function 未認証の場合は何も表示されない()
    {

        $response = $this->get('/?tab=mylist');

        // ステータスコードが200であること
        $response->assertStatus(200);

        // 商品を表示する要素（例：item-cardクラス）が含まれないことを確認
        $response->assertDontSee('item-card');
    }
}
