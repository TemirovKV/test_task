<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetProductRequest extends FormRequest
{
	public function authorize(): bool
	{
		return true;
	}

	public function rules(): array
	{
		return [
			'productId' => 'required|integer|exists:products,id',
		];
	}

	protected function prepareForValidation(): void
	{
		$this->merge([
			'productId' => $this->route('productId'),
		]);
	}
}
