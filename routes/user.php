<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('user')->group(function () {
	Route::post('authorize', [UserController::class, 'authorize']);
});
