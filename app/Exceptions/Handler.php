<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class Handler extends ExceptionHandler
{
	protected $dontFlash = [
		'password',
		'auth_token',
	];

	public function register()
	{
		$this->renderable(function (Throwable $e, Request $request) {
			list($message, $code) = match (get_class($e)) {
				ValidationException::class => [
					$e->validator->errors()->getMessages(),
					422,
				],
				default => [
					$e->getMessage(),
					400,
				],
			};

			return response()->json([
				'errors' => $message,
			], $code);
		});
	}
}
