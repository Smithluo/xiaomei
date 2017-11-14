<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\IndexKeywordsGroupSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Index Keywords Groups';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="index-keywords-group-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Index Keywords Group', ['create'], ['class' => 'btn btn-success']) ?>
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
                        'size' => 'sm',
                        'formOptions' => [
                            'action' => ['/index-keywords-group/edit-value'],
                        ],
                    ];
                },
                'pageSummary' => true,
            ],
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'cat_id',
                'value' => function ($model) {
                    if (empty(\common\helper\CacheHelper::getTopGoodsCategoryMap()[$model['cat_id']])) {
                        return null;
                    }
                    return \common\helper\CacheHelper::getTopGoodsCategoryMap()[$model['cat_id']];
                },
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'header' => '1级分类',
                        'inputType' => \kartik\editable\Editable::INPUT_SELECT2,
                        'options' => [
                            'data' => \common\helper\CacheHelper::getTopGoodsCategoryMap(),
                            'options' => [
                                'placeholder' => '选择分类',
                            ],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ],
                        'size' => 'sm',
                        'formOptions' => [
                            'action' => ['/index-keywords-group/edit-value'],
                        ],
                    ];
                },
                'pageSummary' => true,
            ],
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'scene',
                'value' => function ($model) {
                    return \common\models\IndexKeywordsGroup::$sceneMap[$model['scene']];
                },
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'header' => '场景',
                        'size' => 'sm',
                        'inputType' => \kartik\editable\Editable::INPUT_DROPDOWN_LIST,
                        'formOptions' => [
                            'action' => ['/index-keywords-group/edit-value'],
                        ],
                        'data' => \common\models\IndexKeywordsGroup::$sceneMap,
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
                        'size' => 'sm',
                        'formOptions' => [
                            'action' => ['/index-keywords-group/edit-value'],
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
                        'size' => 'sm',
                        'inputType' => \kartik\editable\Editable::INPUT_SWITCH,
                        'formOptions' => [
                            'action' => ['/index-keywords-group/edit-value'],
                        ],
                    ];
                },
                'pageSummary' => true,
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
