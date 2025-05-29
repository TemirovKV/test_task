<?php

namespace Tests;

use Tests\Support\ApiTester;

class UserCest
{
	public function authorize(ApiTester $I)
	{
		$I->comment('Не передаём обязательные параметры.');
		$I->sendPost('/user/authorize');
		$I->seeResponseCodeIs(422);
		$I->seeResponseIsJson();
		$response = json_decode($I->grabResponse(), true);
		$I->assertEquals(
			[
				'errors' => [
					'email'    => ['The email field is required.'],
					'password' => ['The password field is required.']
				]
			],
			$response
		);

		$I->comment('Передаём невалидные параметры.');
		$I->sendPost(
			'/user/authorize',
			[
				'email'    => 'qwe',
				'password' => 'qwe',
			]
		);
		$I->seeResponseCodeIs(422);
		$I->seeResponseIsJson();
		$response = json_decode($I->grabResponse(), true);
		$I->assertEquals(
			[
				'errors' => [
					'email'    => ['The email field must be a valid email address.'],
					'password' => ['The password field must be at least 6 characters.']
				]
			],
			$response
		);

		$I->comment('Передаём нормальные данные.');
		$I->sendPost(
			'/user/authorize',
			[
				'email'    => 'example@mail.ru',
				'password' => 'example@mail.ru',
			]
		);
		$I->seeResponseCodeIs(200);
		$I->seeResponseIsJson();
		$response = json_decode($I->grabResponse(), true);
		$I->seeResponseMatchesJsonType([
			'auth_token' => 'string',
		]);
	}
}
