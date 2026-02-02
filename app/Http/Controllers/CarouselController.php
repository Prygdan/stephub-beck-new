<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\HandlesBase64Image;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Carousel\Store;
use Illuminate\Support\Facades\DB;
use App\Models\Carousel;

class CarouselController extends Controller
{
    use HandlesBase64Image;

    public function index()
    {
        $carousels = Carousel::with(['items'])->get();

        return response()->json($carousels, 200);
    }

    public function getCarouselHome()
    {
        $carousel = Carousel::with(['items'])->where('category_id', null)->first();

        if (!$carousel) {
            return response()->noContent(201);
        }

        return response()->json($carousel, 200);
    }

    public function store(Store $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->validated();

            $carousel = Carousel::create([
                'category_id'       =>  $data['category_id'] ?? null
            ]);

            foreach($data['items'] as $index => $imageData) {
                $path = $this->saveBase64Image(
                    $imageData['image'],
                    'carousels',
                    'carousel-item-' . $carousel->id . '-' . $index
                );

                $pathMobile = $imageData['image_mobile'] && $this->saveBase64Image(
                    $imageData['image_mobile'],
                    'carousels',
                    'carousel-item-mobile' . $carousel->id . '-' . $index
                );

                $carousel->items()->create([
                    'image'         => $path,
                    'image_mobile'  => $pathMobile,
                ]);
            };
            DB::commit();

            return response()->json([
                'message' => 'Carousel created successfully',
                'carousel' => $carousel->load('items')
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->noContent(500);
        }
    }
    
    public function destroy(Carousel $carousel)
    {
        try {
            foreach($carousel->items as $item) 
            {
                if($item->image && Storage::disk('public')->exists($item->image)) {
                    Storage::disk('public')->delete(str_replace('storage/', '', $item->image));
                }

                if($item->image_mobile && Storage::disk('public')->exists($item->image_mobile)) {
                    Storage::disk('public')->delete(str_replace('storage/', '', $item->image_mobile));
                }
            }

            $carousel->items()->delete();
            $carousel->delete();
            
            return response()->noContent(200);
        } catch (\Exception $e) {
            return response()->noContent(500);
        }
    }
}
