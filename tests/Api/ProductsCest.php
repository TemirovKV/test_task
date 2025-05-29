<?php

namespace Tests;

use Tests\Support\ApiTester;

class ProductsCest
{
	public function getList(ApiTester $I)
	{
		$I->comment('Не передаём обязательные параметры.');
		$I->sendGet('/products');
		$I->seeResponseCodeIs(422);
		$I->seeResponseIsJson();
		$response = json_decode($I->grabResponse(), true);
		$I->assertEquals(
			[
				'errors' => [
					'sort'  => ['The sort field is required.'],
					'order' => ['The order field is required.']
				]
			],
			$response
		);

		$I->comment('Передаём невалидные параметры.');
		$I->sendGet(
			'/products',
			[
				'sort'  => 'qwe',
				'order' => 'qwe',
			]
		);
		$I->seeResponseCodeIs(422);
		$I->seeResponseIsJson();
		$response = json_decode($I->grabResponse(), true);
		$I->assertEquals(
			[
				'errors' => [
					'sort'  => ['The selected sort is invalid.'],
					'order' => ['The selected order is invalid.']
				]
			],
			$response
		);

		$I->comment('Передаём нормальные данные.');
		$I->sendGet(
			'/products',
			[
				'sort'  => 'price',
				'order' => 'DESC',
			]
		);
		$I->seeResponseCodeIs(200);
		$I->seeResponseIsJson();
		$I->seeResponseMatchesJsonType(
			[
				'id'    => 'integer',
				'title' => 'string',
				'price' => 'string',
			],
			'$data.[*]'
		);
	}

	public function getById(ApiTester $I)
	{
		$I->comment('Передаём несуществующий id.');
		$I->sendGet('/products/9999');
		$I->seeResponseCodeIs(422);
		$I->seeResponseIsJson();
		$response = json_decode($I->grabResponse(), true);
		$I->assertEquals(
			[
				'errors' => [
					'productId'  => ['The selected product id is invalid.']
				]
			],
			$response
		);

		$I->comment('Передаём нормальные данные.');
		$I->sendGet('/products/1');
		$I->seeResponseCodeIs(200);
		$I->seeResponseIsJson();
		$response = json_decode($I->grabResponse(), true);
		$I->assertEquals(
			[
				'id'    => 1,
				'title' => 'Товар1',
				'price' => '1000.10',
			],
			$response['data']
		);
	}
}
