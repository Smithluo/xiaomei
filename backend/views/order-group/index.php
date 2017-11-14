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

    <h1><?= Html::encode($this->title) ?></h1>
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php
    $url = str_replace(
        '/order-group/index',
        '/order-group/export',
        urldecode(Yii::$app->request->url)
    );
    echo Html::a('导出', $url, ['class' => 'btn btn-default']);
    $url = str_replace(
        '/order-group/index',
        '/order-group/export-divide',
        urldecode(Yii::$app->request->url)
    );
    echo Html::a('导出分成', $url, ['class' => 'btn btn-default']);

    echo Html::a('导出区域', \yii\helpers\Url::to([
        '/order-group/export-region'
    ]), ['class' => 'btn btn-default']);

    echo Html::a('导出商品名', \yii\helpers\Url::to([
        '/order-group/export-goods'
    ]), ['class' => 'btn btn-default']);
    ?>

    <?php
    if (Yii::$app->user->can('/order-group/import')) {
        $form = ActiveForm::begin([
            'action' => ['import'],
            'method' => 'post',
            'options' => ['enctype' => 'multipart/form-data'
            ]]);
        echo $form->field($importForm, 'file')->fileInput();
        echo '<button>提交</button>';

        ActiveForm::end();
    }
    ?>

    <?php
    $columns = [
        ['class' => 'yii\grid\SerialColumn'],
        'id',
        'group_id',
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'offline',
            'editableOptions' => function($model, $key, $index) {
                return [
                    'header' => '是否线下订单',
                    'size' => 'md',
                    'formOptions' => [
                        'action' => ['/order-group/edit-offline'],
                    ],
                ];
            },
            'pageSummary' => true,
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{view}'
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
            'attribute' => 'pay_name',
            'value' => function ($model) {
                if (empty($model->orderList)) {
                    return null;
                }
                else {
                    return $model->orderList[0]['pay_name'];
                }
            }
        ],
        [
            'label' => '支付日志',
            'value' => function ($model) {
                $firstOrder = $model->getFirstOrder();
                if (empty($firstOrder)) {
                    return '';
                }
                return $firstOrder->getPayNote();
            }
        ],
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
        'money_paid',
        'order_amount',
        [
            'attribute' => 'create_time',
            'value' => function($model) {
                return DateTimeHelper::getFormatCNDateTime($model->create_time);
            }
        ],
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
        'discount',
    ];

    echo DynaGrid::widget([
        'columns' => $columns,
        'storage' => DynaGrid::TYPE_COOKIE,
        'theme' => 'panel-primary',
        'gridOptions' => [
            'dataProvider' => $dataProvider,
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
            'id' => 'dynagrid-order-group',
        ],
    ]); ?>
</div>
