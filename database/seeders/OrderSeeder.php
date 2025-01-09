<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = Customer::all();
        $products = Product::all();

        foreach ($customers as $customer) {
            $order = Order::create([
                "customer_id" => $customer->id,
                "total_amount" => 0,
            ]);

            $totalAmount = 0;
            $items = $products->random(rand(1, 5));

            foreach ($items as $product) {
                $quantity = rand(1, 3);

                OrderItem::create([
                    "order_id" => $order->id,
                    "product_id" => $product->id,
                    "quantity" => $quantity,
                    "price" => $product->price,
                ]);

                $totalAmount += $product->price * $quantity;
            }

            $order->update(["total_amount" => $totalAmount]);
        }
    }
}
