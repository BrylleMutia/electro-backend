<?php

use App\Models\User;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\SellersController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\CategoriesController;
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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/user/register', [UsersController::class, 'register']);
Route::post('/user/login', [UsersController::class, 'login']);

Route::post('/seller/register', [SellersController::class, 'register']);
Route::post('/seller/login', [SellersController::class, 'login']);

Route::middleware('auth:sanctum')->get('/verify', [UsersController::class, 'verify']);


Route::group(['middleware' => 'auth:sanctum'], function() {
    Route::get('/sellers', [SellersController::class, 'index']);
    Route::get('/sellers/{id}', [SellersController::class, 'show']);
    
    Route::apiResource('categories', CategoriesController::class);
    
    Route::apiResource('products', ProductsController::class);
});
