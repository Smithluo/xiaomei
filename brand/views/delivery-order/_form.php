<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\helper\DateTimeHelper;
use common\models\Region;

/* @var $this yii\web\View */
/* @var $model common\models\DeliveryOrder */
/* @var $form yii\widgets\ActiveForm */

$region_names = Region::getUserAddress($model);
?>

<div class="delivery-order-form">

    <?php $form = ActiveForm::begin([
        'options' => ['class' => 'form-horizontal'],
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-1 control-label'],
        ],
    ]); ?>

    <?= $form->field($model, 'shipping_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'invoice_no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'consignee')->textInput(['maxlength' => true, 'readonly' => 'readonly']) ?>
    <?= $form->field($model, 'mobile')->textInput(['maxlength' => true, 'readonly' => 'readonly']) ?>

    <div class="form-group field-deliveryorder-area">
        <label class="col-lg-1 control-label" for="deliveryorder-area">行政区域</label>
        <div class="col-lg-3"><input type="text" id="deliveryorder-area" class="form-control" name="DeliveryOrder[area]" value="<?=$region_names?>" readonly="readonly" maxlength="60"></div>
        <div class="col-lg-8"><div class="help-block"></div></div>
    </div>
    <?= $form->field($model, 'address')->textInput(['maxlength' => true, 'readonly' => 'readonly']) ?>

    <?= $form->field($model, 'zipcode')->textInput(['maxlength' => true]) ?>


<!--    --><?php //echo $form->field($model, 'sign_building')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'tel')->textInput(['maxlength' => true]) ?>


    <?= $form->field($model, 'delivery_sn')->textInput(['maxlength' => true, 'readonly' => 'readonly']) ?>

    <?= $form->field($model, 'order_sn')->textInput(['maxlength' => true, 'readonly' => 'readonly']) ?>

    <!--    --><?php //echo $form->field($model, 'order_id')->textInput(['maxlength' => true]) ?>

    <!--    --><?php //echo $form->field($model, 'shipping_id')->textInput() ?>

    <div class="form-group field-deliveryorder-time">
        <label class="col-lg-1 control-label" for="deliveryorder-time">分单时间</label>
        <div class="col-lg-3"><input type="text" id="deliveryorder-time" class="form-control" name="DeliveryOrder[time]" value="<?=DateTimeHelper::getFormatDateTime($model->add_time)?>" readonly="readonly" maxlength="60"></div>
        <div class="col-lg-8"><div class="help-block"></div></div>
    </div>

<!--    --><?php //echo $form->field($model, 'best_time')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'postscript')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'how_oos')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'insure_fee')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'shipping_fee')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'update_time')->hiddenInput(['maxlength' => true, 'value' => time()])->label('') ?>

<!--    --><?php //echo $form->field($model, 'suppliers_id')->hiddenInput() ?>

    <?= $form->field($model, 'status')->hiddenInput()->label('') ?>

<!--    --><?php //echo $form->field($model, 'agency_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('发货', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
