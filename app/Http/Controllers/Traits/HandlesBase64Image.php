<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Support\Str;

trait HandlesBase64Image
{
  protected function saveBase64Image(
        string $base64Image,
        string $directory,
        string $name,
        int $maxSize = 1600,
        int $quality = 90
    ): string {
        // 1. Decode base64
        [, $base64] = explode(',', $base64Image);
        $binary = base64_decode($base64);

        $image = imagecreatefromstring($binary);

        if ($image === false) {
            throw new \RuntimeException('Invalid image data');
        }

        // 2. Resize якщо потрібно
        $width  = imagesx($image);
        $height = imagesy($image);

        if ($width > $maxSize || $height > $maxSize) {
            $ratio = min($maxSize / $width, $maxSize / $height);

            $newW = (int) ($width * $ratio);
            $newH = (int) ($height * $ratio);

            $resized = imagecreatetruecolor($newW, $newH);
            imagecopyresampled(
                $resized,
                $image,
                0,
                0,
                0,
                0,
                $newW,
                $newH,
                $width,
                $height
            );

            imagedestroy($image);
            $image = $resized;
        }

        // 3. File name
        $fileName = Str::slug($name) . '-' . uniqid() . '.webp';
        $path = $directory . '/' . $fileName;

        // 4. Save WebP
        $fullPath = storage_path('app/public/' . $path);
        imagewebp($image, $fullPath, $quality);

        imagedestroy($image);

        return $path;
    }
}
