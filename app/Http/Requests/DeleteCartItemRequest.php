<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteCartItemRequest extends FormRequest
{
	public function authorize(): bool
	{
		return true;
	}

	public function rules(): array
	{
		return [
			'itemId' => 'required|integer|exists:cart_items,id',
		];
	}

	protected function prepareForValidation(): void
	{
		$this->merge([
			'itemId' => $this->route('itemId'),
		]);
	}
}
