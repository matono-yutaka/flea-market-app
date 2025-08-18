<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // 先に users を作成
    \App\Models\User::factory()->create([
        'id' => 1, // 明示的に ID を指定する（任意）
        'name' => 'admin',
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
    ]);

        $this->call(ItemsTableSeeder::class);
        $this->call(CategoriesTableSeeder::class);

    }
}
