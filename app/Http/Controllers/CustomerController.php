<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function addCustomer(Request $request)
    {
        $request->validate([
            "name" => "required|string",
            "email" => "required|email|unique:customers,email",
        ]);

        $customer = Customer::create($request->only(["name", "email"]));
        return response()->json($customer, 201);
    }

    public function getCustomer($id)
    {
        $customer = Customer::find($id);

        if (!$customer) {
            return response()->json(["error" => "Customer not found"], 404);
        }

        return response()->json($customer, 200);
    }

    public function customerOrders(Request $request, $id)
    {
        $customer = Customer::find($id);

        if (!$customer) {
            return response()->json(["error" => "Customer not found"], 404);
        }

        $page = $request->input("page", 1);

        $perPage = $request->input("per_page", 10);

        $orders = Order::where("customer_id", $id)
            ->with("orderItems")
            ->paginate($perPage, ["*"], "page", $page)
            ->through(function ($order) {
                $totalAmount = $order->orderItems->sum(function ($item) {
                    return $item->price * $item->quantity;
                });

                return [
                    "id" => $order->id,
                    "customer_id" => $order->customer_id,
                    "total_amount" => $totalAmount,
                    "created_at" => $order->created_at,
                    "updated_at" => $order->updated_at,
                ];
            });

        return response()->json($orders, 200);
    }
}
