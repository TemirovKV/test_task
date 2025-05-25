<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PayOrderRequest extends FormRequest
{
	public function authorize(): bool
	{
		return true;
	}

	public function rules(): array
	{
		return [
			'orderId'         => 'required|integer|exists:orders,id',
			'paymentMethodId' => 'required|integer|exists:payment_methods,id',
		];
	}

	protected function prepareForValidation(): void
	{
		$this->merge([
			'orderId'         => $this->route('orderId'),
			'paymentMethodId' => $this->route('paymentMethodId'),
		]);
	}
}
