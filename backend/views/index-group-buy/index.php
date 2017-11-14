<?php

use yii\helpers\Html;
use kartik\dynagrid\DynaGrid;

/* @var $this yii\web\View */
/* @var $searchModel common\models\IndexGroupBuySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '首页显示的团采活动';
$this->params['breadcrumbs'][] = $this->title;
?>
<div>tips: 在这里配置的是团采活动在微信站或者是pc站的首页显示</div>
<div class="season-goods-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('创建首页团采', ['create'], ['class' => 'btn btn-success']) ?>
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
                        'action' => ['/index-group-buy/editTitle'],
                    ],
                ];
            },
            'pageSummary' => true,
        ],
        [
            'attribute' => 'activity_id',
            'value' => function($model)
            {
                return $model->goodsActivity['act_name'];
            }
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'sort_order',
            'editableOptions' => function($model, $key, $index) {
                return [
                    'header' => '排序值',
                    'size' => 'md',
                    'formOptions' => [
                        'action' => ['/index-group-buy/editSortOrder'],
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
            'id' => 'dynagrid-group-buy',
        ],
    ]);
    ?>
</div>

