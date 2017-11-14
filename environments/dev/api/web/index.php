<?php
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require(__DIR__ . '/../../vendor/autoload.php');
require(__DIR__ . '/../../vendor/yiisoft/yii2/Yii.php');
require(__DIR__ . '/../../common/config/bootstrap.php');
require(__DIR__ . '/../config/bootstrap.php');

$config = yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../../common/config/main.php'),
    require(__DIR__ . '/../../common/config/main-local.php'),
    require(__DIR__ . '/../config/main.php'),
    require(__DIR__ . '/../config/main-local.php')
);

//下面是支付宝,只用到了AopClient的签名接口
Yii::$classMap['AopClient'] = __DIR__. '/../../vendor/alipay/aop/AopClient.php';
Yii::$classMap['AlipayTradeQueryRequest'] = __DIR__. '/../../vendor/alipay/aop/request/AlipayTradeQueryRequest.php';
Yii::$classMap['SignData'] = __DIR__. '/../../vendor/alipay/aop/SignData.php';

//下面是微信支付
Yii::$classMap['WxPayApi'] = __DIR__. '/../../vendor/wxpay/WxPay.Api.php';
Yii::$classMap['WxPayDataBase'] = __DIR__. '/../../vendor/wxpay/WxPay.Data.php';
Yii::$classMap['WxPayUnifiedOrder'] = __DIR__. '/../../vendor/wxpay/WxPay.Data.php';
Yii::$classMap['WxPayNotifyReply'] = __DIR__. '/../../vendor/wxpay/WxPay.Data.php';
Yii::$classMap['WxPayNotify'] = __DIR__. '/../../vendor/wxpay/WxPay.Notify.php';

//短信
Yii::$classMap['ChuanglanSMS'] = __DIR__. '/../../vendor/chuanglan/ChuanglanSMS.php';

//易宝支付
Yii::$classMap['yeepayMPay'] = __DIR__. '/../../vendor/yeepay/yeepayMPay.php';

$application = new yii\web\Application($config);
$application->run();
