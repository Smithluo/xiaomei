<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\GoodsCollection */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="goods-collection-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-lg-2">
            <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-2">
            <?= $form->field($model, 'desc')->textarea(['rows' => 6]) ?>
        </div>
        <div class="col-lg-2">
            <?= $form->field($model, 'click_init')->textInput() ?>
        </div>
        <div class="col-lg-2">
            <?= $form->field($model, 'color')->textInput(['maxlength' => true])->widget(kartik\widgets\ColorInput::className()) ?>
        </div>
        <div class="col-lg-2">
            <?= $form->field($model, 'sort_order')->textInput() ?>
        </div>
        <div class="col-lg-2">
            <?= $form->field($model, 'is_show')->dropDownList([
                '1' => '是',
                '0' => '否',
            ]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-2">
            <?= $form->field($model, 'keywords')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-2">
            <?= $form->field($model, 'is_hot')->dropDownList([
                '1' => '是',
                '0' => '否',
            ]) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
