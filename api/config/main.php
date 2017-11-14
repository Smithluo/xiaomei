<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'api\controllers',
    'modules' => [
        'v1' => [
            'basePath' => '@api/modules/v1',
            'class' => 'api\modules\v1\Module',
        ],
        'v2' => [
            'basePath' => '@api/modules/v2',
            'class' => 'api\modules\v2\Module',
        ],
    ],
    'components' => [
        'user' => [
            'identityClass' => 'api\modules\v1\models\Users',
            'enableAutoLogin' => true,
            'enableSession' => false,
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
        'response' => [
            'class' => 'yii\web\Response',
            'on beforeSend' => function ($event) {
                $response = $event->sender;
                if ($response->data !== null && (!empty(Yii::$app->request->get('suppress_response_code')) || !empty(Yii::$app->request->post('suppress_response_code')))) {
                    $response->data = [
                        'success' => $response->isSuccessful,
                        'data' => $response->data,
                    ];
                    $response->statusCode = 200;
                }
                //如果是第三方通知接口，需要用raw输出一些内容告诉第三方支付成功，第三方支付就不会继续发送通知了
                if (Yii::$app->request->url == '/v1/notify/alipay-rsp'
                    || Yii::$app->request->url == '/v1/notify/wxpay-rsp'
                    || Yii::$app->request->url == '/v1/notify/yeepay-rsp'
                ) {
                    $response->format = \yii\web\Response::FORMAT_RAW;
                }
            },
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' => 'v1/activity',
                    'extraPatterns' => [
                        'GET group-buy' => 'group_buy',
                        'GET flash-sale' => 'flash_sale',
                        'GET full-gift' => 'full_gift',
                        'GET li-bao' => 'li_bao',
                        'GET full-cut' => 'full_cut',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' => 'v1/goods',
                    'extraPatterns' => [
                        'GET view' => 'view',
                        'GET view-v2' => 'view_v2',
                        'POST list' => 'list',
                        'POST select-item' => 'select_item',
                        'POST brand-goods' => 'brand_goods',
                        'POST goods-list' => 'goods_list',
                        'POST event-list' => 'event_list',
                        'POST event' => 'event',
                        'POST event-v2' => 'event_v2',
                        'POST list-v2' => 'list_v2',
                        'POST query-count' => 'query_count',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' => 'v1/user',
                    'extraPatterns' => [
                        'POST login' => 'login',
                        'POST token' => 'token',
                        'POST register' => 'register',
                        'POST send-check-no' => 'send_check_no',
                        'POST reset-password' => 'reset_password',
                        'POST upload-img' => 'upload_img',
                        'POST apply' => 'apply',
                        'GET captcha' => 'captcha',
                        'POST edit-profile' => 'edit_profile',
                        'GET edit-profile' => 'edit_profile',
                        'POST purchasing-desire' => 'purchasing_desire',
                        'GET assign-bought-goods'=> 'assign_bought_goods',
                        'POST upgrade-apply'=> 'upgrade_apply'
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' => 'v1/cart',
                    'extraPatterns' => [
                        'POST add' => 'add',
                        'POST group-add' => 'group_add',
                        'POST list' => 'list',
                        'POST list-v2' => 'list_v2',
                        'POST select' => 'select',
                        'POST unselect' => 'unselect',
                        'DELETE delete' => 'delete',
                        'POST num' => 'num',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' => 'v1/event',
                    'extraPatterns' => [
                        'POST gifts' => 'gifts',
                        'POST detail' => 'detail',
                        'POST valid-events' => 'valid_events',
                        'GET take-coupon' => 'take_coupon',
                        'GET take-coupon-by-rule' => 'take_coupon_by_rule',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' => 'v1/coupon',
                    'extraPatterns' => [
                        'GET coupon-center' => 'coupon_center',
                        'GET coupon-receive' => 'coupon_receive',
                        'GET coupon-list' => 'coupon_list',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' => 'v1/category',
                    'extraPatterns' => [
                        'POST usable' => 'usable',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' => 'v1/order',
                    'extraPatterns' => [
                        'GET view'      => 'view',
                        'POST list'     => 'list',
                        'POST group-list' => 'group_list',
                        'POST cancel'   => 'cancel',
                        'POST group-cancel' => 'group_cancel',
                        'POST checkout' => 'checkout',
                        'POST checkout-v2' => 'checkout_v2',
                        'POST create-v2' => 'create_v2',
//                        'POST cart-create' => 'cart_create',
//                        'POST general-create' => 'general_create',
//                        'POST groupbuy-create' => 'groupbuy_create',
                        'POST exchange-create' => 'exchange_create',

                        'POST cs-status-no' => 'cs_status_no',
                        'POST pay' => 'pay',
                        'POST wxpay' => 'wxpay',
                        'POST alipay' => 'alipay',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' => 'v1/notify',
                    'extraPatterns' => [
                        'POST alipay-rsp' => 'alipay_rsp',
                        'POST wxpay-rsp' => 'wxpay_rsp',
                        'POST yeepay-rsp' => 'yeepay_rsp',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' => 'v1/brand',
                    'extraPatterns' => [
                        'POST detail' => 'detail',
                        'GET view' => 'view',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' => 'v1/address',
                    'extraPatterns' => [
                        'GET list'      => 'list',
                        'POST create'   => 'create',
                        'POST edit'     => 'edit',
                        'POST default'  => 'default',
                        'POST drop'     => 'drop',
                        'POST default-info' => 'default_info',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' => 'v1/home',
                    'extraPatterns' => [
                        'GET index' => 'index',
                        'GET zhifa' => 'zhifa',
                        'GET guide-goods' => 'guide_goods',
                        'GET rank-list' => 'rank_list',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' => 'v1/customer',
                    'extraPatterns' => [
                        'PUT upgrade' => 'upgrade',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' => 'v1/user-profile',
                    'extraPatterns' => [
                        'POST edit' => 'edit',
                        'GET view' => 'view',
                        'POST servicer' => 'servicer',
                        'POST checked' => 'checked',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' => 'v1/feedback',
                    'extraPatterns' => [
                        'POST add' => 'add',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' => 'v1/integral',
                    'extraPatterns' => [
                        'GET list'      => 'list',      //  显示积分流水
                        'GET balance'   => 'balance',      //  显示积分可用余额
                        'POST create'   => 'create',    //  插入交易记录  下单或 兑换
                        'POST edit'     => 'edit',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' => 'v1/order-group',
                    'extraPatterns' => [
                        'POST list' => 'list',
                        'GET view' => 'view',
                        'POST cancel' => 'cancel',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' => 'v1/coupon-record',
                    'extraPatterns' => [
                        'POST list' => 'list',
                    ],
                ],

                'debug/<controller>/<action>' => 'debug/<controller>/<action>',
            ],
        ],
    ],
    'params' => $params,
];
