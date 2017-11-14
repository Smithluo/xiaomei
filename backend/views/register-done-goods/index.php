<?php

use yii\helpers\Html;
use kartik\dynagrid\DynaGrid;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\SeasonGoodsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '宣导页商品';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="season-goods-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('创建商品', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php
    $columns =[
        [
            'attribute' => 'id',
        ],
        [
            'attribute' => 'goods_id',
            'value' => function($model) {
                return $model->goods['goods_name'];
            }
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'is_show',
            'value' => function($model) {
                return ($model->is_show == 0) ? '不显示' : '显示';
            },
            'editableOptions' => function($model, $key, $index) {
                return [
                    'header' => '是否显示',
                    'size' => 'md',
                    'inputType' => 'dropDownList',
                    'data' => \common\models\SeasonGoods::$is_show_map,
                    'formOptions' => [
                        'action' => ['/register-done-goods/editIsShow'],
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
                        'action' => ['/register-done-goods/editSortOrder'],
                    ],
                ];
            },
            'pageSummary' => true,
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'header' => '操作',
            'template' => '{delete} ',

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
            'id' => 'dynagrid-register-done-goods',
        ],
    ]);
    ?>
</div>
