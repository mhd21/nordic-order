<?php

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('retrieves a paginated list of a customer\'s orders', function () {
    $customer = Customer::factory()->create();

    $products = Product::factory()->count(3)->create();

    // Create 30 orders for the customer
    foreach (range(1, 30) as $i) {
        $order = Order::factory()->create(["customer_id" => $customer->id]);

        foreach ($products as $product) {
            $quantity = rand(1, 5); // Random quantity for each product
            OrderItem::factory()->create([
                "order_id" => $order->id,
                "product_id" => $product->id,
                "quantity" => $quantity,
                "price" => $product->price,
            ]);
        }
    }

    $response = $this->getJson(
        route("customers.orders", [
            "id" => $customer->id,
            "page" => 1,
            "per_page" => 15,
        ])
    );

    $response->assertStatus(200);

    $response->assertJsonStructure([
        "data" => [
            "*" => [
                "id",
                "customer_id",
                "total_amount",
                "created_at",
                "updated_at",
            ],
        ],
        "current_page",
        "from",
        "last_page",
        "links",
        "path",
        "per_page",
        "to",
        "total",
    ]);

    $this->assertCount(15, $response->json("data"));

    $this->assertEquals(1, $response->json("current_page"));
    $this->assertEquals(15, $response->json("per_page"));
    $this->assertEquals(30, $response->json("total"));
});

it('retrieves the second page of a customer\'s orders', function () {
    $customer = Customer::factory()->create();

    $products = Product::factory()->count(3)->create();

    // Create 30 orders for the customer
    foreach (range(1, 30) as $i) {
        $order = Order::factory()->create(["customer_id" => $customer->id]);

        foreach ($products as $product) {
            $quantity = rand(1, 5); // Random quantity for each product
            OrderItem::factory()->create([
                "order_id" => $order->id,
                "product_id" => $product->id,
                "quantity" => $quantity,
                "price" => $product->price,
            ]);
        }
    }

    $response = $this->getJson(
        route("customers.orders", [
            "id" => $customer->id,
            "page" => 2,
            "per_page" => 15,
        ])
    );

    $response->assertStatus(200);

    $this->assertEquals(2, $response->json("current_page"));

    $this->assertCount(15, $response->json("data"));
});

it(
    "returns 404 when retrieving paginated orders for a non-existent customer",
    function () {
        $response = $this->getJson(route("customers.orders", ["id" => 999]));

        $response->assertStatus(404);
    }
);
