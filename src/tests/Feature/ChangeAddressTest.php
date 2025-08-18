<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\UserProfile;
use App\Models\ShippingAddress;

class ChangeAddressTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 送付先住所変更画面にて登録した住所が商品購入画面に反映されている()
    {
    $user = User::factory()->create();
    $item = Item::factory()->create();

    $this->actingAs($user);

    $response = $this->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])->post("/purchase/address/{$item->id}", [
        'post_code' => '123-4567',
        'address' => '東京都',
        'building' => 'テストビル101'
    ]);

    $response->assertRedirect("/purchase/{$item->id}");

    $this->get("/purchase/{$item->id}")
        ->assertSee('123-4567')
        ->assertSee('東京都')
        ->assertSee('テストビル101');
    }

    /** @test */
    public function 購入した商品に送付先住所が紐づいて登録される()
    {
    $user = User::factory()->create();
    $item = Item::factory()->create();

    // プロフィール住所が必要なので、Profile も作成しておく
    UserProfile::factory()->create(['user_id' => $user->id]);
    // ログイン状態にする
    $this->actingAs($user);

    // 成功処理を叩く（支払い後の成功ページ）
    $response = $this->get("/purchase/success/{$item->id}");

    // shipping_addresses にデータが入っていることを確認
    $this->assertDatabaseHas('shipping_addresses', [
        'user_id' => $user->id,
        'item_id' => $item->id,
    ]);

    // purchases テーブルに、shipping_address_id が紐づいていることを確認
    $shipping = ShippingAddress::where('user_id', $user->id)
        ->where('item_id', $item->id)
        ->first();

    $this->assertDatabaseHas('purchases', [
        'user_id' => $user->id,
        'item_id' => $item->id,
        'shipping_address_id' => $shipping->id,
    ]);

    // リダイレクトが正しくされているか
    $response->assertRedirect('/');
    }
}