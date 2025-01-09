<?php

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it(
    "adds a new customer successfully and returns customer attributes",
    function () {
        $payload = [
            "name" => "John Doe",
            "email" => "johndoe@example.com",
        ];

        $response = $this->postJson(route("customers.add"), $payload);

        $response->assertStatus(201)->assertJson([
            "name" => $payload["name"],
            "email" => $payload["email"],
        ]);

        $this->assertDatabaseHas("customers", [
            "name" => $payload["name"],
            "email" => $payload["email"],
        ]);
    }
);

it("validates the input when adding a customer", function (
    $payload,
    $expectedErrorField
) {
    $response = $this->postJson(route("customers.add"), $payload);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors($expectedErrorField);
})->with([
    [["email" => "johndoe@example.com"], "name"], // Missing name
    [["name" => "John Doe"], "email"], // Missing email
    [["name" => "", "email" => "johndoe@example.com"], "name"], // Empty name
    [["name" => "John Doe", "email" => "invalid-email"], "email"], // Invalid email
]);
