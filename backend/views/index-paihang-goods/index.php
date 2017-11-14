<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\IndexPaihangGoodsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '热销排行商品';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="index-paihang-goods-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('新建排行商品', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
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
                            'action' => ['/index-paihang-goods/editTitle'],
                        ],
                    ];
                },
                'pageSummary' => true,
            ],
            [
                'attribute' => 'goods_id',
                'value' => function ($model) {
                    if (empty($model->goods)) {
                        return null;
                    }
                    return $model->goods->goods_name;
                }
            ],
            [
                'attribute' => 'floor_id',
                'value' => function ($model) {
                    if (empty($model->floor)) {
                        return null;
                    }
                    return $model->floor->title;
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
                            'action' => ['/index-paihang-goods/editSortOrder'],
                        ],
                    ];
                },
                'pageSummary' => true,
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
