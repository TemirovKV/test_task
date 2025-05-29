<?php

declare(strict_types=1);

namespace Tests\Support;

/**
 * Inherited Methods
 * @method void wantTo($text)
 * @method void wantToTest($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause($vars = [])
 *
 * @SuppressWarnings(PHPMD)
*/
class ApiTester extends \Codeception\Actor
{
    use _generated\ApiTesterActions;

	/**
     * Авторизация. Вызывает стандартный метод amBearerAuthenticated с переданный токеном.
     * По умолчанию использует токен для авторизации под пользователем с id=1
     */
    public function authorize($accessToken='3|45GPI7Ff4c1tgQ57SEX4m1TvJfozM0X6QTrxoioX12dfc5e8')
    {
        $this->amBearerAuthenticated($accessToken);
    }
}
