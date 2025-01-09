<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function addProduct(Request $request)
    {
        $request->validate([
            "name" => "required|string",
            "price" => "required|numeric|min:0",
            "stock" => "required|integer|min:0",
        ]);

        $product = Product::create($request->only(["name", "price", "stock"]));
        return response()->json($product, 201);
    }

    public function productList(Request $request)
    {
        $page = $request->input("page", 1);

        $perPage = $request->input("per_page", 10);

        $products = Product::paginate($perPage, ["*"], "page", $page);

        return response()->json($products);
    }
}
