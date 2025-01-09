<?php

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it("retrieves a customer by ID", function () {
    $customer = Customer::factory()->create([
        "name" => "Jane Doe",
        "email" => "jane.doe@example.com",
    ]);

    $response = $this->getJson(route("customers.get", ["id" => $customer->id]));

    $response->assertStatus(200)->assertJson([
        "id" => $customer->id,
        "name" => $customer->name,
        "email" => $customer->email,
    ]);
});

it("returns 404 when retrieving a non-existent customer", function () {
    $response = $this->getJson(route("customers.get", ["id" => 999]));

    $response->assertStatus(404);
});
