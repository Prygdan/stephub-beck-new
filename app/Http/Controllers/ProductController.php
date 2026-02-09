<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\SanitizesFields;
use App\Http\Requests\Product\Store;
use App\Http\Requests\Product\Update;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;
use App\Services\NextRevalidateService;

class ProductController extends Controller
{
    use SanitizesFields;

    public function index()
    {
        $products = Product::with([
            'category', 'subcategory', 'brand', 'season', 'material', 'images', 'sizes'
                ])->orderBy('created_at', 'asc')
                ->paginate(12);

        return response()->json($products, 200);
    }

    public function discountProducts()
    {
        $products = Product::with([
            'category', 'subcategory', 'brand', 'season', 'material', 'images', 'sizes'
                ])->where('discount', '>', 0)
                ->orderBy('discount', 'desc')
                ->paginate(12);

        return response()->json($products, 200);
    }

    public function show(string $slug)
    {
        $product = Product::with([
            'images', 'sizes', 'category', 'subcategory', 'brand', 'season','material'
            ])->where('slug', $slug)->firstOrFail();

        return response()->json($product, 200);
    }

    public function store(Store $request, NextRevalidateService $revalidate)
    {
        $data = collect($this->sanitizeData($request->validated()));
        $product = Product::make($data->except(['sizes'])->toArray());

        if ($data->get('discount')) {
            $product->discounted_price = $product->calculateDiscountedPrice($data->get('discount'));
        }

        DB::transaction(function() use($product, $data) {
            $product->save();

            if ($data->has('sizes')) {
                $product->sizes()->sync($data->get('sizes'));
            }
        });

        $revalidate->tags([
            'category',
            'category-get',
            'subcategory',
            'discountProducts'
        ]);

        return response()->json($product->load('sizes'), 200);
    }

    public function update(Update $request, Product $product, NextRevalidateService $revalidate) 
    {   
        $data = collect($this->sanitizeData($request->validated()));

        DB::transaction(function () use ($data, $product) {
            $product->update($data->except(['sizes'])->toArray());
            $product->sizes()->sync($data->get('sizes', []));
            $product->discounted_price = !empty($data->get('discount'))
                ? round($product->price * (1 - $data->get('discount') / 100), 2)
                : null;
            $product->save();
        });

        $revalidate->tags([
            'category',
            'category-get',
            'subcategory',
            'discountProducts'
        ]);

        return response()->json($product, 200);
    }

    public function destroy(Product $product, NextRevalidateService $revalidate)
    {
        $imagesPath = $product->images->pluck('image');

        foreach($imagesPath as $imgPath) {
            if (Storage::disk('public')->exists($imgPath)) {
                Storage::disk('public')->delete($imgPath);
            }
        }

        $product->images()->delete();
        $product->delete();
        $revalidate->tags([
            'category',
            'category-get',
            'subcategory',
            'discountProducts'
        ]);

        return response()->json($product, 200);
    }

    public function search(Request $request)
    {
        $validated = $request->validate([
            'query' => ['required', 'string', 'min:2', 'max:10'],
        ]);
        $query = $validated['query'];
    
        return Product::where('name', 'LIKE', "%{$query}%")
            ->with(['images'])
            ->limit(10)
            ->get();
    }   
}
