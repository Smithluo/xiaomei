<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\PaidCoupon */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="paid-coupon-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'amount')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'event_id')->widget(\kartik\widgets\Select2::className(), [
        'data' => \common\helper\EventHelper::getValidCouponEventMap(),
        'options' => [
            'placeholder' => '选择活动',
        ],
        'pluginOptions' => [
            'allowClear' => true,
        ]
    ]) ?>

    <?= $form->field($model, 'rule_id')->widget(\kartik\widgets\Select2::className(), [
        'data' => \common\helper\EventHelper::getValidCouponRuleMap(),
        'options' => [
            'placeholder' => '选择规则',
        ],
        'pluginOptions' => [
            'allowClear' => true,
        ]
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
