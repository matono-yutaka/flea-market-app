<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Good;
use App\Models\Comment;
use App\Models\Category;


class ItemDetailTest extends TestCase
{
    use RefreshDatabase;

   /** @test */
    public function 必要な情報が表示される()
    {
        $item = Item::factory()->create([
            'image' => 'default.png',
            'name' => 'テスト商品',
            'brand_name' => 'テストブランド',
            'price' => 1980,
            'description' => 'これはテスト用の商品です。',
            'condition' => '良好',
        ]);

        $category1 = Category::factory()->create(['name' => '本']);
        $category2 = Category::factory()->create(['name' => 'ゲーム']);
        $item->categories()->attach([$category1->id, $category2->id]);

        $user = User::factory()->hasProfile(['image' => 'sample.png'])->create();
        $comment = Comment::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'comment' => '良い商品ですね',
        ]);

        Good::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $response = $this->get('/item/' . $item->id);

        $response->assertStatus(200);
        $response->assertSee('default.png');
        $response->assertSee('テスト商品');
        $response->assertSee('テストブランド');
        $response->assertSee('1,980');
        $response->assertSee('良好');
        $response->assertSee('これはテスト用の商品です。');
        $response->assertSee('本');
        $response->assertSee('ゲーム');
        $response->assertSee('良い商品ですね');
        $response->assertSee($user->name); // コメントしたユーザー名
    }

    /** @test */
    public function 複数選択されたカテゴリが表示されているか()
    {
        $item = Item::factory()->create();

        $categories = Category::factory()->count(3)->create();
        $item->categories()->attach($categories->pluck('id'));

        $response = $this->get('/item/' . $item->id);

        foreach ($categories as $category) {
            $response->assertSee($category->name);
        }
    }


}
