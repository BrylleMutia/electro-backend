<?php

use App\Models\User;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\SellersController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\OfferController;
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

// PUBLIC ROUTES
Route::prefix('buyer')->group(function () {
    Route::post('/register', [UsersController::class, 'register']);
    Route::post('/login', [UsersController::class, 'login']);
    Route::post('/logout', [UsersController::class, 'logout']);
    Route::middleware('auth:sanctum')->get('/verify', [UsersController::class, 'verify']);
});

Route::prefix('seller')->group(function () {
    Route::post('/register', [SellersController::class, 'register']);
    Route::post('/login', [SellersController::class, 'login']);
    Route::post('/logout', [SellersController::class, 'logout']);
    Route::middleware('auth:sanctum')->get('/verify', [SellersController::class, 'verify']);
});

Route::get('/sellers', [SellersController::class, 'index']);
Route::get('/sellers/{id}', [SellersController::class, 'show']);

Route::get('/offers/{title}', [OfferController::class, 'products']);
Route::apiResource('offers', OfferController::class);

Route::apiResource('categories', CategoriesController::class);

Route::get('/products/search/{name}', [ProductsController::class, 'search']);
Route::apiResource('products', ProductsController::class);


// PROTECTED ROUTES
Route::group(['middleware' => 'auth:sanctum'], function () {
    //
});
