<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Models\Delivery\NovaPoshtaKey;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NovaPoshtaKeyController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(NovaPoshtaKey::paginate(100), 200);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'value' => ['required', 'string']
        ]);

        $key = NovaPoshtaKey::create(['value' => $data['value']]);

        return response()->json($key, 201);
    }

    public function update(Request $request, NovaPoshtaKey $key): JsonResponse
    {
        $data = $request->validate([
            'value' => ['required', 'string']
        ]);

        $key->update(['value' => $data['value']]);

        return response()->json($key, 201);
    }

    public function destroy(NovaPoshtaKey $key): JsonResponse
    {
        $key->delete();

        return response()->json(['message' => 'Key deleted successfully'], 200);
    }
}
