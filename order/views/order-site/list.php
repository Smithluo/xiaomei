<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\dynagrid\DynaGrid;
use common\helper\DateTimeHelper;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\OrderGroupSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '总单列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-group-index">

<!--    <h1>--><?php //echo Html::encode($this->title) ?><!--</h1>-->
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

    <!-- //  订单导入 -->

    <?php
    $columns = [
        ['class' => 'yii\grid\SerialColumn'],
        'group_id',
        [
            'attribute' => 'create_time',
            'label' => '创建时间',
            'value' => function($model) {
                return DateTimeHelper::getFormatCNDateTime($model->create_time);
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
            'attribute' => 'group_status',
            'value' => function($model){
                return \common\models\OrderGroup::$order_group_status[$model->group_status];
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
            'label' => '订单金额',
            'value' => function ($model) {
                if (empty($model->orders)) {
                    return '0.00';
                }
                if ($model->orders[0]['extension_code'] == \common\models\OrderInfo::EXTENSION_CODE_INTEGRAL) {
                    return ''. $model->getTotalFee(). '积分';
                }
                return \common\helper\NumberHelper::price_format($model->getTotalFee());
            }
        ],
        'money_paid',
        'order_amount',

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
//        [
//            'attribute' => 'recv_time',
//            'value' => function($model) {
//                return DateTimeHelper::getFormatCNDateTime($model->recv_time);
//            }
//        ],

//        'discount',
    ];

    echo DynaGrid::widget([
        'columns' => $columns,
        'storage' => DynaGrid::TYPE_COOKIE,
        'theme' => 'panel-primary',
        'gridOptions' => [
            'dataProvider' => $dataProvider,
//            'filterModel' => $searchModel,
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
            'id' => 'dynagrid-order-group',
        ],
    ]); ?>
</div>
