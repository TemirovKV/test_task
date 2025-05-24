<?php

namespace App\Http\Controllers;

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
}
