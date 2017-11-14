<?php use api\tests\ApiTester;
$I = new ApiTester($scenario);
$I->wantTo('perform actions and see result');
$I->haveHttpHeader('Content-Type', 'application/json');

$I->sendGET('/goods/view', [
    'suppress_response_code' => 1,
    'goods_id' => 1,
]);

$I->seeResponseContains('brand');