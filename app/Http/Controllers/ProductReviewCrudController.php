<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductReview\Update;
use App\Models\ProductReview;

class ProductReviewCrudController extends Controller
{
    public function index()
    {
        $reviews = ProductReview::with(['product', 'product.images', 'user', 'answer'])->paginate(30);

        return response()->json($reviews, 200);
    }

    public function update(Update $request, ProductReview $crud_review)
    {
        $data = $request->validated();
        $crud_review->update($data);

        return response()->json($crud_review, 201);
    }

    public function destroy(ProductReview $crud_review)
    {
        $crud_review->delete();

        return response()->json($crud_review, 200);
    }
}
