<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Tags;

/* @var $this yii\web\View */
/* @var $model common\models\Tags */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tags-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'type')->dropDownList([
        Tags::TAG_TYPE_GOODS => Tags::tagTypeName(Tags::TAG_TYPE_GOODS),
        Tags::TAG_TYPE_ACTIVITY => Tags::tagTypeName(Tags::TAG_TYPE_ACTIVITY),
        Tags::TAG_TYPE_PROPERTY => Tags::tagTypeName(Tags::TAG_TYPE_PROPERTY),
    ]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'desc')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sort')->textInput() ?>

    <?= $form->field($model, 'enabled')->dropDownList([
        0 => '不显示',
        1 => '显示',
    ]) ?>

    <?= $form->field($model, 'code')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'mCode')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
