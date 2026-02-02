<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Http\Requests\Areas\StoreRequest;
use App\Http\Requests\Areas\UpdateRequest;
use App\Models\Delivery\Area;
use Illuminate\Http\JsonResponse;

class AreasController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Area::paginate(100), 200);
    }

    public function store(StoreRequest $request): JsonResponse
    {
        $area = Area::create($request->validated());

        return response()->json($area, 201);
    }

    public function update(UpdateRequest $request, Area $area): JsonResponse
    {
        $area->fill($request->validated());
        $area->save();

        return response()->json($area, 200);
    }

    public function destroy(Area $area): JsonResponse
    {
        $area->delete();

        return response()->json(['message' => 'Area deleted successfully'], 200);
    }
}
