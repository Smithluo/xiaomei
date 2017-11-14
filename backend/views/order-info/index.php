<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\dynagrid\DynaGrid;
use common\helper\DateTimeHelper;
use common\models\OrderInfo;

/* @var $this yii\web\View */
/* @var $searchModel common\models\OrderInfoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '订单列表';
$this->params['breadcrumbs'][] = $this->title;
$extensionCodeMap = OrderInfo::$extensionCodeMap;
?>
<div class="order-info-index">

    <?= $this->render('_search', [
        'model' => $searchModel,
        'paymentMap' => $paymentMap,
        'params' => $params,
    ]) ?>

    <div class="form-group">
        <?php
        echo Html::a('导出当前搜索到的所有订单', str_replace(
            '/order-info/index',
            '/order-info/export',
            urldecode(Yii::$app->request->url)
        ), [
            'class' => 'btn btn-default',
            'target' => '_blank',
        ]);
        echo Html::a('导出ERP对接数据', str_replace(
            '/order-info/index',
            '/order-info/export-for-erp',
            urldecode(Yii::$app->request->url)
        ), [
            'class' => 'btn btn-default',
            'target' => '_blank',
        ]);
        echo Html::a('导出分成', str_replace(
            '/order-info/index',
            '/order-info/export-divide',
            urldecode(Yii::$app->request->url)
        ), [
            'class' => 'btn btn-default',
            'target' => '_blank',
        ]);
        ?>
    </div>

    <?php
    $columns = [
        'order_id',
        'group_id',
        'order_sn',
        'offline',
        [
            'class'=>'kartik\grid\ExpandRowColumn',
            'width'=>'50px',
            'value'=>function ($model, $key, $index, $column) {
                return GridView::ROW_COLLAPSED;
            },
            'detail'=>function ($model, $key, $index, $column) use ($isGiftStyleMap) {
                return Yii::$app->controller->renderPartial(
                    '_goods-list',
                    [
                        'model' => $model,
                        'isGiftStyleMap' => $isGiftStyleMap,
                    ]
                );
            },
            'headerOptions'=>['class'=>'kartik-sheet-style'],
        ],
        [
            'attribute' => 'user_id',
            'label' => '下单用户',
            'format' => 'raw',
            'value' => function ($model) {
                if (empty($model->users)) {
                    return null;
                }
                return Html::a($model->users->showName. '('. $model->users->mobile_phone. ')', \yii\helpers\Url::to([
                    '/sc-user/view', 'id' => $model->user_id,
                ]), [
                    'target' => '_blank',
                ]);
            }
        ],
        [
            'attribute' => 'order_status',
            'value' => function($model){
                return OrderInfo::$order_status_map[$model->order_status];
            },
        ],
        [
            'attribute' => 'shipping_status',
            'value' => function($model){
                return OrderInfo::$shipping_status_map[$model->shipping_status];
            }
        ],
        [
            'attribute' => 'pay_status',
            'value' => function($model){
                return OrderInfo::$pay_status_map[$model->pay_status];
            }
        ],
        'consignee',
        [
            'label' => '收货地址',
            'value' => function($model){
                return \common\models\Region::getUserAddress($model).' '.$model->address;
            },
        ],
        'mobile',
        [
            'attribute' => 'shipping_name',
            'format' => 'raw',
            'value' => function($model){
                if ($model->shipping_fee > 0) {
                    return '<span style="background: red">'.$model->shipping_name.'</span>';
                } else {
                    return $model->shipping_name;
                }
            }
        ],
        // 'pay_id',
         'pay_name',
        // 'how_oos',
        // 'how_surplus',
        // 'pack_name',
        // 'card_name',
        // 'card_message',
        // 'inv_payee',
        // 'inv_content',
        'goods_amount',
        [
            'attribute' => 'shipping_fee',
            'format' => 'raw',
            'value' => function($model){
                if ($model->shipping_fee > 0) {
                    return '<span style="color: red">'.$model->shipping_fee.'</span>';
                } else {
                    return $model->shipping_fee;
                }
            }
        ],
        // 'insure_fee',
        // 'pay_fee',
        // 'pack_fee',
        // 'card_fee',
//        'money_paid',
        // 'surplus',
        // 'integral',
        // 'integral_money',
        // 'bonus',
//        'order_amount',
        // 'from_ad',
        // 'referer',
        [
            'attribute' => 'add_time',
            'value' => function($model) {
                return DateTimeHelper::getFormatCNDateTime($model->add_time);
            }
        ],
        // 'confirm_time',
//        [
//            'attribute' => 'pay_time',
//            'value' => function($model) {
//                return DateTimeHelper::getFormatCNDateTime($model->pay_time);
//            }
//        ],
//        [
//            'attribute' => 'shipping_time',
//            'value' => function($model) {
//                return DateTimeHelper::getFormatCNDateTime($model->shipping_time);
//            }
//        ],
        // 'recv_time:datetime',
        // 'pack_id',
        // 'card_id',
        // 'bonus_id',
//        'invoice_no',
        [
            'attribute' => 'extension_code',
            'value' => function($model) use ($extensionCodeMap) {
                $extensionCode = $model->extension_code ?: OrderInfo::EXTENSION_CODE_GENERAL;
                return $extensionCodeMap[$extensionCode];
            }
        ],

        // 'extension_id',
        // 'to_buyer',
        // 'pay_note',
        // 'agency_id',
        // 'inv_type',
        // 'tax',
        // 'is_separate',
        // 'parent_id',
        // 'discount',
        // 'mobile_pay',
        // 'mobile_order',
//        [
//            'attribute' => 'brand_id',
//            'value' => function($model) {
//                return $model->brand['brand_name'];
//            }
//        ],
//        [
//            'attribute' => 'supplier_user_id',
//            'value' => function($model) {
//                return $model->supplierUser['company_name'];
//            }
//        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{view}'
        ],
    ];
    echo DynaGrid::widget([
            'columns' => $columns,
            'storage' => DynaGrid::TYPE_COOKIE,
            'theme' => 'panel-primary',
            'gridOptions' => [
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'panel' => [
                    'heading' => '<h3 class="panel-title">订单列表</h3>',
                ],
                'toolbar' =>  [
                    ['content'=>
                        Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['index'], ['data-pjax'=>0, 'class' => 'btn btn-default', 'title'=>'Reset Grid'])
                    ],
                    ['content'=>'{dynagridFilter}{dynagridSort}{dynagrid}'],
                    '{toggleData}',
                ]
            ],
            'options' => [
                'id' => 'dynagrid-order-info',
            ],
        ]);
    ?>
</div>
