<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;

Route::get("/user", function (Request $request) {
    return $request->user();
})->middleware("auth:sanctum");

Route::prefix("products")->group(function () {
    Route::post("/", [ProductController::class, "addProduct"])->name(
        "products.add"
    );
    Route::get("/", [ProductController::class, "productList"])->name(
        "products.list"
    );
});

Route::prefix("customers")->group(function () {
    Route::post("/", [CustomerController::class, "addCustomer"])->name(
        "customers.add"
    );
    Route::get("/{id}", [CustomerController::class, "getCustomer"])->name(
        "customers.get"
    );
    Route::get("/{id}/orders", [
        CustomerController::class,
        "customerOrders",
    ])->name("customers.orders");
});

Route::prefix("orders")->group(function () {
    Route::post("/", [OrderController::class, "placeOrder"])->name(
        "orders.place"
    );
});
