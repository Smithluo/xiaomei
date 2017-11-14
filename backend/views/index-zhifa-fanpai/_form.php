<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\IndexZhifaFanpai */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="index-zhifa-fanpai-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-lg-2">
            <?php
            $goodsList = \backend\models\Goods::find()->where(['is_on_sale' => 1, 'is_delete' => 0])->asArray()->all();
            $data = array_column($goodsList, 'goods_name', 'goods_id');
            echo $form->field($model, 'goods_id')->widget(\kartik\widgets\Select2::classname(), [
                'data' => $data,
                'options' => ['placeholder' => '选择商品'],
            ]);
            ?>
        </div>
        <div class="col-lg-2">
            <?= $form->field($model, 'sort_order')->textInput() ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
