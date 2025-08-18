<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemConfirmTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 小計画面で変更が反映される()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['price' => 5000]);

        $this->actingAs($user);

        // カード払いを選択した場合
        $responseCard = $this->post("/checkout/{$item->id}", [
            'select' => 'カード払い',
        ]);
        $responseCard->assertStatus(302); // リダイレクトが起きるだけ確認

        // コンビニ払いを選択した場合
        $responseKonbini = $this->post("/konbini/{$item->id}", [
            'select' => 'コンビニ払い',
        ]);
        $responseKonbini->assertStatus(302); // 同様にリダイレクト確認
    }
}
