<?php use api\tests\ApiTester;
$I = new ApiTester($scenario);
$I->wantTo('register a new user');
$I->haveHttpHeader('Content-Type', 'application/json');

$userName = 'shiningxiao0120';
$mobilePhone = '13510600120';
$password = '111111';

$I->sendPOST('/user/register', [
    'suppress_response_code' => 1,
    'data' => [
        'username' => $userName,
        'mobile' => $mobilePhone,
        'password' => $password,
        'company_name' => '众里',
        'province' => 1,
        'city' => 1,
    ]
]);

$I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
$I->seeResponseIsJson();
$I->canSeeResponseContainsJson([
    'success' => true,
]);

$I->canSeeResponseContainsJson([
    'user_name' => $userName,
]);

$I->canSeeResponseContainsJson([
    'mobile_phone' => $mobilePhone,
]);

$I->canSeeResponseContainsJson([
    'user_rank' => 1,
]);

$I->canSeeResponseContainsJson([
    'is_checked' => 0,
]);

$I->seeResponseContains('user_id');
$I->seeResponseContains('access_token');

//登录测试
$I->wantTo('login use '. $userName. ': '. $password);

$I->amHttpAuthenticated($userName, $password);
$I->sendPOST('/user/login', [
    'suppress_response_code' => 1,
]);

$I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
$I->seeResponseIsJson();
$I->canSeeResponseContainsJson([
    'success' => true,
]);

$I->canSeeResponseContainsJson([
    'user_name' => $userName,
]);

$I->canSeeResponseContainsJson([
    'mobile_phone' => $mobilePhone,
]);

$I->seeResponseContains('access_token');

