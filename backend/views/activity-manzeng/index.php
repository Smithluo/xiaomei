<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ActivityManzengSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '活动满赠商品列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="activity-manzeng-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('新建', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => 'goods_id',
                'value' => function ($model) {
                    if (empty($model['goods'])) {
                        return null;
                    }
                    return $model['goods']->getBackendName();
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
                            'action' => ['/activity-manzeng/edit-sort'],
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
                            'action' => ['/activity-manzeng/edit-show'],
                        ],
                    ];
                },
                'pageSummary' => true,
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
