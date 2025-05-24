<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetProductsListRequest extends FormRequest
{
	public function authorize(): bool
	{
		return true;
	}

	public function rules(): array
	{
		return [
			'sort'  => 'required|string|in:price',
			'order' => 'required|string|in:ASC,DESC',
		];
	}
}
