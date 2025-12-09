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
        // 先に users を作成(プロテストでは不要)
    //\App\Models\User::factory()->create([
        //'id' => 1, // 明示的に ID を指定する
        //'name' => 'admin',
        //'email' => 'test@example.com',
        //'password' => bcrypt('password'),
    //]);

        $this->call(UsersTableSeeder::class);
        $this->call(ItemsTableSeeder::class);
        $this->call(CategoriesTableSeeder::class);

    }
}
