<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Models\Delivery\City;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class CitiesController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(City::paginate(100), 200);
    }

    public function destroy(City $city): JsonResponse
    {
        $city->delete();

        return response()->json(['message' => 'City deleted successfully'], 200);
    }

    public function getCitiesByArea($areaRef): JsonResponse
    {
        $validator = Validator::make(['areaRef' => $areaRef], [
            'areaRef' => 'required|exists:areas,ref'
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid areaRef'], 400);
        }

        $cities = City::where('areaRef', $areaRef)->get();
        
        return response()->json($cities);
    }
}
