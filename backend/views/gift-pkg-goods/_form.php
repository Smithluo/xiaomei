<?php

use yii\helpers\Html;
use kartik\detail\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\GiftPkgGoods */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="gift-pkg-goods-form">
    <?php
    $attributes = [
        [
            'columns' => [
                [
                    'attribute' => 'gift_pkg_id',
                    'type' => DetailView::INPUT_SELECT2,
                    'widgetOptions' => [
                        'data' => !empty($giftPkgList) ? $giftPkgList : '',
                        'options' => ['placeholder' => '选择礼包活动'],
                        'pluginOptions' => ['allowClear'=>true, 'width'=>'100%'],
                    ],
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 15%',
                    ],
                ],
                [
                    'attribute' => 'goods_id',
                    'value' => !empty($model->goods_id) ? $goodsList[$model->goods_id] : 0,
                    'type' => DetailView::INPUT_SELECT2,
                    'widgetOptions' => [
                        'data' => !empty($goodsList) ? $goodsList : '',
                        'options' => ['placeholder' => '选择礼包商品'],
                        'pluginOptions' => ['allowClear'=>true, 'width'=>'100%'],
                    ],
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 15%',
                    ],
                ],
                [
                    'attribute' => 'goods_num',
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 15%',
                    ],
                ],
            ],
        ],
    ];

    echo DetailView::widget([
        'model' => $model,
        'attributes' => $attributes,
        'mode' => Yii::$app->controller->action->id != 'view' ? DetailView::MODE_EDIT : DetailView::MODE_VIEW,
        'enableEditMode' => true,
        'deleteOptions'=>[ // your ajax delete parameters
            'params' => ['id' => $model->id, 'custom_param' => true],
        ],
        'panel'=>[
            'heading' => '礼包活动',
            'type'=>DetailView::TYPE_PRIMARY,
        ],
        'formOptions' => [
            'action' => $model->isNewRecord
                ? \yii\helpers\Url::to(['create'])
                : \yii\helpers\Url::to(['update', 'id' => $model->id]),
        ],
    ]);
    ?>
</div>
