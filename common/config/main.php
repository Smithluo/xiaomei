<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
            'cachePath' => '@runtime/cache',
        ],
//        'authManager' => [
//            'class' => 'yii\rbac\DbManager',
//        ],
    ],
    'timeZone'=>'Asia/Shanghai',

//    'modules' => [
//        'admin' => [
//            'class' => 'mdm\admin\Module',
//            'userClassName' => 'backend\models\AdminUser',
//            'idField' => 'user_id',
//            'usernameField' => 'user_name',
//        ],
//    ],
];
