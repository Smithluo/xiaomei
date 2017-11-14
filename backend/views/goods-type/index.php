<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\GoodsTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Goods Types';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="goods-type-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Goods Type', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'resizableColumns' => true,
        'persistResize' => true,
        'columns' => [
            'cat_id',
            'cat_name',
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'enabled',
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'header' => '是否可用',
                        'size' => 'md',
                        'formOptions' => [
                            'action' => ['/goods-type/edit-enabled'],
                        ],
//                        'inputType' => \kartik\editable\Editable::INPUT_SPIN,
                    ];
                },
                'pageSummary' => true,
            ],
            'attr_group',
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{view} | {update} | {list} | {delete}',
                'buttons' => [
                    'list' => function ($url, $model, $key) {
                        $jump = '/attribute/index?AttributeSearch%5Bcat_id%5D='. $model->cat_id;
                        return
                            Html::a(
                                '<span class="glyphicon glyphicon-question-sign"></span>',
                                $jump,
                                [
                                    'title' => '列表',
                                ]
                            );
                    },
                ],
            ],
        ],
    ]); ?>
</div>
