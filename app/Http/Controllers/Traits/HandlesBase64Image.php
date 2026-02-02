<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait HandlesBase64Image
{
  protected function saveBase64Image(string $base64Image, string $directory, string $name): string
  {
    $imageData = base64_decode(explode(',', $base64Image)[1]);
    $fileName = Str::slug($name) . '.jpg';
    Storage::disk('public')->put($directory . '/' . $fileName, $imageData);
    
    return $directory . '/' . $fileName;
  }
}
