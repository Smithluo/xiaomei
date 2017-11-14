<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'modules' => [
        'admin' => [
            'class' => 'mdm\admin\Module',
            'userClassName' => 'common\models\Users',
            'idField' => 'user_id',
            'usernameField' => 'mobile_phone',
        ],
    ],
    'id' => 'app-data',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
        'log',
        'admin',
    ],
    'controllerNamespace' => 'data\controllers',
    'as access' => [
        'class' => 'data\controllers\DataAccessControl',
//        'class' => 'mdm\admin\classes\AccessControl',
        'allowActions' => [
            'data-user/login-user',
//            'data-user/auth-user',
//            'data-data/set-data',
//            '*',
//            'admin/*',
//            'gii/*',
//            'debug/*',
        ]
    ],
    'components' => [
        'request' => [
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
                'text/json' => 'yii\web\JsonParser',
            ],
        ],
        'response' => [
            'class' => 'yii\web\Response',
            'on beforeSend' => function ($event) {
                $response = $event->sender;
                if ($response->statusCode != 200) {
                    $response->data = [
                        'code' => $response->statusCode,
                        'msg' => $response->data['message'],
                    ];
                    $response->statusCode = 200;
                }
            },
        ],
        'redis' =>[
            'class' => 'yii\redis\Connection',
            'hostname' => '127.0.0.1',
            'port' => 6379,
            'database' => 15,
        ],
        'user' => [
            'identityClass' => 'common\models\Users',
            'enableAutoLogin' => true,
            'enableSession' => true,
            'loginUrl' => null,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'data-site/error',
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
//            'suffix' => '.html',
            'rules' => [
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ],
        ],
        /*
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        */
    ],

    'params' => $params,
];
