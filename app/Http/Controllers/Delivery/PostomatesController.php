<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Models\Delivery\Postomat;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class PostomatesController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Postomat::paginate(100), 200);
    }

    public function destroy(Postomat $postomat): JsonResponse
    {
        $postomat->delete();

        return response()->json(['message' => 'Postomat deleted successfully'], 200);
    }

    public function getPostmatesByCity($cityRef): JsonResponse
    {
        $validator = Validator::make(['cityRef' => $cityRef], [
            'cityRef' => 'required|exists:cities,ref'
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid city ref'], 400);
        }

        $postmates = Postomat::where('cityRef', $cityRef)->get();

        return response()->json($postmates);
    }
}
