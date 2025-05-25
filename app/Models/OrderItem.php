<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderItem extends Model
{
	protected $fillable = [
		'order_id',
		'product_id',
		'quantity',
	];

	public function products(): HasOne
	{
		return $this->hasOne(Product::class, 'id', 'product_id')
			->select('products.id', 'products.title', 'products.price');
	}
}
