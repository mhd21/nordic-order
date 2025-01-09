<?php

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it("returns a paginated list of products", function () {
    Product::factory()->count(30)->create();

    $response = $this->getJson(
        route("products.list", ["page" => 1, "per_page" => 15])
    );

    $response->assertStatus(200);

    $response->assertJsonStructure([
        "data" => [
            "*" => ["id", "name", "price", "stock", "created_at", "updated_at"], // Product attributes
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

it("returns a paginated list of products for the second page", function () {
    Product::factory()->count(30)->create();

    $response = $this->getJson(
        route("products.list", ["page" => 2, "per_page" => 15])
    );

    $response->assertStatus(200);

    $this->assertEquals(2, $response->json("current_page"));

    $this->assertCount(15, $response->json("data"));
});
