<?php
namespace api\tests;
use api\tests\FunctionalTester;
use common\fixtures\User as UserFixture;

class OrderCest
{
    public function _before(FunctionalTester $I)
    {
        $I->haveFixtures([
            'user' => [
                'class' => UserFixture::className(),
                'dataFile' => codecept_data_dir() . 'login_data.php'
            ]
        ]);
    }

    /**
     * @param FunctionalTester $I
     */
    public function orderList(FunctionalTester $I)
    {
        $I->amOnPage('/order/list');
        $I->fillField('user_name', 'shiningxiao');
        $I->fillField('password', '111111');
        $I->click('login-button');

        $I->see('Logout (erau)', 'form button[type=submit]');
        $I->dontSeeLink('Login');
        $I->dontSeeLink('Signup');
    }

    public function _after(FunctionalTester $I)
    {
    }

}
