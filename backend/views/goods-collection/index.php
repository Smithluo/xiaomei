<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\GoodsCollectionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '选品专辑';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="goods-collection-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('新建专辑', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'title',
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'header' => '标题',
                        'size' => 'md',
                        'formOptions' => [
                            'action' => ['/goods-collection/edit-value'],
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
                            'action' => ['/goods-collection/edit-value'],
                        ],
                    ];
                },
                'pageSummary' => true,
            ],
            'create_time',
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'color',
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'inputType' => \kartik\editable\Editable::INPUT_COLOR,
                        'header' => '主题色',
                        'size' => 'md',
                        'formOptions' => [
                            'action' => ['/goods-collection/edit-value'],
                        ],
                    ];
                },
                'pageSummary' => true,
            ],
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'keywords',
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'header' => '关键词',
                        'size' => 'md',
                        'formOptions' => [
                            'action' => ['/goods-collection/edit-value'],
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
                            'action' => ['/goods-collection/edit-value'],
                        ],
                    ];
                },
                'pageSummary' => true,
            ],
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'is_show',
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'header' => '是否显示',
                        'size' => 'md',
                        'formOptions' => [
                            'action' => ['/goods-collection/edit-value'],
                        ],
                    ];
                },
                'pageSummary' => true,
            ],
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'is_hot',
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'header' => '是否在聚合页显示',
                        'size' => 'md',
                        'formOptions' => [
                            'action' => ['/goods-collection/edit-value'],
                        ],
                    ];
                },
                'pageSummary' => true,
            ],
            'click_init',
            'click',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
            ],
        ],
    ]); ?>
</div>
