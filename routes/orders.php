<?php

use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::group([
	'prefix'     => 'orders',
	'middleware' => 'auth:sanctum',
], function () {
	Route::get('/', [OrderController::class, 'getList']);
	Route::post('/', [OrderController::class, 'create']);
	Route::get('/{orderId}', [OrderController::class, 'get']);
});
