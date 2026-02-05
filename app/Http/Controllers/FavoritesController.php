<?php

namespace App\Http\Controllers;

use App\Http\Requests\Favorite\Store;
use Illuminate\Support\Facades\Session;
use App\Models\Favorite;

class FavoritesController extends Controller
{
    public function index()
    {
        $sessionId = Session::getId();
        
        return response()->json(Favorite::where('session_id', $sessionId)->with([
            'product.images',
            'product.sizes',
        ])->get(), 200);
    }

    public function store(Store $request)
    {
        $sessionId = Session::getId();
        $productId = $request->input('product_id');

        $query = Favorite::where('product_id', $productId);
        $query->where('session_id', $sessionId);
        

        $existingFavorite = $query->first();

        if ($existingFavorite) {
            $existingFavorite->delete();

            return response()->json($existingFavorite, 200); 
        } else {
            $favorite = Favorite::create([
                'session_id' => $sessionId,
                'product_id' => $productId
            ]);

            return response()->json($favorite, 201); 
        }
    }
}
