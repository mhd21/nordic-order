<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition()
    {
        return [
            "customer_id" => Customer::factory(),
            "total_amount" => 0, // Calculated in the seeder
            "status" => "confirmed",
            "created_at" => now(),
            "updated_at" => now(),
        ];
    }
}
