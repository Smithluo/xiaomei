<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\CouponRecord */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="coupon-record-form col-lg-3">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'event_id')->textInput(['readonly' => true]) ?>

    <?= $form->field($model, 'rule_id')->textInput(['readonly' => true]) ?>

    <?= $form->field($model, 'coupon_sn')->textInput(['maxlength' => true, 'readonly' => true]) ?>

    <?= $form->field($model, 'user_id')->widget(kartik\select2\Select2::className(), [
        'data' => $usersMap,
        'options' => ['placeholder' => '请选择'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]) ?>

    <?php // echo $form->field($model, 'received_at')->textInput(['readonly' => true]) ?>

    <?php //  $form->field($model, 'used_at')->textInput() ?>

    <?= $form->field($model, 'group_id')->textInput() ?>

    <?= $form->field($model, 'created_by')->textInput(['readonly' => true]) ?>

    <?= $form->field($model, 'status')->dropDownList($couponStatusMap) ?>

    <?= $form->field($model, 'start_time')->textInput() ?>

    <?= $form->field($model, 'end_time')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '创建' : '编辑', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
