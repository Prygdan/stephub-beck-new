<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductReview\Update;
use App\Models\ProductReview;
use App\Services\NextRevalidateService;

class ProductReviewCrudController extends Controller
{
    public function index()
    {
        $reviews = ProductReview::with(['product', 'product.images', 'user', 'answer'])
                                ->orderBy('created_at', 'desc')
                                ->paginate(30);

        return response()->json($reviews, 200);
    }

    public function update(Update $request, ProductReview $crud_review, NextRevalidateService $revalidate)
    {
        $data = $request->validated();
        $crud_review->update($data);
        $revalidate->tags([
            'reviews',
        ]);

        return response()->json($crud_review, 201);
    }

    public function destroy(ProductReview $crud_review, NextRevalidateService $revalidate)
    {
        $crud_review->delete();
        $revalidate->tags([
            'reviews',
        ]);

        return response()->json($crud_review, 200);
    }
}
