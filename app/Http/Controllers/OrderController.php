<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessOrder;
use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderConfirmationMail;
use App\Models\OrderItem;
use Exception;

class OrderController extends Controller
{
    public function placeOrder(Request $request)
    {
        $validated = $request->validate([
            "customer_id" => "required|exists:customers,id",
            "products" => "required|array",
            "products.*.id" => "required|exists:products,id",
            "products.*.quantity" => "required|integer|min:1",
        ]);

        try {
            $order = DB::transaction(function () use ($validated) {
                // Create the order
                $order = Order::create([
                    "customer_id" => $validated["customer_id"],
                    "total_amount" => 0, // Will be calculated later
                ]);

                $totalAmount = 0;

                foreach ($validated["products"] as $productData) {
                    $product = Product::find($productData["id"]);

                    // Add order item (deduction happens later)
                    OrderItem::create([
                        "order_id" => $order->id,
                        "product_id" => $product->id,
                        "quantity" => $productData["quantity"],
                        "price" => $product->price,
                    ]);

                    $totalAmount += $product->price * $productData["quantity"];
                }

                $order->update(["total_amount" => $totalAmount]);

                return $order;
            });

            // Dispatch job to handle stock deduction and order confirmation
            ProcessOrder::dispatch($order);

            return response()->json(
                [
                    "message" =>
                        "Order placed successfully and will be processed.",
                    "order_id" => $order->id,
                    "total_amount" => $order->total_amount,
                ],
                201
            );
        } catch (Exception $e) {
            return response()->json(
                [
                    "error" => "Failed to place order.",
                    "details" => $e->getMessage(),
                ],
                500
            );
        }
    }
}
