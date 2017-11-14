<?php

use yii\helpers\Html;
use kartik\dynagrid\DynaGrid;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\FashionGoodsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '潮流爆款';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fashion-goods-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('创建潮流爆款', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php
    $columns =[
        [
            'attribute' => 'id',
        ],
        [
            'attribute' => 'goods_id',
            'value' => function($model)
            {
                return $model->goods['goods_name'];
            }
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'name',
            'editableOptions' => function($model, $key, $index) {
                return [
                    'header' => '名字',
                    'size' => 'md',
                    'formOptions' => [
                        'action' => ['/fashion-goods/editName'],
                    ],
                ];
            },
            'pageSummary' => true,
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'desc',
            'editableOptions' => function($model, $key, $index) {
                return [
                    'header' => '描述',
                    'size' => 'md',
                    'formOptions' => [
                        'action' => ['/fashion-goods/editDesc'],
                    ],
                ];
            },
            'pageSummary' => true,
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'sort_order',
            'editableOptions' => function($model, $key, $index) {
                return [
                    'header' => '排序值',
                    'size' => 'md',
                    'formOptions' => [
                        'action' => ['/fashion-goods/editSortOrder'],
                    ],
                ];
            },
            'pageSummary' => true,
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'header' => '操作',
            'template' => ' {update} {delete} ',

        ],
    ];

    echo DynaGrid::widget([
        'columns' => $columns,
        'storage' => DynaGrid::TYPE_COOKIE,
        'theme' => 'panel-primary',
        'gridOptions' => [
            'dataProvider' => $dataProvider,
            'panel' => [
                'heading' => '<h3 class="panel-title">商品列表</h3>',
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
            'id' => 'dynagrid-fashion-goods',
        ],
    ]);
    ?>
</div>
