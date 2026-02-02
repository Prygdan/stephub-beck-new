<?php

namespace App\Http\Controllers;

use App\Models\ProductReviewAnswer;
use App\Http\Requests\ProductReviewAnswer\Store;
use App\Http\Requests\ProductReviewAnswer\Update;

class ProductReviewCrudAnswerController extends Controller
{
    public function index()
    {
        $answers = ProductReviewAnswer::with(['review'])->paginate(10);

        return response()->json($answers, 200);
    }

    public function store(Store $request)
    {
        $data = $request->validated();
        $answer = ProductReviewAnswer::create($data);

        return response()->json($answer, 200);
    }

    public function update(Update $request, ProductReviewAnswer $crud_reviews_answer)
    {
        $crud_reviews_answer->update($request->validated());

        return response()->json($crud_reviews_answer, 200);
    }

    public function destroy(ProductReviewAnswer $crud_reviews_answer)
    {
        $crud_reviews_answer->delete();

        return response()->json(['message' => 'Answer deleted successfully'], 200);
    }
}
