<?php

use yii\helpers\Html;
use kartik\dynagrid\DynaGrid;
use common\models\ActivityConfig;
/* @var $this yii\web\View */
/* @var $searchModel common\models\ActivityConfig*/
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '微信端活动配置';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="season-goods-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('创建活动配置', ['create'], ['class' => 'btn btn-success']) ?>
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
                        'action' => ['/activity-config/editTitle'],
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
                        'action' => ['/activity-config/editSortOrder'],
                    ],
                ];
            },
            'pageSummary' => true,
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'is_show',
            'value' => function($model) {
                return ActivityConfig::$is_show_map[$model->is_show];
            },
            'editableOptions' => function($model, $key, $index) {
                return [
                    'header' => '是否显示',
                    'size' => 'md',
                    'inputType' => 'dropDownList',
                    'data' => ActivityConfig::$is_show_map,
                    'formOptions' => [
                        'action' => ['/activity-config/editIsShow'],
                    ],
                ];
            },
            'pageSummary' => true,
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'api',
            'editableOptions' => function($model, $key, $index) {
                return [
                    'header' => '排序值',
                    'size' => 'md',
                    'formOptions' => [
                        'action' => ['/activity-config/editAPI'],
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
                'heading' => '<h3 class="panel-title">小美直发配置</h3>',
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
            'id' => 'dynagrid-activity-config',
        ],
    ]);
    ?>
</div>
