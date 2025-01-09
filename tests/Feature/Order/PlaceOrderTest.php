<?php

use App\Jobs\ProcessOrder;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\Queue;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it("creates an order and dispatches the ProcessOrder job", function () {
    Queue::fake();

    $customer = Customer::factory()->create();

    $product1 = Product::factory()->create(["stock" => 10, "price" => 100]);
    $product2 = Product::factory()->create(["stock" => 5, "price" => 200]);

    $payload = [
        "customer_id" => $customer->id,
        "products" => [
            ["id" => $product1->id, "quantity" => 2],
            ["id" => $product2->id, "quantity" => 1],
        ],
    ];

    $response = $this->postJson(route("orders.place"), $payload);

    $response->assertStatus(201)->assertJson([
        "message" => "Order placed successfully and will be processed.",
    ]);

    $orderId = $response->json("order_id");

    $this->assertDatabaseHas("orders", [
        "id" => $orderId,
        "customer_id" => $customer->id,
        "status" => "pending",
    ]);

    $this->assertDatabaseHas("order_items", [
        "order_id" => $orderId,
        "product_id" => $product1->id,
        "quantity" => 2,
        "price" => $product1->price,
    ]);
    $this->assertDatabaseHas("order_items", [
        "order_id" => $orderId,
        "product_id" => $product2->id,
        "quantity" => 1,
        "price" => $product2->price,
    ]);

    // Assert the ProcessOrder job was dispatched
    Queue::assertPushed(ProcessOrder::class, function ($job) use ($orderId) {
        return $job->getOrder()->id === $orderId;
    });
});
