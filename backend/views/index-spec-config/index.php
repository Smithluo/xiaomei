<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\IndexSpecConfigSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '首页活动专区配置';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="index-spec-config-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('新增配置商品', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            [
                'attribute' => 'goods_id',
                'value' => function ($model) {
                    if (!empty($model->goods)) {
                        return $model->goods->goods_name;
                    }
                    return null;
                }
            ],
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'tip',
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'header' => '顶部tip',
                        'size' => 'md',
                        'formOptions' => [
                            'action' => ['/index-spec-config/edit-tip'],
                        ],
                    ];
                },
                'pageSummary' => true,
            ],
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'title',
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'header' => '标题',
                        'size' => 'md',
                        'formOptions' => [
                            'action' => ['/index-spec-config/edit-title'],
                        ],
                    ];
                },
                'pageSummary' => true,
            ],
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'sub_title',
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'header' => '副标题',
                        'size' => 'md',
                        'formOptions' => [
                            'action' => ['/index-spec-config/edit-sub-title'],
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
                            'action' => ['/index-spec-config/edit-sort'],
                        ],
                    ];
                },
                'pageSummary' => true,
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
