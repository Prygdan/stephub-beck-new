<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Models\Delivery\Branch;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class BranchesController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Branch::paginate(100), 200);
    }

    public function destroy(Branch $branch): JsonResponse
    {
        $branch->delete();

        return response()->json(['message' => 'Branch deleted successfully'], 200);
    }

    public function getBranchesByCity($cityRef): JsonResponse
    {
        $validator = Validator::make(['cityRef' => $cityRef], [
            'cityRef' => 'required|exists:cities,ref'
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid city ref'], 400);
        }

        $branches = Branch::where('cityRef', $cityRef)->get();

        return response()->json($branches);
    }
}
