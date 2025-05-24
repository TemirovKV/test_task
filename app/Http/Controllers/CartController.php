<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCartItemRequest;
use App\Models\Cart;

class CartController extends Controller
{
	public function get()
	{
		$cartProducts = Cart::query()
			->select(
				'carts.id',
				'carts.product_id',
				'carts.quantity'
			)
			->with('products')
			->where('user_id', auth()->user()->id)
			->get()
			->toArray();

		foreach ($cartProducts as &$product)
		{
			$product['title'] = $product['products']['title'];
			$product['price'] = $product['products']['price'];
			unset($product['products']);
		}

		return response()->json([
			'data' => $cartProducts,
		]);
	}

	public function addItem(AddCartItemRequest $request)
	{
		$data = $request->validated();

		$cartItem = Cart::query()
			->updateOrCreate(
				[
					'user_id' => auth()->user()->id,
					'product_id' => $data['productId'],
				],
				[
					'quantity' => $data['quantity'],
				]
			);

		return response()->json($cartItem->toArray());
	}
}
