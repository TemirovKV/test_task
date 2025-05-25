<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
	protected $fillable = [
		'user_id',
		'payment_status',
	];

	public function items(): HasMany
	{
		return $this->hasMany(OrderItem::class, 'order_id')
			->select('order_items.id', 'order_items.order_id', 'order_items.product_id', 'order_items.quantity');
	}

	public function paymentMethods(): HasOne
	{
		return $this->hasOne(PaymentMethod::class, 'id', 'payment_method_id')
			->select('payment_methods.id', 'payment_methods.title');
	}
}
