<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ServicerDivideRecord */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="servicer-divide-record-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'order_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'amount')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'spec_strategy_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'user_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'servicer_user_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'parent_servicer_user_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'divide_amount')->textInput() ?>

    <?= $form->field($model, 'parent_divide_amount')->textInput() ?>

    <?= $form->field($model, 'child_record_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'money_in_record_id')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
