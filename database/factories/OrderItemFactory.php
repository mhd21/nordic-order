<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "order_id" => Order::factory(),
            "product_id" => Product::factory(),
            "quantity" => $this->faker->numberBetween(1, 5),
            "price" => $this->faker->randomFloat(2, 1, 100), // Random price between 1.00 and 100.00
        ];
    }
}
