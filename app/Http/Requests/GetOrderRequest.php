<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetOrderRequest extends FormRequest
{
	public function authorize(): bool
	{
		return true;
	}

	public function rules(): array
	{
		return [
			'orderId' => 'required|integer|exists:orders,id',
		];
	}

	protected function prepareForValidation(): void
	{
		$this->merge([
			'orderId' => $this->route('orderId'),
		]);
	}
}
