<?php

use App\Models\User;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\SellersController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\OrdersController;
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
Route::group(['prefix' => 'buyer'], function () {
    // AUTH ROUTES (buyer)
    Route::post('/register', [UsersController::class, 'register']);
    Route::post('/login', [UsersController::class, 'login']);
    Route::get('/logout', [UsersController::class, 'logout']);

    // PROTECTED ROUTES (buyer)
    Route::group(['middleware' => "auth:sanctum"], function () {
        Route::get("/orders", [UsersController::class, 'orders']);
        Route::post('/update', [UsersController::class, 'update']);
    });
});

// SELLER ROUTES
Route::group(['prefix' => 'seller'], function () {
    // PROTECTED ROUTES (seller)
    Route::group(['middleware' => ["auth:sanctum", 'is_seller']], function () {
        Route::post('/update', [SellersController::class, 'update']);
        Route::get('/products', [SellersController::class, 'products']);
        Route::get('/orders', [SellersController::class, 'orders']);
    });

    // AUTH ROUTES
    Route::post('/register', [SellersController::class, 'register']);
    Route::post('/login', [SellersController::class, 'login']);
    Route::get('/logout', [SellersController::class, 'logout']);

    // PUBLIC ROUTES
    Route::get('/', [SellersController::class, 'index']);
    Route::get('/{id}', [SellersController::class, 'show']);
});
// -------------- end


// PROTECTED ROUTES 
Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/verify', [UsersController::class, 'verify']);
    Route::post("/purchase", [UsersController::class, 'purchase']);
});
// --------------- end


// PRODUCTS
Route::group(['prefix' => 'products'], function () {
    Route::group(['middleware' => "auth:sanctum"], function () {
        Route::post("/", [ProductsController::class, "store"]);
        Route::post("/review", [ProductsController::class, 'review']);
    });

    Route::get("/{id}", [ProductsController::class, "show"]);
    Route::get('/search/{name}', [ProductsController::class, 'search']);
    Route::get("/", [ProductsController::class, 'index']);
});
// ------------- end


// ORDERS
Route::group(['prefix' => 'orders', 'middleware' => 'is_seller'], function() {
    Route::get('/', [OrdersController::class, 'index']);
    Route::patch('/update/{id}', [OrdersController::class, 'update']);
});


Route::get('/offers/{title}', [OfferController::class, 'products']);
Route::apiResource('offers', OfferController::class);

Route::middleware("auth:sanctum")->apiResource('categories', CategoriesController::class);
