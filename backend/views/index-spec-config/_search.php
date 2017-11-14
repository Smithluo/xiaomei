<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\IndexSpecConfigSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="index-spec-config-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-lg-3">
            <?= $form->field($model, 'id') ?>
        </div>
        <div class="col-lg-3">
            <?php
            $goodsMap = [];
            $allGoods = \backend\models\Goods::findAll(['is_on_sale' => 1, 'is_delete' => 0]);
            foreach ($allGoods as $goods) {
                $goodsMap[$goods['goods_id']] = $goods['goods_name'];
            }
            echo $form->field($model, 'goods_id')->widget(\kartik\widgets\Select2::classname(), [
                'data' => $goodsMap,
                'options' => ['placeholder' => '选择商品'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'sort_order') ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('重置搜索条件', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
