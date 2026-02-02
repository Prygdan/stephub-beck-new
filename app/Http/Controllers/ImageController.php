<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:2048',
        ]);

        // Зберігаємо у storage/app/public/uploads
        $path = $request->file('image')->store('uploads', 'public');

        // Отримуємо веб-доступний URL
        $url = Storage::url($path); // Наприклад: /storage/uploads/file.jpg

        return response()->json(['url' => $url]);
    }
}
