<?php

$config = [
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '-tGmbGTZaluKRS3H8ZLY-9cXh7GICeNp',
        ],
    ],
];

if (!YII_ENV_TEST) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'generators' => [
            'jadecrud' => [
                'class' => 'jacmoe\giijade\crud\Generator',
                'templates' => [
                    'myCrud' => '@jacmoe/giijade/crud/default',
                ]
            ]
        ],
    ];
}

return $config;
