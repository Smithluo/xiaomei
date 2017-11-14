<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\dynagrid\DynaGrid;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\IndexFullCutGoodsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '首页显示的满减商品';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="index-full-cut-goods-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('添加首页显示的满减商品', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php
    $columns = [
        'id',
        'goods_id',
        [
            'attribute' => 'goods_id',
            'label' => '商品名称',
            'value' => function ($model) use ($goodsList) {
                return $goodsList[$model->goods_id];
            }
        ],
        'sort_order',

        [
            'class' => 'yii\grid\ActionColumn',
            'header' => '操作',
        ],
    ];

    echo DynaGrid::widget([
        //        'dataProvider' => $dataProvider,
        //        'filterModel' => $searchModel,
        'columns' => $columns,
        'storage' => DynaGrid::TYPE_COOKIE,
        'theme' => 'panel-primary',
        'gridOptions' => [
            'dataProvider' => $dataProvider,
            //            'filterModel' => $searchModel,
            'panel' => [
                'heading' => '<h3 class="panel-title">首页显示的满减商品</h3>',
            ],
        ],
        'options' => [
            'id' => 'dynagrid-full-cut-goods-index',
        ],
    ]);

    ?>
</div>
