<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\Variant;
use App\Models\Color;
use App\Models\Size;
use Illuminate\Support\Facades\Log;

class ProductList extends Controller
{
    public function listByFilter(Request $request)
    {
        $validatedData = $request->validate([
            'category' => 'string|sometimes',
            'name' => 'string|sometimes',
            'color' => 'string|sometimes',
            'size' => 'string|sometimes',
            'price_min' => 'sometimes|regex:/^\d{1,4}(\.\d{1,2})?$/',
            'price_max' => 'sometimes|regex:/^\d{1,4}(\.\d{1,2})?$/',
        ]);

        $category = Category::where('category', $request->input('category'))->first();
        if ($category) {
            $categoryID = $category->id;
        } else {
            Log::info('Category Bulunamadı');
            $categoryID = null;
        }

        $colorID = null;
        if ($request->input('color')) {
            $color = Color::where('color', $request->input('color'))->first();
            if ($color) {
                $colorID = $color->id;
            } else {
                Log::info('Color Bulunamadı');
            }
        }

        $sizeID = null;
        if ($request->input('size')) {
            $size = Size::where('size', $request->input('size'))->first();
            if ($size) {
                $sizeID = $size->id;
            } else {
                Log::info('Size Bulunamadı');
            }
        }

        $query = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('variants', 'products.id', '=', 'variants.product_id')
            ->select('products.id', 'products.name', 'products.main_price', 'products.description', 'variants.color_id', 'variants.size_id', 'variants.add_price','variants.id as variant_id');

        if ($categoryID) {
            $query->where('products.category_id', $categoryID);
        }

        if ($colorID) {
            $query->where('variants.color_id', $colorID);
        }

        if ($sizeID) {
            $query->where('variants.size_id', $sizeID);
        }

        if (!empty($validatedData['price_min'])) {
            $query->where('products.main_price', '>=', $validatedData['price_min']);
        }

        if (!empty($validatedData['price_max'])) {
            $query->where('products.main_price', '<=', $validatedData['price_max']);
        }

        // Tüm unique ID'leri al
        $results = $query->get();

        // Ürünleri gruplandır
        $groupedResults = $results->groupBy('id');

        // JSON formatında döndür
        $response = $groupedResults->map(function ($items, $id) {
            $product = $items->first();
            $variants = $items->map(function ($item) {
                // Renk ve boyut isimlerini al
                $color = Color::find($item->color_id);
                $size = Size::find($item->size_id);

                return [
                    'variant_id' => $item->variant_id,  // Doğru Varyant ID'si
                    'color' => $color ? $color->color : 'Unknown',
                    'size' => $size ? $size->size : 'Unknown',
                    'additional_price' => $item->add_price,
                ];
            });

            return [
                'product_id' => $id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->main_price,
                'variants' => $variants,
            ];
        });

        return response()->json($response, 200);
    }


    public function listByID(Product $id)
    {
        return $id;
    }
}
