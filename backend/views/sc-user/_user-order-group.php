<?php

use yii\helpers\Html;
use common\helper\DateTimeHelper;
use kartik\grid\GridView;
use kartik\dynagrid\DynaGrid;
use common\models\OrderGroup;

/* @var $this yii\web\View */
/* @var $model common\models\Users */
/* @var $searchModel backend\models\OrderGroupSearch */
/* @var $provider yii\data\ActiveDataProvider */

//表格
$gridColumns = [
    ['class' => 'yii\grid\SerialColumn'],
    'id',
    [
        'format' => 'raw',
        'attribute' => 'group_id',
        'value' => function ($model) {
            return Html::a($model->group_id, \yii\helpers\Url::to([
                '/order-group/view',
                'id' => $model->id,
            ]), [
                'target' => '_blank',
            ]);
        }
    ],
    [
        'class'=>'kartik\grid\ExpandRowColumn',
        'width'=>'50px',
        'value'=>function ($model, $key, $index, $column) {
            return GridView::ROW_COLLAPSED;
        },
        'detail'=>function ($model, $key, $index, $column) {
            return Yii::$app->controller->renderPartial('_goods-list', ['model'=>$model]);
        },
        'headerOptions'=>['class'=>'kartik-sheet-style'],
    ],
    [
        'attribute' => 'user_id',
        'label' => '下单用户',
        'value' => function ($model) {
            if (empty($model->users)) {
                return null;
            }
            return $model->users->showName. '('. $model->users->mobile_phone. ')';
        },
        'filter' => false,
    ],
    [
        'attribute' => 'group_status',
        'value' => function($model){
            return OrderGroup::$order_group_status[$model->group_status];
        },
        'filterType' => GridView::FILTER_SELECT2,
        'filterWidgetOptions' => [
            'data' => OrderGroup::$order_group_status,
            'options' => ['placeholder' => '选择总单状态'],
            'pluginOptions' => ['allowClear'=>true, 'width'=>'100%'],
        ]
    ],
    'consignee',
    [
        'label' => '收货地址',
        'value' => function($model){
            return \common\models\Region::getUserAddress($model).' '.$model->address;
        },
    ],
    'mobile',
    //'pay_name',
    [
        'attribute' => 'pay_name',
        'value' => function($model){
            return $model->pay_name;
        },
        'filterType' => GridView::FILTER_SELECT2,
        'filterWidgetOptions' => [
            'data' => \common\models\OrderGroup::getAllPayName(),
            'options' => ['placeholder' => '选择支付方式'],
            'pluginOptions' => ['allowClear' => true, 'width' => '100%'],
        ]
    ],
    [
        'attribute' => 'goods_amount',
        'value' => function ($model) {
            if (empty($model->orders)) {
                return '0.00';
            }
            if ($model->orders[0]['extension_code'] == \common\models\OrderInfo::EXTENSION_CODE_INTEGRAL) {
                return ''. $model->goods_amount. '积分';
            }
            return \common\helper\NumberHelper::price_format($model->goods_amount);
        },
        'filter' => false,
    ],
    [
        'attribute' => 'shipping_fee',
        'format' => 'raw',
        'value' => function($model){
            if ($model->shipping_fee > 0) {
                return '<span style="color: red">'.$model->shipping_fee.'</span>';
            } else {
                return $model->shipping_fee;
            }
        },
        'filter' => false,
    ],
    [
        'attribute' => 'money_paid',
        'filter' => false,
    ],
    [
        'attribute' => 'order_amount',
        'filter' => false,
    ],
    [
        'attribute' => 'create_time',
        'value' => function($model) {
            return DateTimeHelper::getFormatCNDateTime($model->create_time);
        },
        'filter' => false,
    ],
    [
        'attribute' => 'discount',
        'filter' => false,
    ],
];

echo DynaGrid::widget([
    'columns' => $gridColumns,
    'storage' => DynaGrid::TYPE_COOKIE,
    'theme' => 'panel-primary',
    'gridOptions' => [
        'dataProvider' => $provider,
        'filterModel' => $searchModel,
        'panel' => [
            'heading' => '<h3 class="panel-title">'. $this->title. '</h3>',
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
        'id' => 'dynagrid-user-order-group',
    ],
]);

?>
