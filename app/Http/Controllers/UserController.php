<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthorizeRequest;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
	public function authorize(AuthorizeRequest $request)
	{
		$credentials = $request->validated();

		if (!Auth::attempt($credentials))
			return response()->json(['message' => 'Неверные учетные данные'], 401);

		$user = Auth::user();
		$token = $user->createToken('auth_token')->plainTextToken;

		return response()->json([
			'auth_token' => $token,
		]);
	}
}
