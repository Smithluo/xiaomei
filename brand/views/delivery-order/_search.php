<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\DeliveryOrderSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="delivery-order-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'delivery_id') ?>

    <?= $form->field($model, 'delivery_sn') ?>

    <?= $form->field($model, 'order_sn') ?>

    <?= $form->field($model, 'order_id') ?>

    <?= $form->field($model, 'invoice_no') ?>

    <?php // echo $form->field($model, 'add_time') ?>

    <?php // echo $form->field($model, 'shipping_id') ?>

    <?php // echo $form->field($model, 'shipping_name') ?>

    <?php // echo $form->field($model, 'user_id') ?>

    <?php // echo $form->field($model, 'action_user') ?>

    <?php // echo $form->field($model, 'consignee') ?>

    <?php // echo $form->field($model, 'address') ?>

    <?php // echo $form->field($model, 'country') ?>

    <?php // echo $form->field($model, 'province') ?>

    <?php // echo $form->field($model, 'city') ?>

    <?php // echo $form->field($model, 'district') ?>

    <?php // echo $form->field($model, 'sign_building') ?>

    <?php // echo $form->field($model, 'email') ?>

    <?php // echo $form->field($model, 'zipcode') ?>

    <?php // echo $form->field($model, 'tel') ?>

    <?php // echo $form->field($model, 'mobile') ?>

    <?php // echo $form->field($model, 'best_time') ?>

    <?php // echo $form->field($model, 'postscript') ?>

    <?php // echo $form->field($model, 'how_oos') ?>

    <?php // echo $form->field($model, 'insure_fee') ?>

    <?php // echo $form->field($model, 'shipping_fee') ?>

    <?php // echo $form->field($model, 'update_time') ?>

    <?php // echo $form->field($model, 'suppliers_id') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'agency_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
