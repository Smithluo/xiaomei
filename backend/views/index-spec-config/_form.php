<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\IndexSpecConfig */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="index-spec-config-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-lg-3">
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
        <div class="col-lg-3">
            <?= $form->field($model, 'tip')->textInput() ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'title')->textInput() ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'sub_title')->textInput() ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3">
            <?= $form->field($model, 'sort_order')->textInput() ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '新建' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
