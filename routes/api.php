<?php

use App\Http\Controllers\BrandController;
use App\Http\Controllers\CarouselController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductReviewController;
use App\Http\Controllers\ProductReviewCrudAnswerController;
use App\Http\Controllers\ProductReviewCrudController;
use App\Http\Controllers\SeasonController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\SubcategoryController;
use App\Http\Controllers\UploadProductsImageController;
use App\Http\Controllers\Delivery\NovaPoshtaKeyController;
use App\Http\Controllers\Delivery\PostomatesController;
use App\Http\Controllers\Delivery\CitiesController;
use App\Http\Controllers\Delivery\GetDeliveryController;
use App\Http\Controllers\Delivery\AreasController;
use App\Http\Controllers\Delivery\BranchesController;
use App\Http\Controllers\FavoritesController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum','admin'])->group(function () {
    Route::apiResource('categories',    CategoryController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('subcategories', SubcategoryController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('brands',        BrandController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('seasons',       SeasonController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('sizes',         SizeController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('materials',     MaterialController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('carousels',     CarouselController::class)->only(['index', 'store', 'destroy']);
    Route::apiResource('products',      ProductController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
    Route::apiResource('pages',         PageController::class)->only(['index', 'store', 'update', 'destroy']);

    Route::get('uploadProductsImage/{productId}',   [UploadProductsImageController::class, 'index']);
    Route::post('uploadProductsImage',              [UploadProductsImageController::class, 'store']);
    Route::delete('uploadProductsImage/{productId}/{imageId}', [UploadProductsImageController::class, 'delete']);

    Route::apiResource('delivery/key',          NovaPoshtaKeyController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
    Route::get('delivery/get-areas',            [GetDeliveryController::class, 'getAreas']);
    Route::get('delivery/get-cities',           [GetDeliveryController::class, 'getCities']);
    Route::get('delivery/get-branches',         [GetDeliveryController::class, 'getBranches']);
    Route::get('delivery/get-postomates',       [GetDeliveryController::class, 'getPostomates']);
    Route::get('delivery/get-job-status/{log}', [GetDeliveryController::class, 'getJobStatus']);

    Route::apiResource('delivery/areas',        AreasController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('delivery/cities',       CitiesController::class)->only(['destroy']);
    Route::apiResource('delivery/branches',     BranchesController::class)->only(['destroy']);
    Route::apiResource('delivery/postomates',   PostomatesController::class)->only(['destroy']);

    Route::apiResource('crud-orders',           OrdersController::class)->only(['index', 'update', 'destroy']);
    Route::apiResource('crud-reviews',          ProductReviewCrudController::class)->only(['index', 'update', 'destroy']);
    Route::apiResource('crud-reviews-answers',  ProductReviewCrudAnswerController::class)->only(['index', 'store', 'update', 'destroy']);
    /* Upload Image From TextArea Form by TipTap */
    Route::post('upload-image',         [ImageController::class, 'upload']);
});

Route::apiResource('categories',        CategoryController::class)->only(['index', 'show']);
Route::apiResource('subcategories',     SubcategoryController::class)->only(['index', 'show']);
Route::apiResource('brands',            BrandController::class)->only('index', 'show');
Route::apiResource('seasons',           SeasonController::class)->only('index');
Route::apiResource('sizes',             SizeController::class)->only('index');
Route::apiResource('materials',         MaterialController::class)->only('index');
Route::apiResource('products',          ProductController::class)->only(['index', 'show']);
Route::apiResource('pages',             PageController::class)->only('show');

Route::get('carousels-home',                [CarouselController::class, 'getCarouselHome']);

Route::get('products-with-discount',        [ProductController::class, 'discountProducts']);

Route::get('categories/{slug}/filters',     [CategoryController::class, 'filters']);
Route::get('subcategories/{slug}/filters',  [SubcategoryController::class, 'filters']);
Route::get('brands/{slug}/filters',         [BrandController::class, 'filters']);

    /* Get Reviews By Product */
Route::get('product-reviews/{product}',         [ProductReviewController::class, 'index']);
Route::post('product-reviews/{product}',        [ProductReviewController::class, 'store']);
Route::get('product-rating-stats/{product}',    [ProductReviewController::class, 'ratingStats']);

Route::apiResource('delivery/areas',                AreasController::class)->only(['index']);
Route::apiResource('delivery/cities',               CitiesController::class)->only(['index']);
Route::apiResource('delivery/branches',             BranchesController::class)->only(['index']);
Route::apiResource('delivery/postomates',           PostomatesController::class)->only(['index']);

Route::get('delivery/get-cities-area/{areaRef}',    [CitiesController::class, 'getCitiesByArea']);
Route::get('delivery/get-branches-city/{cityRef}',  [BranchesController::class, 'getBranchesByCity']);
Route::get('delivery/get-postmates-city/{cityRef}', [PostomatesController::class, 'getPostmatesByCity']);

Route::apiResource('favorites',     FavoritesController::class)->only(['index', 'store']);

Route::post('order',        [OrderController::class, 'store']); //anly create order from front
Route::post('fast-order',   [OrderController::class, 'fastStore']); //anly create order from front

Route::get('search',        [ProductController::class, 'search']);
