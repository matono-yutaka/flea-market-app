<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserProfileEditTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 各項目の初期値が正しく表示されている()
    {
        // ユーザー作成
        $user = User::factory()->create();

        // プロフィール作成（初期値をセット）
        $profile = UserProfile::factory()->create([
            'user_id' => $user->id,
            'name' => 'テストユーザー',
            'post_code' => '123-4567',
            'address' => '東京都渋谷区テスト町1-2-3',
            'image' => 'profile.jpg',
        ]);

        // ログインしてプロフィール編集ページへアクセス
        $response = $this->actingAs($user)->get('/mypage/profile');

        $response->assertStatus(200);

        // フォームに初期値が含まれているか確認
        $response->assertSee('テストユーザー', false);
        $response->assertSee('123-4567', false);
        $response->assertSee('東京都渋谷区テスト町1-2-3', false);
        $response->assertSee('profile.jpg', false);
    }
}
