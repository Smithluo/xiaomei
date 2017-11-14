<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-service',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
        'log',
        'admin',
    ],
    'defaultRoute' => 'service-site',
    'controllerNamespace' => 'service\controllers',
    'components' => [
        'user' => [
            'identityClass' => 'common\models\ServiceUser',
            'loginUrl' => ['service-site/login'],
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
            'errorAction' => 'service-site/error',
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
            'userClassName' => 'common\models\ServiceUser',
            'idField' => 'user_id',
            'usernameField' => 'user_name',
        ],
    ],
    'as access' => [
        'class' => 'mdm\admin\classes\AccessControl',
        'allowActions' => [
            'service-site/login',
            'service-site/logout',
            'service-site/admin-login',
//            'admin/*',
        ]
    ],
];
