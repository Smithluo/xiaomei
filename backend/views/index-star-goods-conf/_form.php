<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\IndexStarGoodsConf */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="index-star-goods-conf-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-lg-4">
            <?php
            $goodsMap = [];
            foreach ($allGoods as $goods) {
                $goodsMap[$goods['goods_id']] = $goods['goods_name'];
            }
            echo $form->field($model, 'goods_id')->widget(\kartik\widgets\Select2::classname(), [
                'data' => $goodsMap,
                'options' => ['placeholder' => '选择商品'],
            ]);
            ?>
        </div>
        <div class="col-lg-4">
            <?php
            $tabMap = [];
            foreach ($allTabs as $tab) {
                $tabMap[$tab['id']] = $tab['tab_name'];
            }
            echo $form->field($model, 'tab_id')->widget(\kartik\widgets\Select2::classname(), [
                'data' => $tabMap,
                'options' => ['placeholder' => '选择标签'],
            ]);
            ?>
        </div>
        <div class="col-lg-4">
            <?= $form->field($model, 'sort_order')->textInput() ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
