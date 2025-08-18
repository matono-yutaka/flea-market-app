<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\ShippingAddress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemIndexTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 全商品を取得できる()
    {
        $user = User::factory()->create();
        Item::factory()->count(3)->create();

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        // 商品一覧に登録された商品の名前が含まれていること
        foreach (Item::all() as $item) {
            $response->assertSee($item->name);
        }
    }

    /** @test */
    public function 購入済み商品は「Sold」と表示される()
    {
        $user = User::factory()->create();

        $item = Item::factory()->create();

        Purchase::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'shipping_address_id' => ShippingAddress::factory()->create(['user_id' => $user->id])->id,
        ]);

        $response = $this->actingAs($user)->get('/');
        $this->assertDatabaseHas('purchases', [
            'item_id' => $item->id,
        ]);

        $response->assertSee('Sold');
    }

    /** @test */
    public function 自分が出品した商品は表示されない()
    {
        $user = User::factory()->create();

        // 自分の商品（表示されない）
        Item::factory()->create([
            'user_id' => $user->id,
            'name' => '自分の商品',
        ]);

        // 他人の商品（表示される）
        Item::factory()->create([
            'name' => '他人の商品',
        ]);

        $response = $this->actingAs($user)->get('/');

        $response->assertDontSee('自分の商品');
        $response->assertSee('他人の商品');
    }
}
