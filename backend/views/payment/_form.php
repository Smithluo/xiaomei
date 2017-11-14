<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Payment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="payment-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'pay_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pay_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pay_fee')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pay_desc')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'pay_order')->textInput() ?>

    <?= $form->field($model, 'pay_config')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'enabled')->textInput() ?>

    <?= $form->field($model, 'is_cod')->textInput() ?>

    <?= $form->field($model, 'is_online')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
