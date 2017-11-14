<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\dynagrid\DynaGrid;
use common\helper\DateTimeHelper;
use backend\models\DeliveryOrder;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\DeliveryOrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '发货单列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="delivery-order-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php
    $columns = [
        ['class' => 'yii\grid\SerialColumn'],

        'delivery_id',
        'delivery_sn',
        'group_id',
        'order_sn',
        'order_id',
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'invoice_no',
            'editableOptions' => function($model, $key, $index) {
                return [
                    'header' => '物流单号',
                    'size' => 'md',
                    'formOptions' => [
                        'action' => ['/delivery-order/editInvoiceNo'],
                    ],
                ];
            },
            'pageSummary' => true,
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'shipping_fee',
            'editableOptions' => function($model, $key, $index) {
                return [
                    'header' => '运费',
                    'size' => 'md',
                    'formOptions' => [
                        'action' => ['/delivery-order/editShippingFee'],
                    ],
                ];
            },
            'pageSummary' => true,
        ],
        [
            'attribute' => 'add_time',
            'value' => function ($model, $key, $index, $column) {
                return DateTimeHelper::getFormatCNDateTime($model->add_time);
            },
        ],
        // 'shipping_id',
        'shipping_name',
        'user_id',
        'action_user',
        'consignee',
        // 'address',
        // 'country',
        // 'province',
        // 'city',
        // 'district',
        // 'sign_building',
        // 'email:email',
        // 'zipcode',
        // 'tel',
        // 'mobile',
        // 'best_time',
        // 'postscript',
        // 'how_oos',
        // 'insure_fee',
        // 'shipping_fee',
        // 'update_time',
        // 'suppliers_id',
        // 'status',
        // 'agency_id',
    ];
    echo DynaGrid::widget([
        'columns' => $columns,
        'storage' => DynaGrid::TYPE_COOKIE,
        'theme' => 'panel-primary',
        'gridOptions' => [
            'dataProvider' => $dataProvider,
            //'filterModel' => $searchModel,
            'panel' => [
                'heading' => '<h3 class="panel-title">发货单列表</h3>',
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
            'id' => 'dynagrid-1',
        ],
    ]); ?>
</div>
