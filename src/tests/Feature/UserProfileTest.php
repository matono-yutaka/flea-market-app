<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 必要な情報が取得できる()
    {
        // ユーザーとプロフィール作成
        $user = User::factory()->create();

        $profile = UserProfile::factory()->create([
            'name' => 'テストユーザー',
            'user_id' => $user->id,
            'image' => 'profile.jpg',
        ]);

        // 出品商品作成
        $item = Item::factory()->create([
            'user_id' => $user->id,
            'name' => '出品テスト商品',
        ]);

        // 購入商品作成
        $purchaseItem = Item::factory()->create();
        Purchase::factory()->create([
            'user_id' => $user->id,
            'item_id' => $purchaseItem->id,
        ]);

        // ログインしてプロフィールページ（出品一覧）を確認
        $responseSell = $this->actingAs($user)->get('/mypage?page=sell');
        $responseSell->assertStatus(200);
        $responseSell->assertSee('テストユーザー');
        $responseSell->assertSee('profile.jpg');
        $responseSell->assertSee('出品テスト商品');

        // プロフィールページ（購入一覧）を確認
        $responsePurchase = $this->actingAs($user)->get('/mypage?page=buy');
        $responsePurchase->assertStatus(200);
        $responsePurchase->assertSee($purchaseItem->name);
    }
}
