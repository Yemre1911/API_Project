<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\Variant;
use App\Models\Color;
use App\Models\Size;
use Illuminate\Support\Facades\Log;


class TableController extends Controller
{
    public function add_category(Request $request)
    {
        $validatedData = $request->validate([
            'category' => 'required|string|unique:categories,category',
        ]);

        $category = new Category();
        $category->category = $validatedData['category'];
        $category->save();
        return response()->json(['message' => 'Category added successfully', 'data' => $category], 201);
    }

    public function add_color(Request $request)
    {
        $validatedData = $request->validate([
            'color' => 'required|string|unique:colors,color',
        ]);
        Log::info('addColor: ',$validatedData);
        $color = new Color();
        $color->color = $validatedData['color'];
        $color->save();
        return response()->json(['message' => 'Color added successfully', 'data' => $color], 201);
    }

    public function add_size(Request $request)
    {
        $validatedData = $request->validate([
            'size' => 'required|string|unique:sizes,size',
        ]);

        $size = new Size();
        $size->size = $validatedData['size'];
        $size->save();
        return response()->json(['message' => 'Size added successfully', 'data' => $size], 201);
    }

    public function add_product(Request $request)
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

    public function add_variant(Request $request)
    {
        $validatedData = $request->validate([
            'products_id' => 'required|integer',
            'color_id' => 'required|integer',
            'size_id' => 'required|integer',
            'add_price' => 'required|regex:/^\d{1,4}(\.\d{1,2})?$/', //Bu regex, (örneğin 123.45) doğrulamasını yapar.
            'main_image_url' => 'sometimes|string',
            'img1_url' => 'sometimes|string',
            'img2_url' => 'sometimes|string',
            'img3_url' => 'sometimes|string',
        ]);


        $variant = new Variant();
        $variant->product_id = $validatedData['products_id'];
        $variant->color_id = $validatedData['color_id'];
        $variant->size_id = $validatedData['size_id'];
        $variant->add_price = $validatedData['add_price'];
        $variant->main_image = $validatedData['main_image_url'] ?? null;
        $variant->img1 = $validatedData['img1_url'] ?? null;
        $variant->img2 = $validatedData['img2_url'] ?? null;
        $variant->img3 = $validatedData['img3_url'] ?? null;
        $variant->save();
        return response()->json(['message' => 'Variant added successfully', 'data' => $variant], 201);
    }
}
