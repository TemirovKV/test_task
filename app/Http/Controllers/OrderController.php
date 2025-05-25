<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateOrderRequest;
use App\Http\Requests\GetOrderRequest;
use App\Http\Requests\GetOrdersListRequest;
use App\Http\Requests\PayOrderRequest;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusEnum;
use Exception;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
	public function getList(GetOrdersListRequest $request)
	{
		$data = $request->validated();

		$query = Order::query()
			->select(
				'orders.id',
				'orders.payment_status',
				'orders.payment_method_id'
			)
			->whereHas('items.products')
			->with('items.products', 'paymentMethods')
			->where('user_id', auth()->user()->id)
			->orderBy($data['sort'], $data['order']);

		if (isset($data['status']))
			$query->where('payment_status', $data['status']);

		$orders = $query->get()
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

	public function create(CreateOrderRequest $request)
	{
		$data = $request->validated();

		DB::beginTransaction();
		try {
			$cartItems = CartItem::query()
				->select(
					'cart_items.id',
					'cart_items.product_id',
					'cart_items.quantity'
				)
				->with('products')
				->where('user_id', auth()->user()->id)
				->get();

			if ($cartItems->isEmpty())
				return response()->json(['message' => 'cart is empty'], 400);

			$order = Order::create([
				'user_id'           => auth()->user()->id,
				'payment_status'    => OrderStatusEnum::ForPayment,
				'payment_method_id' => $data['paymentMethodId'],
			]);

			foreach ($cartItems as $cartItem)
			{
				OrderItem::create([
					'order_id'   => $order->id,
					'product_id' => $cartItem->product_id,
					'quantity'   => $cartItem->quantity,
					'price'      => $cartItem->products->price,
				]);
			}

			CartItem::query()
				->where('user_id', auth()->user()->id)
				->delete();

		} catch (Exception $e) {
			DB::rollBack();
			throw $e;
		}
		DB::commit();

		return response()->json(['payment_link' => env('APP_URL') . "/api/orders/{$order->id}/payments/{$data['paymentMethodId']}"]);
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

	public function pay(PayOrderRequest $request)
	{
		$data = $request->validated();

		$order = Order::query()
			->where([
				['user_id', auth()->user()->id],
				['id', $data['orderId']],
				['payment_method_id', $data['paymentMethodId']],
				['payment_status', OrderStatusEnum::ForPayment],
			])
			->first();

		if (!$order)
			return response()->json(['message' => 'can\'t pay order'], 400);

		$order->payment_status = OrderStatusEnum::Paid;
		$order->save();

		return response()->json(['status' => $order->payment_status]);
	}
}
