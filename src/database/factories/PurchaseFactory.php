<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Item;
use App\Models\ShippingAddress;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'item_id' => Item::factory(),
            'shipping_address_id' => ShippingAddress::factory(),
        ];
    }
}
