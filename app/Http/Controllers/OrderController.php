<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetOrderRequest;
use App\Models\Order;

class OrderController extends Controller
{
	public function getList()
	{
		$orders = Order::query()
			->select(
				'orders.id',
				'orders.payment_status',
				'orders.payment_method_id'
			)
			->whereHas('items.products')
			->with('items.products', 'paymentMethods')
			->where('user_id', auth()->user()->id)
			->get()
			->toArray();

		foreach ($orders as &$order)
		{
			foreach ($order['items'] as &$item)
			{
				$item['title'] = $item['products']['title'];
				$item['price'] = $item['products']['price'];
				unset($item['products']);
			}
			unset($order['payment_method_id']);
		}

		return response()->json([
			'data' => $orders,
		]);
	}

	public function get(GetOrderRequest $request)
	{
		$data = $request->validated();

		$order = Order::query()
			->select(
				'orders.id',
				'orders.payment_status',
				'orders.payment_method_id'
			)
			->whereHas('items.products')
			->with('items.products', 'paymentMethods')
			->where([
				['user_id', auth()->user()->id],
				['id', $data['orderId']],
			])
			->first()
			->toArray();

		if (!$order)
			return response()->json(['message' => 'order not found'], 404);

		foreach ($order['items'] as &$item)
		{
			$item['title'] = $item['products']['title'];
			$item['price'] = $item['products']['price'];
			unset($item['products']);
		}
		unset($order['payment_method_id']);

		return response()->json($order);
	}
}
