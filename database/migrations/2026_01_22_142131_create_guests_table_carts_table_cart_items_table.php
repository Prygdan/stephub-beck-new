<?php

use App\Models\Order\Cart;
use App\Models\Product;
use App\Models\Size;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('guests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('surname');
            $table->string('middle_name')->nullable();
            $table->string('phone');
            $table->string('area')->nullable();
            $table->string('area_ref')->nullable();
            $table->string('city')->nullable();
            $table->string('city_ref')->nullable();
            $table->string('branch')->nullable();
            $table->string('branch_ref')->nullable();
            $table->string('postomat')->nullable();
            $table->string('postomat_ref')->nullable();
            /* Is migartion for add 'user_id' */
            $table->timestamps();
        });

        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('guest_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('session_id')->nullable();
            $table->decimal('total_price', 10, 2)->default(0);
            $table->integer('status')->default(1);
            $table->string('comment')->nullable();
            $table->string('payment_method')->nullable();
            $table->timestamps();
        });

        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Cart::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(Product::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(Size::class)->nullable()->constrained()->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('carts');
        Schema::dropIfExists('guests');
    }
};
