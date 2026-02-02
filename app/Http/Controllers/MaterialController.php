<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Http\Requests\Material\Store;
use App\Http\Requests\Material\Update;
use Illuminate\Http\JsonResponse;

class MaterialController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Material::orderBy('name', 'asc')->get(), 200);
    }

    public function store(Store $request)
    {
        $data = Material::create($request->validated());

        return response()->json($data, 200);
    }

    public function update(Update $request, Material $material): JsonResponse
    {   
        $material->fill($request->validated());

        if ($material->isDirty('name')) {
            $material->slug = null;
        }

        $material->save();
     
        return response()->json($material, 200);
    }

    public function destroy(Material $material)
    {
        $material->delete();
     
        return response()->noContent();
    }
}
