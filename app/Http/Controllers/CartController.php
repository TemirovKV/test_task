<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCartItemRequest;
use App\Http\Requests\DeleteCartItemRequest;
use App\Models\CartItem;

class CartController extends Controller
{
	public function getItems()
	{
		$cartItems = CartItem::query()
			->select(
				'cart_items.id',
				'cart_items.product_id',
				'cart_items.quantity'
			)
			->with('products')
			->where('user_id', auth()->user()->id)
			->get()
			->toArray();

		foreach ($cartItems as &$item)
		{
			$item['title'] = $item['products']['title'];
			$item['price'] = $item['products']['price'];
			unset($item['products']);
		}

		return response()->json([
			'data' => $cartItems,
		]);
	}

	public function addItem(AddCartItemRequest $request)
	{
		$data = $request->validated();

		$cartItem = CartItem::query()
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

		$success = CartItem::query()
			->where([
				['id', '=', $data['itemId']],
				['user_id', '=', auth()->user()->id],
			])
			->delete();

		if (!$success)
			return response()->json('can\'t delete cart item', 400);

		return response(null, 204);
	}
}
