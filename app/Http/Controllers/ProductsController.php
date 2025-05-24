<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetProductRequest;
use App\Http\Requests\GetProductsListRequest;
use App\Models\Product;

class ProductsController extends Controller
{
	public function getList(GetProductsListRequest $request)
	{
		$requestData = $request->validated();

		$products = Product::query()
			->select('id', 'title', 'price')
			->orderBy($requestData['sort'], $requestData['order'])
			->get()
			->toArray();

		return response()->json([
			'data' => $products,
		]);
	}

	public function get(GetProductRequest $request)
	{
		$requestData = $request->validated();

		$product = Product::query()
			->select('id', 'title', 'price')
			->where('id', $requestData['productId'])
			->first()
			->toArray();

		return response()->json([
			'data' => $product,
		]);
	}
}
