<?php

use App\Http\Controllers\ProductsController;
use Illuminate\Support\Facades\Route;

Route::group([
	'prefix' => 'products',
], function () {
	Route::get('/', [ProductsController::class, 'getList']);
	Route::get('/{productId}', [ProductsController::class, 'get']);
});
