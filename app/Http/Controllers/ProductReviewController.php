<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductReview;
use App\Http\Requests\ProductReview\Store;
use App\Services\NextRevalidateService;
use Illuminate\Support\Facades\Auth;

class ProductReviewController extends Controller
{
    public function index(Product $product)
    {
        $reviews = $product->reviews()->with(['answer'])->latest()->paginate(2);
        $averageRating = round($product->reviews()->avg('rating'), 1);

        return response()->json([
            'reviews' => $reviews,
            'average_rating' => $averageRating,
        ], 200);
    }

    public function store(Store $request, Product $product, NextRevalidateService $revalidate)
    {
        $data = $request->validated();

        if(Auth::check()) {
            $data['name'] = Auth::user()->name;
            $data['user_id'] = Auth::id();
        } else {
            $data['user_id'] = null;
        }

        $data['product_id'] = $product->id;
        $review = ProductReview::create($data);

        $revalidate->tags([
            'reviews',
        ]);

        return response()->json([
            'message'   =>  'Відгук успішно додано.',
            'data'      =>  $review,
        ], 200);
    }

    public function ratingStats(Product $product)
    {
        $ratings = $product->reviews()
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->pluck('count', 'rating');

        $total = $ratings->sum();

        $stats = [];

        foreach (range(5, 1) as $star) {
            $count = $ratings[$star] ?? 0;
            $percentage = $total > 0 ? round(($count / $total) * 100) : 0;

            $stats[] = [
                'rating' => $star,
                'count' => $count,
                'percentage' => $percentage,
            ];
        }

        return response()->json([
            'total' => $total,
            'stats' => $stats
        ]);
    }
}
