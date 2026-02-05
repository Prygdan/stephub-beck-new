<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadProductsImage\Store;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;

class UploadProductsImageController extends Controller
{
    public function index($productId)
    {
        $product = Product::findOrFail($productId);
        $images = $product->images()->get();

        return $images;
    }

    public function store(Store $request)
    {
        $validated = $request->validated();

        $imageData = $validated['image'];
        $productId = $validated['product_id'];

        $product = Product::findOrFail($productId);

        // 1. Витягуємо base64
        [, $base64] = explode(',', $imageData);
        $binary = base64_decode($base64);

        // 2. Створюємо image resource
        $image = imagecreatefromstring($binary);

        if ($image === false) {
            return response()->json(['error' => 'Invalid image data'], 422);
        }

        // 3. Генеруємо шлях
        $fileName = 'products/' . uniqid('', true) . '.webp';
        $fullPath = storage_path('app/public/' . $fileName);

        // 4. Конвертуємо в WebP (ВАЖЛИВО)
        imagewebp($image, $fullPath, 90); // 85–90 оптимально

        imagedestroy($image);

        // 5. Зберігаємо в БД
        $product->images()->create([
            'image' => $fileName,
        ]);

        return response()->json(['image' => $fileName], 201);
    }

    public function delete($productId, $imageId)
    {
        $product = Product::findOrFail($productId);
        $image = $product->images()->find($imageId);

        if(!$image) {
            return response()->json(['error' => 'Image not found'], 404);
        }

        Storage::disk('public')->delete($image->image);
        $image->delete();

        return response()->json(['success' => 'Image deleted successfully'], 200);
    }
}
