<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\PriceProductController;

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



Route::middleware('auth:sanctum')->group(function ()
{
    Route::group([
        'as' =>  'api.v1.'
    ], function()
    {
		Route::get('products', [ProductController::class, 'index'])->name('products.index');
		Route::get('products/{product:id}', [ProductController::class, 'show'])->name('products.show');	
        Route::post('products',[ProductController::class, 'store'])->name('products.store');
		Route::put('products/{product:id}',[ProductController::class, 'update'])->name('products.update');
		Route::delete('products/{product:id}', [ProductController::class, 'destroy'])->name('products.delete');

		Route::put('prices/{product:id}', [PriceProductController::class, 'update'])->name('prices.update');
    });
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

