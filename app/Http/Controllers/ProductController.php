<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
class ProductController extends Controller
{
    public function index()
    {
        return Product::all();
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'products_category_id' => 'required|integer',
            'name' => 'required|string',
            'description' => 'sometimes|string',
            'main_price' => 'required|regex:/^\d{1,4}(\.\d{1,2})?$/', //Bu regex, (örneğin 123.45) doğrulamasını yapar.
            'main_image_url' => 'sometimes|string',
        ]);


        $product = new Product();
        $product->category_id = $validatedData['products_category_id']; // products_category -> category_id olarak güncellenmiş olabilir
        $product->name = $validatedData['name'];
        $product->description = $validatedData['description'] ?? null;
        $product->main_price = $validatedData['main_price'];
        $product->main_image = $validatedData['main_image_url'] ?? null;
        $product->save();
        return response()->json(['message' => 'Product added successfully', 'data' => $product], 201);
    }

    public function show(Product $product)
    {
        return response()->json(['message' => 'This Route Gives You Spesific Product You Choosed'], 201);
    }

    public function update(Request $request, Product $product)
    {
        return response()->json(['message' => 'This Route Updates The Product You Choosed'], 201);

    }

    public function destroy(Product $product)
    {
        return response()->json(['message' => 'This Route Deletes The Product You Choosed'], 201);

    }
}
