<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\IntegralSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="integral-search" class="col-lg-12">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-6\">{input}</div>",
            'labelOptions' => ['class' => 'col-lg-6 control-label text-right'],
        ],
    ]); ?>

    <div class="col-lg-3">
    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'integral') ?>
    </div>
    <div class="col-lg-3">
    <?= $form->field($model, 'user_id') ?>

    <?= $form->field($model, 'pay_code')->dropDownList($payCodeMap, ['prompt' => '请选择']) ?>
    </div>
    <div class="col-lg-3">
    <?= $form->field($model, 'note') ?>
    <?= $form->field($model, 'out_trade_no') ?>
    </div>

    <div class="col-lg-3">
        <?php  echo $form->field($model, 'status')->dropDownList($statusMap) ?>
        <br />
        <div class="form-group" align="center">
            <?= Html::submitButton('筛选', ['class' => 'btn btn-primary']) ?>
            <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
        </div>
    </div>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php ActiveForm::end(); ?>

</div>

