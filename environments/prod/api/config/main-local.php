<?php
return [
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'oFGERegmGDMqHRnY6ZycvCT4R5NA4cYY',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
    ],
];
