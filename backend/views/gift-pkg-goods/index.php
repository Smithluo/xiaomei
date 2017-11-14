<?php

use yii\helpers\Html;
use kartik\dynagrid\DynaGrid;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\GiftPkgGoodsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '礼包商品';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gift-pkg-goods-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_search', [
        'model'     => $searchModel,
        'giftPkgList' => $giftPkgList,
        'goodsList' => $goodsList,
    ]); ?>
    <p>
        <?= Html::a('给礼包活动添加商品', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php
    $columns = [
            'id',
            [
                'attribute' => 'gift_pkg_id',
                'value' => function ($model) use ($giftPkgList) {
                    return !empty($giftPkgList[$model->gift_pkg_id]) ? $giftPkgList[$model->gift_pkg_id] : '';
                }
            ],
            [
                'attribute' => 'goods_id',
                'value' => function ($model) use ($goodsList) {
                    return !empty($goodsList[$model->goods_id]) ? $goodsList[$model->goods_id] : '';
                }
            ],
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'goods_num',
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'header' => '数量',
                        'size' => 'md',
                        'formOptions' => [
                            'action' => ['/gift-pkg-goods/editGoodsNum'],
                        ],
                    ];
                },
                'pageSummary' => true,
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ];
    ?>

    <?= DynaGrid::widget([
        //        'dataProvider' => $dataProvider,
        //        'filterModel' => $searchModel,
        'columns' => $columns,
        'storage' => DynaGrid::TYPE_COOKIE,
        'theme' => 'panel-primary',
        'gridOptions' => [
            'dataProvider' => $dataProvider,
            //            'filterModel' => $searchModel,
            'panel' => [
                'heading' => '<h3 class="panel-title">礼包商品</h3>',
            ],
        ],
        'options' => [
            'id' => 'dynagrid-gift-pkg-goods-index',
        ],
    ]);?>
</div>
