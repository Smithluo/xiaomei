<?php
defined('YII_DEBUG') or define('YII_DEBUG', false);
defined('YII_ENV') or define('YII_ENV', 'prod');

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

Yii::$classMap['AopClient'] = __DIR__. '/../../vendor/alipay/aop/AopClient.php';
Yii::$classMap['AlipayTradeQueryRequest'] = __DIR__. '/../../vendor/alipay/aop/request/AlipayTradeQueryRequest.php';
Yii::$classMap['AlipayTradeWapPayRequest'] = __DIR__. '/../../vendor/alipay/aop/request/AlipayTradeWapPayRequest.php';
Yii::$classMap['SignData'] = __DIR__. '/../../vendor/alipay/aop/SignData.php';

//易宝支付
Yii::$classMap['yeepayMPay'] = __DIR__. '/../../vendor/yeepay/yeepayMPay.php';

$application = new yii\web\Application($config);
//	给yii2外部环境调用Model，这里不执行run() 方法
// $application->run();		
