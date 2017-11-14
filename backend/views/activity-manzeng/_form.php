<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ActivityManzeng */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="activity-manzeng-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-lg-3">
            <?php
            $data = \backend\models\Goods::getGoodsMap();
            echo $form->field($model, 'goods_id')->widget(\kartik\widgets\Select2::classname(), [
                'data' => $data,
                'options' => ['placeholder' => '选择商品'],
            ]);
            ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'sort_order')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'is_show')->dropDownList([
                1 => '显示',
                0 => '不显示',
            ]) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
