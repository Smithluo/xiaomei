<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\CouponRecordSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="coupon-record-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-8\">{input}</div>",
            'labelOptions' => ['class' => 'col-lg-4 control-label text-right'],
        ],
    ]); ?>

    <div class="col-lg-3">
        <?= $form->field($model, 'event_id')->dropDownList($couponEventMap, ['prompt' => '请选择']) ?>

        <?= $form->field($model, 'rule_id')->dropDownList($couponEventRuleMap, ['prompt' => '请选择']) ?>
    </div>

    <div class="col-lg-3">
        <?= $form->field($model, 'user_id')->widget(kartik\select2\Select2::className(), [
            'data' => $usersMap,
            'options' => ['placeholder' => '请选择'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]) ?>

        <?= $form->field($model, 'status')->dropDownList($couponStatusMap, ['prompt' => '请选择']) ?>
    </div>

    <div class="col-lg-3">
        <?= $form->field($model, 'coupon_id') ?>

        <?= $form->field($model, 'coupon_sn') ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton('筛选', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
