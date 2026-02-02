<?php

namespace App\Http\Controllers;

use App\Http\Requests\Size\Store;
use App\Http\Requests\Size\Update;
use Illuminate\Http\JsonResponse;
use App\Models\Size;

class SizeController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Size::orderBy('value_eu', 'asc')->get(), 200);
    }

    public function store(Store $request): JsonResponse
    {
        $size = Size::create($request->validated());

        return response()->json($size, 200);
    }

    public function update(Update $request, Size $size): JsonResponse
    {
        $size->update($request->validated());

        return response()->json($size, 200);
    }

    public function destroy(Size $size)
    {
        $size->delete();
        
        return response()->noContent(200);
    }
}
