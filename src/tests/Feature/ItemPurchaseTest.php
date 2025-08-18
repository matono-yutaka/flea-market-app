<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\ShippingAddress;

class ItemPurchaseTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 「購入する」ボタンを押下すると購入が完了する()
    {
        $user = User::factory()->create();
        UserProfile::factory()->create(['user_id' => $user->id]);

        $item = Item::factory()->create();

        // ShippingAddress はまだ存在しない前提
        $response = $this
            ->actingAs($user)
            ->get('/purchase/success/' . $item->id);

        $response->assertRedirect('/');

        $shipping = ShippingAddress::where('user_id', $user->id)
            ->where('item_id', $item->id)
            ->first();

        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'shipping_address_id' => $shipping->id,
        ]);
    }

    /** @test */
    public function 購入した商品は商品一覧画面にて「sold」と表示される()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $shippingAddress = ShippingAddress::factory()->create();

        // 購入レコードを作成
        Purchase::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'shipping_address_id' => $shippingAddress->id,
        ]);

        $response = $this->get('/'); // トップ画面や商品一覧画面のURL

        $response->assertSee('Sold');
    }

    /** @test */
    public function 「プロフィール／購入した商品一覧」に追加されている()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $shipping = ShippingAddress::factory()->create();

        // 購入レコードを作成
        Purchase::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'shipping_address_id' => $shipping->id,
        ]);

        $response = $this->actingAs($user)->get('/mypage?page=buy'); // 購入履歴ページ

        $response->assertSee($item->name);
    }
}
