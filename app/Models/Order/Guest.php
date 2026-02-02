<?php

namespace App\Models\Order;

use App\Models\User;
use App\Models\Order\Cart;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model {
    protected $fillable = [
        'name', 'surname', 'middle_name', 'phone',
        'area', 'area_ref', 'city', 'city_ref',
        'branch', 'branch_ref', 'postomat', 'postomat_ref', 
        'user_id'
    ];

    public function cart() {
        return $this->hasOne(Cart::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}