<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model backend\models\ServicerDivideRecordSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="servicer-divide-record-search">

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
            $orderSn = '订单号未知';
            if (isset($model->orderInfo)) {
                $orderSn = $model->orderInfo->order_sn;
            }
            $url = \yii\helpers\Url::to(['/order-info/order-info-list']);
            echo $form->field($model, 'order_id')->widget(kartik\widgets\Select2::className(), [
                'initValueText' => $orderSn,
                'options' => [
                    'placeholder' => '输入订单号查询',
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
            ])
            ?>
        </div>
        <div class="col-lg-2">
            <?= $form->field($model, 'amount')->label('商品总价大于：') ?>
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
            <?php
            $userName = '用户未知';
            if (isset($model->servicer)) {
                $userName = $model->servicer->showName. '('. $model->servicer->mobile_phone. ')';
            }
            $url = \yii\helpers\Url::to(['/sc-user/user-list']);
            echo $form->field($model, 'servicer_user_id')->widget(kartik\widgets\Select2::className(), [
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
            <?php
            $userName = '用户未知';
            if (isset($model->parentServicer)) {
                $userName = $model->parentServicer->showName. '('. $model->parentServicer->mobile_phone. ')';
            }
            $url = \yii\helpers\Url::to(['/sc-user/user-list']);
            echo $form->field($model, 'parent_servicer_user_id')->widget(kartik\widgets\Select2::className(), [
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
    </div>

    <div class="row">
        <div class="col-lg-2">
            <?php  echo $form->field($model, 'divide_amount')->label('业务员分成金额大于：') ?>
        </div>
        <div class="col-lg-2">
            <?php  echo $form->field($model, 'parent_divide_amount')->label('服务商分成金额大于：') ?>
        </div>
        <div class="col-lg-2">
            <?php  echo $form->field($model, 'money_in_record_id') ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('筛选', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('重置条件', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
