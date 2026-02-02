<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;
use App\Models\Order\Guest;
use App\Models\User;

class Cart extends Model {
    protected $guarded = [];

    public function guest() {
        return $this->belongsTo(Guest::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function items() {
        return $this->hasMany(CartItem::class);
    }
}