<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model home\models\BrandApplication */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="brand-application-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'company_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'company_address')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'position')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'contact')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'brands')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'licence')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'recorded')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'registed')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'taxed')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'checked')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
