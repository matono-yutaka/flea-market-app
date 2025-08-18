<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(), // 外部キーには関連Factoryを使うのが定番
            'name' => $this->faker->word(),
            'price' => $this->faker->numberBetween(1000, 10000),
            'brand_name' => $this->faker->company(),
            'description' => $this->faker->paragraph(),
            'image' => 'default.png', // ダミー画像でもOK
            'condition' => $this->faker->randomElement(['良好', '目立った傷や汚れなし', 'やや傷や汚れあり', '状態が悪い']),
        ];
    }
}
