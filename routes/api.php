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

Route::post('/register', [UsersController::class, 'register']);

Route::post('/login', [UsersController::class, 'login']);

Route::middleware('auth:sanctum')->get('/verify', [UsersController::class, 'verify']);


Route::group(['middleware' => 'auth:sanctum'], function() {
    Route::apiResource('sellers', SellersController::class);
    
    Route::apiResource('categories', CategoriesController::class);
    
    Route::apiResource('products', ProductsController::class);
});
