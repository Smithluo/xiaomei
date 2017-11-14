<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\OrderGroup */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-group-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'group_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'user_id')->textInput() ?>

    <?= $form->field($model, 'create_time')->textInput() ?>

    <?= $form->field($model, 'group_status')->textInput() ?>

    <?= $form->field($model, 'consignee')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'country')->textInput() ?>

    <?= $form->field($model, 'province')->textInput() ?>

    <?= $form->field($model, 'city')->textInput() ?>

    <?= $form->field($model, 'district')->textInput() ?>

    <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'mobile')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pay_id')->textInput() ?>

    <?= $form->field($model, 'pay_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'goods_amount')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'shipping_fee')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'money_paid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'order_amount')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pay_time')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'shipping_time')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'recv_time')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'discount')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
