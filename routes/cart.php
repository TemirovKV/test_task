<?php

use App\Http\Controllers\CartController;
use Illuminate\Support\Facades\Route;

Route::group([
	'prefix'     => 'cart',
	'middleware' => 'auth:sanctum',
], function () {
	Route::get('/', [CartController::class, 'get']);
});
