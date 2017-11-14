<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\PaymentSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="payment-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'pay_id') ?>

    <?= $form->field($model, 'pay_code') ?>

    <?= $form->field($model, 'pay_name') ?>

    <?= $form->field($model, 'pay_fee') ?>

    <?= $form->field($model, 'pay_desc') ?>

    <?php // echo $form->field($model, 'pay_order') ?>

    <?php // echo $form->field($model, 'pay_config') ?>

    <?php // echo $form->field($model, 'enabled') ?>

    <?php // echo $form->field($model, 'is_cod') ?>

    <?php // echo $form->field($model, 'is_online') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
