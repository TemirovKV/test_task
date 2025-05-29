<?php

namespace Tests;

use Tests\Support\ApiTester;

class CartCest
{
	public function getItems(ApiTester $I)
	{
		$I->comment('Пытаемся получить корзину неавторизованным пользователем.');
		$I->sendGet('/cart');
		$I->seeResponseCodeIs(400);
		$I->seeResponseIsJson();

		$I->comment('Авторизуемся.');
		$I->authorize();

		$I->comment('Пытаемся получить пустую корзину авторизованным пользователем.');
		$I->sendGet('/cart');
		$I->seeResponseCodeIs(200);
		$I->seeResponseIsJson();
		$response = json_decode($I->grabResponse(), true);
		$I->assertEquals([], $response['data']);

		$I->comment('Добавим несколько товаров в корзину');
		$I->haveInDatabase(
			'cart_items',
			[
				'user_id'    => 1,
				'product_id' => 1,
				'quantity'   => 10,
			]
		);
		$I->haveInDatabase(
			'cart_items',
			[
				'user_id'    => 1,
				'product_id' => 2,
				'quantity'   => 20,
			]
		);

		$I->comment('Пытаемся получить пустую корзину авторизованным пользователем.');
		$I->sendGet('/cart');
		$I->seeResponseCodeIs(200);
		$I->seeResponseIsJson();
		$response = json_decode($I->grabResponse(), true);
		$I->assertEquals(
			[
				"data" => [
					[
						'id'         => 5,
						'product_id' => 1,
						'quantity'   => 10,
						'title'      => 'Товар1',
						'price'      => '1000.10',
					],
					[
						'id'         => 6,
						'product_id' => 2,
						'quantity'   => 20,
						'title'      => 'Товар2',
						'price'      => '2000.00',
					]
				]
			],
			$response
		);
	}

	public function addItem(ApiTester $I)
	{
		$I->comment('Пытаемся добавить товары в корзину неавторизованным пользователем.');
		$I->sendPost('/cart/items');
		$I->seeResponseCodeIs(400);
		$I->seeResponseIsJson();

		$I->comment('Авторизуемся.');
		$I->authorize();

		$I->comment('Не передаём обязательные параметры.');
		$I->sendPost('/cart/items');
		$I->seeResponseCodeIs(422);
		$I->seeResponseIsJson();
		$response = json_decode($I->grabResponse(), true);
		$I->assertEquals(
			[
				'errors' => [
					'productId' => ['The product id field is required.'],
					'quantity'  => ['The quantity field is required.'],
				]
			],
			$response
		);

		$I->comment('Передаём невалидные параметры.');
		$I->sendPost('/cart/items', [
			'productId' => 9999,
			'quantity'  => 0,
		]);
		$I->seeResponseCodeIs(422);
		$I->seeResponseIsJson();
		$response = json_decode($I->grabResponse(), true);
		$I->assertEquals(
			[
				'errors' => [
					'productId' => ['The selected product id is invalid.'],
					'quantity'  => ['The quantity field must be at least 1.'],
				]
			],
			$response
		);

		$I->comment('Валидный заказ.');
		$I->sendPost('/cart/items', [
			'productId' => 1,
			'quantity'  => 10,
		]);
		$I->seeResponseCodeIs(201);
		$I->seeResponseIsJson();
		$response = json_decode($I->grabResponse(), true);
		$I->seeResponseContainsJson(
			[
				'user_id' => 1,
				'product_id' => '1',
				'quantity' => '10',
				'updated_at' => date('Y-m-d\TH:i:s.u\Z'),
				'created_at' => date('Y-m-d\TH:i:s.u\Z'),
			]
		);

		$I->comment('Проверим что в БД появилась запись.');
		$I->seeInDatabase('cart_items', [
			'user_id'    => 1,
			'product_id' => '1',
			'quantity'   => '10',
			'updated_at' => date('Y-m-d\TH:i:s.u\Z'),
			'created_at' => date('Y-m-d\TH:i:s.u\Z'),
		]);
	}

	public function deleteItem(ApiTester $I)
	{
		$I->comment('Пытаемся удалить товар неавторизованным пользователем.');
		$I->sendDelete('/cart/items/1');
		$I->seeResponseCodeIs(400);
		$I->seeResponseIsJson();

		$I->comment('Авторизуемся.');
		$I->authorize();

		$I->comment('Передаём неправильный id.');
		$I->sendDelete('/cart/items/9999');
		$I->seeResponseCodeIs(422);
		$I->seeResponseIsJson();
		$response = json_decode($I->grabResponse(), true);
		$I->assertEquals(
			[
				'errors' => [
					'itemId' => ['The selected item id is invalid.'],
				]
			],
			$response
		);

		$I->comment('Пытаемся удалить товар из чужой корзины.');
		$I->sendDelete('/cart/items/1');
		$I->seeResponseCodeIs(400);
		$I->seeResponseIsJson();
		$response = json_decode($I->grabResponse(), true);
		$I->assertEquals('can\'t delete cart item', $response);

		$I->comment('Добавим несколько товаров в корзину');
		$I->haveInDatabase(
			'cart_items',
			[
				'id'         => 100,
				'user_id'    => 1,
				'product_id' => 1,
				'quantity'   => 10,
			]
		);
		$I->haveInDatabase(
			'cart_items',
			[
				'id'         => 101,
				'user_id'    => 1,
				'product_id' => 2,
				'quantity'   => 20,
			]
		);

		$I->comment('Номральные данные.');
		$I->sendDelete('/cart/items/100');
		$I->seeResponseCodeIs(204);
		$I->assertEquals(null, $I->grabResponse());

		$I->comment('Проверим что из корзины удалился один товар, а второй остался там');
		$I->seeInDatabase('cart_items', [
			'id'         => 101,
			'user_id'    => 1,
			'product_id' => 2,
			'quantity'   => 20,
		]);
		$I->dontSeeInDatabase('cart_items', [
			'id' => 100,
		]);
	}
}
