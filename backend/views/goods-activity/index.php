<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\helper\DateTimeHelper;
use backend\models\GoodsActivity;
use kartik\dynagrid\DynaGrid;
/* @var $this yii\web\View */
/* @var $searchModel common\models\GoodsActivitySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '团采/秒杀';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="goods-activity-index">
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(
            '新建团采',
            ['create', 'act_type' => GoodsActivity::ACT_TYPE_GROUP_BUY],
            ['class' => 'btn btn-success']
        ) ?>
        <?= Html::a(
            '新建秒杀',
            ['create', 'act_type' => GoodsActivity::ACT_TYPE_FLASH_SALE],
            ['class' => 'btn btn-warning']
        ) ?>
        <span style="color: red;">
            <strong>tips: 团采/秒杀 商品如果要设置发货地为深圳（即公司仓库发货），把  团采/秒杀商品的所属品牌改为小美</strong>
        </span>
    </p>
    <?php
    $columns =[
        'act_id',
        'act_name',
        [
            'attribute' => 'act_type',
            'value' => function($model) {
                return \common\models\GoodsActivity::$act_type_map[$model->act_type];
            }
        ],
        'goods_id',
        'start_num',
        [
            'attribute' => 'buy_by_box',
            'format' => 'raw',
            'value' => function($model) {
                $buyByBoxMap = \backend\models\Goods::$buyByBoxMap;
                return $buyByBoxMap[$model->buy_by_box];
            }
        ],
        'number_per_box',
        'limit_num',
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'sort_order',
            'editableOptions' => function($model, $key, $index) {
                return [
                    'header' => '排序',
                    'size' => 'md',
                    'formOptions' => [
                        'action' => ['/goods-activity/edit-value'],
                    ],
                ];
            },
            'pageSummary' => true,
        ],
        'match_num',
        'note',
        'old_price',
        'act_price',
        'production_date',
        'goods_name',
        [
            'attribute' => 'start_time',
            'value' => function($model){
                return DateTimeHelper::getFormatCNDateTime($model->start_time);
            }
        ],
        [
            'attribute' => 'end_time',
            'value' => function($model){
                return DateTimeHelper::getFormatCNDateTime($model->end_time);
            }
        ],
        'is_finished',
        [
            'attribute' => 'shipping_code',
            'value' => function($model) use ($shippingCodeNameMap){
                if (isset($shippingCodeNameMap[$model->shipping_code])) {
                    return $shippingCodeNameMap[$model->shipping_code];
                } else {
                    return '运费到付';
                }
            }
        ],
        [
            'attribute' => 'order_expired_time',
            'label' => '订单有效期',
            'value' => function($model){
                return DateTimeHelper::getTimeDesc($model->order_expired_time);
            }
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'header' => '操作',
            'template' => '{view} {update} {delete} ',

        ],
    ];

    echo DynaGrid::widget([
        'columns' => $columns,
        'storage' => DynaGrid::TYPE_COOKIE,
        'theme' => 'panel-primary',
        'gridOptions' => [
            'dataProvider' => $dataProvider,
            'panel' => [
                'heading' => '<h3 class="panel-title">活动列表</h3>',
            ],
            'toolbar' =>  [
                ['content'=>
                     Html::a('<i class="glyphicon glyphicon-repeat"></i>',
                         ['index'],
                         ['data-pjax'=>0, 'class' => 'btn btn-default', 'title'=>'Reset Grid'])
                ],
                ['content'=>'{dynagridFilter}{dynagridSort}{dynagrid}'],
                '{toggleData}',
            ]
        ],
        'options' => [
            'id' => 'dynagrid-goods-activity',
        ],
    ]);
    ?>
</div>
