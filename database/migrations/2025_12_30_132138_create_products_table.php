<?php

use App\Models\Brand;
use App\Models\Category;
use App\Models\Material;
use App\Models\Season;
use App\Models\Subcategory;
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
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Category::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(Subcategory::class)->nullable()->constrained()->onDelete('cascade');
            $table->foreignIdFor(Brand::class)->nullable()->constrained()->onDelete('cascade');
            $table->foreignIdFor(Season::class)->nullable()->constrained()->onDelete('cascade');
            $table->foreignIdFor(Material::class)->nullable()->constrained()->onDelete('cascade');

            $table->string('name', 256)->unique();
            $table->string('slug')->unique();
            $table->string('article')->unique();
            $table->text('description')->nullable();

            $table->decimal('price', 10,2);
            $table->integer('discount')->nullable();
            $table->integer('discounted_price')->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
