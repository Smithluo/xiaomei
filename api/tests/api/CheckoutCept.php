<?php use api\tests\ApiTester;
$I = new ApiTester($scenario);
$I->wantTo('checkout cart');

$flowType = \common\models\Cart::CART_GENERAL_GOODS;
$addressId = 608;

$I->sendPOST('/order/checkout');