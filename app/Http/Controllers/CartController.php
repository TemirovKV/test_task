<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCartItemRequest;
use App\Http\Requests\DeleteCartItemRequest;
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

		return response()->json($cartItem->toArray(), 201);
	}

	public function deleteItem(DeleteCartItemRequest $request)
	{
		$data = $request->validated();

		$success = Cart::query()
			->where('id', $data['itemId'])
			->delete();

		if (!$success)
			return response()->json('can\'t delete cart item', 400);

		return response(null, 204);
	}
}
