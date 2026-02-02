<?php

namespace App\Http\Controllers;

use App\Filters\ProductFilter;
use App\Http\Requests\Subcategory\Store;
use App\Http\Requests\Subcategory\Update;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Material;
use App\Models\Season;
use App\Models\Size;
use Illuminate\Http\JsonResponse;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubcategoryController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Subcategory::orderBy('name', 'asc')->get(), 200);
    }

    public function show(Request $request, string $slug): JsonResponse
    {
        $request->validate([
            'priceFrom'     => 'nullable|integer|min:0',
            'priceTo'       => 'nullable|integer|min:0',
            'brands'        => 'nullable|string',
            'seasons'       => 'nullable|string',
            'materials'     => 'nullable|string',
            'sizes'         => 'nullable|string',
            'categories'    => 'nullable|string',
            'sortBy'        => 'nullable|in:price_asc,price_desc,newest,oldest,biggest_discount',
        ]);

        $subcategory = Subcategory::where('slug', $slug)->firstOrFail();

        $products = (new ProductFilter($request))
            ->apply(
                $subcategory->products()
                    ->with(['images', 'subcategory', 'brand', 'sizes', 'season', 'material'])
            )
            ->paginate(48);

        return response()->json([
            'subcategory'   => $subcategory,
            'products'      => $products,
        ]);
    }

    public function filters(Request $request, string $slug): JsonResponse
    {
        $subcategory = Subcategory::where('slug', $slug)->firstOrFail();

        // 1️⃣ Базовий запит для продуктів категорії
        $baseQuery = $subcategory->products()->select('products.*');

        // 2️⃣ Всі продукти після застосування поточних фільтрів
        $filteredProducts = (new ProductFilter($request))->apply(clone $baseQuery)->get();

        // IDs продуктів для sizes
        $productIds = $filteredProducts->pluck('id')->toArray();

        // 3️⃣ Генерація доступних опцій для кожного фільтру
        // Виключаємо власний фільтр для dependent filters
        $availableOptions = [
            'brands' => (new ProductFilter($request, ['brands']))->apply(clone $baseQuery)->pluck('brand_id')->unique()->filter()->values(),
            'seasons' => (new ProductFilter($request, ['seasons']))->apply(clone $baseQuery)->pluck('season_id')->unique()->filter()->values(),
            'materials' => (new ProductFilter($request, ['materials']))->apply(clone $baseQuery)->pluck('material_id')->unique()->filter()->values(),
            'categories' => (new ProductFilter($request, ['categories']))->apply(clone $baseQuery)->pluck('products.category_id')->unique()->filter()->values(),
        ];

        // 4️⃣ Отримуємо дані з відповідних таблиць
        $availableBrands = Brand::whereIn('id', $availableOptions['brands'])->get();
        $availableSeasons = Season::whereIn('id', $availableOptions['seasons'])->get();
        $availableMaterials = Material::whereIn('id', $availableOptions['materials'])->get();
        $availableСategories = Category::whereIn('id', $availableOptions['categories'])->get();

        // 5️⃣ Розміри (pivot)
        $availableSizeIds = DB::table('product_size')
            ->whereIn('product_id', $productIds)
            ->pluck('size_id')
            ->unique();
        $availableSizes = Size::whereIn('id', $availableSizeIds)->get();

        // 6️⃣ Ціновий діапазон
        $priceBaseProducts = (new ProductFilter(
            $request,
            ['priceFrom', 'priceTo'] // ⬅️ ключове
        ))->apply(clone $baseQuery)->get();
        // 6️⃣ Ціновий діапазон
        $prices = $priceBaseProducts->map(fn ($p) =>
            $p->discounted_price && $p->discounted_price > 0
                ? $p->discounted_price
                : $p->price
        );

        return response()->json([
            'available_brands'        => $availableBrands,
            'available_seasons'       => $availableSeasons,
            'available_materials'     => $availableMaterials,
            'available_sizes'         => $availableSizes,
            'available_categories'    => $availableСategories,
            'price_range' => [
                'min' => $prices->min(),
                'max' => $prices->max(),
            ],
        ]);
    }

    public function store(Store $request): JsonResponse
    {
        $subcategory = Subcategory::create($request->validated());

        return response()->json($subcategory, 201);
    }

    public function update(Update $request, Subcategory $subcategory): JsonResponse
    {
        $subcategory->fill($request->validated());

        if ($subcategory->isDirty('name')) {
            $subcategory->slug = null;
        }

        $subcategory->save();

        return response()->json($subcategory, 200);
    }

    public function destroy(Subcategory $subcategory)
    {
        $subcategory->delete();

        return response()->noContent(200);
    }
}
