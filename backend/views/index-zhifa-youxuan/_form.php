<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\IndexZhifaYouxuan */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="index-zhifa-youxuan-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-lg-2">
            <div class="col-lg-2">
                <?= $form->field($model, 'image')->fileInput(['accept' => 'image/*']) ?>
            </div>

            <div class="col-lg-4">
                <?= Html::img($model->getUploadUrl('image'), ['class' => 'img-thumbnail']) ?>
            </div>
        </div>
        <div class="col-lg-2">
            <?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?>
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
