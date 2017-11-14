<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
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
        'dashboard' => [
            'class' => 'backend\modules\dashboard\Module',
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
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'maxFileSize' => 102400 //  in KB
                ],
            ],

        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
	    'urlManager' => [
    		'enablePrettyUrl' => true,
    		'showScriptName' => false,
    		'rules'=>[
        		'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
    		],
	    ],
        'wechat' => [
            'class' => 'callmez\wechat\sdk\Wechat',
            'appId' => 'wx4b90a34dd6723b3d',
            'appSecret' => '7ac7557b576dc4e174dd533903906073',
            'token' => 'xiaomei360',
        ],
        'assetManager' => [
            'appendTimestamp' => true,
        ],
    ],
    'params' => $params,
    'language' => 'zh-CN',
];
