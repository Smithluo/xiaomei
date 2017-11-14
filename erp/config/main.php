<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-erp',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'erp\controllers',
    'bootstrap' => [
        'log',
        'admin',
    ],
    'modules' => [
        'admin' => [
            'class' => 'mdm\admin\Module',
            'userClassName' => 'common\models\Users',
            'idField' => 'user_id',
            'usernameField' => 'mobile_phone',
        ],
        'dynagrid'=> [
            'class' => '\kartik\dynagrid\Module',
        ],
        'gridview' => [
            'class' => '\kartik\grid\Module',
        ],
        'purchase' => [
            'class' => 'erp\modules\purchase\Module',
        ],
        'sale' => [
            'class' => 'erp\modules\sale\Module',
        ],
		'depot' => [
            'class' => 'erp\modules\depot\Module',
        ],
        'finance' => [
            'class' => 'erp\modules\finance\Module',
        ],
    ],
    'as access' => [
        'class' => 'mdm\admin\classes\AccessControl',
        'allowActions' => [
            'site/login',
            'site/logout',
//            '*',
//            'admin/*',
//            'gii/*',
//            'debug/*',
        ]
    ],
    'components' => [
        'user' => [
            'identityClass' => 'common\models\Users',
            'enableAutoLogin' => true,
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
            'errorAction' => 'site/error',
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
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
];
