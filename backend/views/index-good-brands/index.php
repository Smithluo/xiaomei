<?php

use yii\helpers\Html;
use kartik\dynagrid\DynaGrid;
/* @var $this yii\web\View */
/* @var $searchModel common\models\IndexGoodBrandsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '优选品牌';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="season-goods-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('创建优选品牌', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php


    $columns =[
        [
            'attribute' => 'id',
        ],
        [
            'attribute' => 'brand_id',
            'value' => function($model) {
                return $model->brand['brand_name'];
            },
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'title',
            'editableOptions' => function($model, $key, $index) {
                return [
                    'header' => '标题',
                    'size' => 'md',
                    'formOptions' => [
                        'action' => ['/index-good-brands/editTitle'],
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
                        'action' => ['/index-good-brands/editSortOrder'],
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
                'heading' => '<h3 class="panel-title">品牌</h3>',
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
            'id' => 'dynagrid-index-good-brands',
        ],
    ]);
    ?>
</div>
