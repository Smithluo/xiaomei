<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Users;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model common\models\Users */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="users-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-lg-3">

            <?php
            $url = \yii\helpers\Url::to(['/sc-user/user-list']);
            echo $form->field($model, 'user_id')->widget(kartik\widgets\Select2::className(), [
                'initValueText' => '',
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
            ]);
            ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($bankModel, 'user_name')->textInput(['maxLength' => true]) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($bankModel, 'id_card_no')->textInput(['maxLength' => true]) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($bankModel, 'bank_name')->textInput(['maxLength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3">
            <?= $form->field($bankModel, 'bank_card_no')->textInput(['maxLength' => true]) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($bankModel, 'bank_address')->textInput(['maxLength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3">
            <?= $form->field($model, 'user_rank')->hiddenInput(['value' => Users::USER_RANK_MEMBER])->label('') ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'user_type')->hiddenInput(['value' => Users::USER_TYPE_SERVICER])->label('') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3">
        <?= $form->field($model, 'is_checked')->hiddenInput(['value' => Users::IS_CHECKED_STATUS_PASSED])->label('') ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('更新', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
