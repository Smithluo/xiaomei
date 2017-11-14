<?php

use yii\helpers\Html;
use kartik\dynagrid\DynaGrid;
/* @var $this yii\web\View */
/* @var $searchModel common\models\IndexActivitySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '首页分类';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="season-goods-index">


    <p>
        <?= Html::a('新增首页分类', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php
    $columns =[
        [
            'attribute' => 'id',
        ],
        [
            'attribute' => 'title',
        ],
        [
            'attribute' => 'm_url',
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'sort_order',
            'editableOptions' => function($model, $key, $index) {
                return [
                    'header' => '排序值',
                    'size' => 'md',
                    'formOptions' => [
                        'action' => ['/index-category/editSort'],
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

    echo \kartik\dynagrid\DynaGrid::widget([
        'columns' => $columns,
        'storage' => DynaGrid::TYPE_COOKIE,
        'theme' => 'panel-primary',
        'gridOptions' => [
            'dataProvider' => $dataProvider,
            'panel' => [
                'heading' => '<h3 class="panel-title">首页分类</h3>',
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
            'id' => 'dynagrid-index-category',
        ],
    ]);
    ?>
</div>
