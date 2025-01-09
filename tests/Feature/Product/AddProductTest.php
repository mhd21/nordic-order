<?php

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it(
    "adds a new product successfully and returns product attributes",
    function () {
        $payload = [
            "name" => "Test Product",
            "price" => 99.99,
            "stock" => 50,
        ];

        $response = $this->postJson(route("products.add"), $payload);

        $response->assertStatus(201)->assertJson([
            "name" => $payload["name"],
            "price" => $payload["price"],
            "stock" => $payload["stock"],
        ]);

        $this->assertDatabaseHas("products", [
            "name" => $payload["name"],
            "price" => $payload["price"],
            "stock" => $payload["stock"],
        ]);
    }
);

it("validates the input when adding a product", function (
    $payload,
    $expectedErrorField
) {
    $response = $this->postJson(route("products.add"), $payload);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors($expectedErrorField);
})->with([
    [["price" => 99.99, "stock" => 50], "name"], // Missing name
    [["name" => "Test Product", "stock" => 50], "price"], // Missing price
    [["name" => "Test Product", "price" => 99.99], "stock"], // Missing stock
    [["name" => "", "price" => 99.99, "stock" => 50], "name"], // Empty name
    [["name" => "Test Product", "price" => -10, "stock" => 50], "price"], // Negative price
    [["name" => "Test Product", "price" => 99.99, "stock" => -5], "stock"], // Negative stock
]);
