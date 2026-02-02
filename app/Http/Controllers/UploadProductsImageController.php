<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadProductsImage\Store;
use App\Http\Requests\UploadProductsImage\StoreRequest;
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
        // Отримуємо валідовані дані
        $data = $request->validated();
        $imageData = $data['image'];
        $productId = $data['product_id'];

        // Шукаємо продукт
        $product = Product::find($productId);
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        // Розділяємо та декодуємо base64-зображення
        list($type, $data) = explode(';', $imageData);
        list(, $data) = explode(',', $data);
        $data = base64_decode($data);

        // Генеруємо унікальне ім'я файлу та зберігаємо його
        $fileName = 'products/' . uniqid() . '.webp';
        Storage::disk('public')->put($fileName, $data);

        // Зберігаємо шлях до зображення в таблиці продуктів
        $product->images()->create(['image' => $fileName]);

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
