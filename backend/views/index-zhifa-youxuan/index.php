<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\IndexZhifaYouxuanSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '小美直发优选列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="index-zhifa-youxuan-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('新赠配置', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            [
                'attribute' => 'image',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::img($model->getUploadUrl('image'));
                }
            ],
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'url',
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'header' => '跳转链接',
                        'size' => 'md',
                        'formOptions' => [
                            'action' => ['/index-zhifa-youxuan/edit-url'],
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
                            'action' => ['/index-zhifa-youxuan/edit-sort'],
                        ],
                    ];
                },
                'pageSummary' => true,
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
