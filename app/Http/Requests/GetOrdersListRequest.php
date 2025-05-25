<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetOrdersListRequest extends FormRequest
{
	public function authorize(): bool
	{
		return true;
	}

	public function rules(): array
	{
		return [
			'sort'   => 'required|string|in:created_at',
			'order'  => 'required|string|in:ASC,DESC',
			'status' => 'sometimes|string|in:for payment,paid,cancelled',
		];
	}
}
