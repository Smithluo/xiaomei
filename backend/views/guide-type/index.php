<?php

use yii\helpers\Html;
use kartik\dynagrid\DynaGrid;
/* @var $this yii\web\View */
/* @var $searchModel common\models\IndexActivitySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '选品指南分类配置';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="season-goods-index">


    <p>
        <?= Html::a('选品指南分类配置', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php


    $columns =[
        [
            'attribute' => 'id',
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'title',
            'editableOptions' => function($model, $key, $index) {
                return [
                    'header' => '标题',
                    'size' => 'md',
                    'formOptions' => [
                        'action' => ['/guide-type/editTitle'],
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
                        'action' => ['/guide-type/editDesc'],
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
                        'action' => ['/guide-type/editSortOrder'],
                    ],
                ];
            },
            'pageSummary' => true,
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'header' => '操作',
            'template' => '{update} {delete} ',

        ],
    ];

    echo DynaGrid::widget([
        'columns' => $columns,
        'storage' => DynaGrid::TYPE_COOKIE,
        'theme' => 'panel-primary',
        'gridOptions' => [
            'dataProvider' => $dataProvider,
            'panel' => [
                'heading' => '<h3 class="panel-title">选品指南分类配置</h3>',
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
            'id' => 'dynagrid-goods-type',
        ],
    ]);
    ?>
</div>
