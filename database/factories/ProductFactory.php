<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            "name" => $this->faker->word(),
            "price" => $this->faker->randomFloat(2, 1, 100), // Prices between 1 and 100
            "stock" => $this->faker->numberBetween(10, 100),
            "created_at" => now(),
            "updated_at" => now(),
        ];
    }
}
