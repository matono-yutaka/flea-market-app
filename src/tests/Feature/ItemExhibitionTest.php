<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ItemExhibitionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 商品出品画面にて必要な情報が保存できること()
    {
        Storage::fake('public');

        // ユーザー作成
        $user = User::factory()->create();

        // テスト用カテゴリを1つだけ作成
        $category = Category::factory()->create();

        // 画像ファイル（ダミー）
        $image = UploadedFile::fake()->create('test.jpg', 200);

        // 出品フォームデータ
        $formData = [
            'categories' => [$category->id], // ← 作ったカテゴリIDを明示的に渡す
            'condition' => '良好',
            'name' => 'テスト商品',
            'brand_name' => 'テストブランド',
            'description' => 'これはテスト商品の説明です。',
            'price' => 5000,
            'image' => $image,
        ];

        // 出品処理を実行
        $response = $this->actingAs($user)
            ->post('/exhibit', $formData);

        $response->assertStatus(302);

        // itemsテーブルの確認
        $this->assertDatabaseHas('items', [
            'condition' => '良好',
            'name' => 'テスト商品',
            'brand_name' => 'テストブランド',
            'description' => 'これはテスト商品の説明です。',
            'price' => 5000,
            'user_id' => $user->id,
        ]);

        // 作成したアイテムを取得
        $item = Item::first();

        // 中間テーブルの確認（カテゴリIDも確実に一致）
        $this->assertDatabaseHas('category_item', [
            'item_id' => $item->id,
            'category_id' => $category->id,
        ]);

        // 画像の確認
        Storage::disk('public')->assertExists('images/' . $formData['image']->hashName());
    }
}
