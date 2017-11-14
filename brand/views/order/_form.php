<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Region;
use brand\models\OrderInfo;

/* @var $this yii\web\View */
/* @var $model common\models\OrderInfo */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-info-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'order_sn')->textInput(['maxlength' => true, 'readonly' => 'readonly']) ?>

<!--    --><?php //echo $form->field($model, 'user_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'order_status')->dropDownList(
        [$model->order_status => OrderInfo::$order_status_map[$model->order_status]], ['disabled' => 'disabled']
    ) ?>

    <?= $form->field($model, 'shipping_status')->dropDownList(
        [$model->shipping_status => OrderInfo::$order_status_map[$model->shipping_status]], ['disabled' => 'disabled']
    ) ?>

    <?= $form->field($model, 'pay_status')->dropDownList(
        [$model->pay_status => OrderInfo::$order_status_map[$model->pay_status]], ['disabled' => 'disabled']
    ) ?>

    <?= $form->field($model, 'consignee')->textInput(['maxlength' => true, 'readonly' => 'readonly']) ?>

    <?= $form->field($model, 'mobile')->textInput(['maxlength' => true, 'readonly' => 'readonly']) ?>

<!--    --><?php //echo $form->field($model, 'country')->textInput() ?>

    <?= $form->field($model, 'province')->dropDownList([$model->province => Region::getRegionName($model->province)], ['disabled' => 'disabled']) ?>

    <?= $form->field($model, 'city')->dropDownList([$model->city => Region::getRegionName($model->city)], ['disabled' => 'disabled']) ?>

    <?= $form->field($model, 'district')->dropDownList([$model->district => Region::getRegionName($model->district)], ['disabled' => 'disabled']) ?>

    <?= $form->field($model, 'address')->textInput(['maxlength' => true, 'readonly' => 'readonly']) ?>

    <?= $form->field($model, 'zipcode')->textInput(['maxlength' => true, 'readonly' => 'readonly']) ?>

    <?php echo $form->field($model, 'tel')->textInput(['maxlength' => true, 'readonly' => 'readonly']) ?>



<!--    --><?php //echo $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'best_time')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'sign_building')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'postscript')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'shipping_id')->textInput() ?>

<!--    --><?php //echo $form->field($model, 'shipping_name')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'pay_id')->textInput() ?>

<!--    --><?php //echo $form->field($model, 'pay_name')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'how_oos')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'how_surplus')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'pack_name')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'card_name')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'card_message')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'inv_payee')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'inv_content')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'goods_amount')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'shipping_fee')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'insure_fee')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'pay_fee')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'pack_fee')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'card_fee')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'money_paid')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'surplus')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'integral')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'integral_money')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'bonus')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'order_amount')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'from_ad')->textInput() ?>

<!--    --><?php //echo $form->field($model, 'referer')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'add_time')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'confirm_time')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'pay_time')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'shipping_time')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'recv_time')->textInput() ?>

<!--    --><?php //echo $form->field($model, 'pack_id')->textInput() ?>

<!--    --><?php //echo $form->field($model, 'card_id')->textInput() ?>

<!--    --><?php //echo $form->field($model, 'bonus_id')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'invoice_no')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'extension_code')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'extension_id')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'to_buyer')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'pay_note')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'agency_id')->textInput() ?>

<!--    --><?php //echo $form->field($model, 'inv_type')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'tax')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'is_separate')->textInput() ?>

<!--    --><?php //echo $form->field($model, 'parent_id')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'discount')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'mobile_pay')->textInput(['maxlength' => true]) ?>

<!--    --><?php //echo $form->field($model, 'mobile_order')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '新增' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
