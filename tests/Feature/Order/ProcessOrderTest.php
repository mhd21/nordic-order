<?php

use App\Jobs\ProcessOrder;
use App\Mail\OrderConfirmationMail;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it(
    "processes an order, deducts stock, confirms the order, and sends email",
    function () {
        Queue::fake();

        Mail::fake();

        $customer = Customer::factory()->create();

        $product1 = Product::factory()->create(["stock" => 10, "price" => 100]);
        $product2 = Product::factory()->create(["stock" => 5, "price" => 200]);

        $order = Order::factory()->create([
            "customer_id" => $customer->id,
            "status" => "pending",
        ]);
        OrderItem::factory()->create([
            "order_id" => $order->id,
            "product_id" => $product1->id,
            "quantity" => 2,
            "price" => $product1->price,
        ]);
        OrderItem::factory()->create([
            "order_id" => $order->id,
            "product_id" => $product2->id,
            "quantity" => 1,
            "price" => $product2->price,
        ]);

        ProcessOrder::dispatch($order);

        (new ProcessOrder($order))->handle();

        $this->assertDatabaseHas("products", [
            "id" => $product1->id,
            "stock" => 8, // 10 - 2
        ]);
        $this->assertDatabaseHas("products", [
            "id" => $product2->id,
            "stock" => 4, // 5 - 1
        ]);

        $this->assertDatabaseHas("orders", [
            "id" => $order->id,
            "status" => "confirmed",
        ]);

        Mail::assertSent(OrderConfirmationMail::class, function ($mail) use (
            $order
        ) {
            return $mail->order->id === $order->id;
        });
    }
);

it(
    "fails to process an order due to insufficient stock and updates status",
    function () {
        Queue::fake();
        Mail::fake();

        $customer = Customer::factory()->create();

        $product = Product::factory()->create(["stock" => 1, "price" => 100]);

        $order = Order::factory()->create([
            "customer_id" => $customer->id,
            "status" => "pending",
        ]);
        OrderItem::factory()->create([
            "order_id" => $order->id,
            "product_id" => $product->id,
            "quantity" => 2,
            "price" => $product->price,
        ]);

        try {
            (new ProcessOrder($order))->handle();
        } catch (\Exception $e) {
            $this->assertEquals(
                "Insufficient stock for product: {$product->name}",
                $e->getMessage()
            );
        }

        $this->assertDatabaseHas("orders", [
            "id" => $order->id,
            "status" => "failed",
        ]);

        $this->assertDatabaseHas("products", [
            "id" => $product->id,
            "stock" => 1,
        ]);

        Mail::assertNothingSent();
    }
);
