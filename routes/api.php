<?php

use App\Models\User;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\SellersController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\ReviewsController;
use App\Http\Controllers\UsersController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::post("/test", [UsersController::class, 'test']);

// BUYER ROUTES 
Route::prefix('buyer')->group(function () {
    // AUTH ROUTES (buyer)
    Route::post('/register', [UsersController::class, 'register']);
    Route::post('/login', [UsersController::class, 'login']);
    Route::get('/logout', [UsersController::class, 'logout']);

    // PROTECTED ROUTES (buyer)
    Route::middleware("auth:sanctum")->group(function() {
        Route::get("/orders", [UsersController::class, 'orders']);
        Route::post('/update', [UsersController::class, 'update']);
    });
});

// SELLER ROUTES
Route::prefix('seller')->group(function () {
    Route::post('/register', [SellersController::class, 'register']);
    Route::post('/login', [SellersController::class, 'login']);
    Route::get('/logout', [SellersController::class, 'logout']);

    // PROTECTED ROUTES (seller)
    Route::middleware("auth:sanctum")->group(function() {
        Route::post('/update', [SellersController::class, 'update']);
    });
});

Route::get('/sellers', [SellersController::class, 'index']);
Route::get('/sellers/{id}', [SellersController::class, 'show']);
// -------------- end


// PROTECTED ROUTES 
Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/verify', [UsersController::class, 'verify']);
    Route::post("/purchase", [UsersController::class, 'purchase']);
});
// --------------- end



Route::get('/offers/{title}', [OfferController::class, 'products']);
Route::apiResource('offers', OfferController::class);

Route::apiResource('categories', CategoriesController::class);

Route::get('/products/search/{name}', [ProductsController::class, 'search']);
Route::post("/product/review", [ProductsController::class, 'review']);
Route::apiResource('products', ProductsController::class);