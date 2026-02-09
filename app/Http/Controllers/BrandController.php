<?php

namespace App\Http\Controllers;

use App\Filters\ProductFilter;
use App\Models\Brand;
use App\Http\Controllers\Traits\HandlesBase64Image;
use App\Http\Requests\Brand\Store;
use App\Http\Requests\Brand\Update;
use App\Models\Category;
use App\Models\Material;
use App\Models\Season;
use App\Models\Size;
use App\Models\Subcategory;
use App\Services\NextRevalidateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    use HandlesBase64Image;

    public function index(): JsonResponse
    {
        return response()->json(Brand::orderBy('name', 'asc')->get(), 200);
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
            'subcategories' => 'nullable|string',
            'sortBy'        => 'nullable|in:price_asc,price_desc,newest,oldest,biggest_discount',
        ]);

        $brand = Brand::where('slug', $slug)->firstOrFail();

        $products = (new ProductFilter($request))
            ->apply(
                $brand->products()
                    ->with(['images', 'subcategory', 'brand', 'sizes', 'season', 'material'])
            )
            ->paginate(48);

        return response()->json([
            'brand'     => $brand,
            'products'  => $products,
        ]);
    }

    public function store(Store $request, NextRevalidateService $revalidate): JsonResponse
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            
            if (isset($data['image']) && Str::startsWith($data['image'], 'data:image')) {
                $data['image'] = $this->saveBase64Image($data['image'], 'brands', $data['name']);
            }

            $data['slug'] = Str::slug($data['name']);
            $brand = Brand::create($data);
            $revalidate->tags([
                'brand',
                'brand-get',
            ]);
            DB::commit();

            return response()->json($brand, 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Помилка при створенні бренду',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Update $request, Brand $brand, NextRevalidateService $revalidate): JsonResponse
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();

            if (array_key_exists('image', $data)) {
                if ($data['image'] === null) {
                    // якщо прийшло null видаляємо ключ, щоб не затерти старе
                    unset($data['image']);
                } elseif (Str::startsWith($data['image'], 'data:image')) {
                    // якщо прийшли нові дані картинки
                    if ($brand->image) {
                        Storage::disk('public')->delete(str_replace('storage/', '', $brand->image));
                    }
                    $data['image'] = $this->saveBase64Image($data['image'], 'brands', $data['name']);
                }
            }

            $data['slug'] = Str::slug($data['name']);
            $brand->update($data);
            $revalidate->tags([
                'brand',
                'brand-get',
            ]);
            DB::commit();

            return response()->json($brand, 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Помилка при оновленні бренду',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Brand $brand, NextRevalidateService $revalidate)
    {
        try {
            if ($brand->image) {
                Storage::disk('public')->delete(str_replace('storage/', '', $brand->image));
            }
            $brand->delete();
            $revalidate->tags([
                'brand',
                'brand-get',
            ]);
            
            return response()->noContent();
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Помилка при видаленні бренду',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function filters(Request $request, string $slug): JsonResponse
    {
        $brand = Brand::where('slug', $slug)->firstOrFail();
        $baseQuery = $brand->products()->select('products.*');
        $filteredProducts = (new ProductFilter($request))->apply(clone $baseQuery)->get();
        $productIds = $filteredProducts->pluck('id')->toArray();
        $availableOptions = [
            'seasons' => (new ProductFilter($request, ['seasons']))->apply(clone $baseQuery)->pluck('season_id')->unique()->filter()->values(),
            'materials' => (new ProductFilter($request, ['materials']))->apply(clone $baseQuery)->pluck('material_id')->unique()->filter()->values(),
            'categories' => (new ProductFilter($request, ['categories']))->apply(clone $baseQuery)->pluck('category_id')->unique()->filter()->values(),
            'subcategories' => (new ProductFilter($request, ['subcategories']))->apply(clone $baseQuery)->pluck('subcategory_id')->unique()->filter()->values(),
        ];
        $availableSeasons = Season::whereIn('id', $availableOptions['seasons'])->get();
        $availableMaterials = Material::whereIn('id', $availableOptions['materials'])->get();
        $availableCategories = Category::whereIn('id', $availableOptions['categories'])->get();
        $availableSubcategories = Subcategory::whereIn('id', $availableOptions['subcategories'])->get();
        $availableSizeIds = DB::table('product_size')
            ->whereIn('product_id', $productIds)
            ->pluck('size_id')
            ->unique();
        $availableSizes = Size::whereIn('id', $availableSizeIds)->get();

        $priceBaseProducts = (new ProductFilter(
                $request, ['priceFrom', 'priceTo']
            ))->apply(clone $baseQuery)->get();
        $prices = $priceBaseProducts->map(fn ($p) =>
            $p->discounted_price && $p->discounted_price > 0
                ? $p->discounted_price
                : $p->price
        );

        return response()->json([
            'available_seasons'       => $availableSeasons,
            'available_materials'     => $availableMaterials,
            'available_sizes'         => $availableSizes,
            'available_categories'    => $availableCategories,
            'available_subcategories' => $availableSubcategories,
            'price_range' => [
                'min' => $prices->min(),
                'max' => $prices->max(),
            ],
        ]);
    }
}
