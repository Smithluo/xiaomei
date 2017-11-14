<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model backend\models\CashRecordSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cash-record-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-lg-2">
            <?= $form->field($model, 'id') ?>
        </div>
        <div class="col-lg-2">
            <?php
            $userName = '用户未知';
            if (isset($model->user)) {
                $userName = $model->user->showName. '('. $model->user->mobile_phone. ')';
            }
            $url = \yii\helpers\Url::to(['/sc-user/user-list']);
            echo $form->field($model, 'user_id')->widget(kartik\widgets\Select2::className(), [
                'initValueText' => $userName,
                'options' => [
                    'placeholder' => '输入用户名或手机号查询',
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'minimumInputLength' => 3,
                    'language' => [
                        'errorLoading' => new JsExpression("function () { return '正在查询...'; }"),
                    ],
                    'ajax' => [
                        'url' => $url,
                        'dataType' => 'json',
                        'data' => new JsExpression('function(params) { return {q:params.term}; }')
                    ],
                    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                    'templateResult' => new JsExpression('function(order) { return order.text; }'),
                    'templateSelection' => new JsExpression('function (order) { return order.text; }'),
                ],
            ]) ?>
        </div>
        <div class="col-lg-2">
            <?php  echo $form->field($model, 'created_time') ?>
        </div>
        <div class="col-lg-2">
            <?php  echo $form->field($model, 'note') ?>
        </div>
        <div class="col-lg-2">
            <?php  echo $form->field($model, 'type')->dropDownList([
                0 => '出账',
                1 => '入账',
                2 => '全部',
            ]) ?>
        </div>
    </div>

    <?php // echo $form->field($model, 'type') ?>

    <?php // echo $form->field($model, 'balance') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
