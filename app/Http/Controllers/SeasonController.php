<?php

namespace App\Http\Controllers;

use App\Http\Requests\Season\Store;
use App\Http\Requests\Season\Update;
use Illuminate\Http\JsonResponse;
use App\Models\Season;

class SeasonController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Season::orderBy('name', 'asc')->get(), 200);
    }

    public function store(Store $request)
    {
        $data = Season::create($request->validated());

        return response()->json($data, 200);
    }

    public function update(Update $request, Season $season): JsonResponse
    {   
        $season->fill($request->validated());

        if ($season->isDirty('name')) {
            $season->slug = null;
        }

        $season->save();
     
        return response()->json($season, 200);
    }

    public function destroy(Season $season)
    {
        $season->delete();
     
        return response()->noContent();
    }
}
