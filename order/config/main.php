<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-order',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
        'log',
        'admin',
    ],
    'defaultRoute' => 'order-site',
    'controllerNamespace' => 'order\controllers',
    'components' => [
        'user' => [
            'identityClass' => 'common\models\OrderUser',
            'loginUrl' => ['order-site/login'],
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'log' => [
            'flushInterval' => 1,
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'info'],
                    'exportInterval' => 1,
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'order-site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules'=>[
                '<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
            ],
        ],
    ],
    'params' => $params,
    'language' => 'zh-CN',

    'modules' => [
        'admin' => [
            'class' => 'mdm\admin\Module',
            'userClassName' => 'common\models\OrderUser',
            'idField' => 'user_id',
            'usernameField' => 'mobile_phone',
        ],
        'dynagrid'=> [
            'class' => '\kartik\dynagrid\Module',
        ],
        'gridview' => [
            'class' => '\kartik\grid\Module',
        ],
    ],
    'as access' => [
        'class' => 'mdm\admin\classes\AccessControl',
        'allowActions' => [
            'order-site/login',
            'order-site/logout',
//            'admin/*',
        ]
    ],
];
