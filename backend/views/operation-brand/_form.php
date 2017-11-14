<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Brand */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="brand-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'brand_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'brand_depot_area')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'brand_logo')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'brand_logo_two')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'brand_bgcolor')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'brand_policy')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'brand_desc')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'brand_desc_long')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'short_brand_desc')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'site_url')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sort_order')->textInput() ?>

    <?= $form->field($model, 'is_show')->textInput() ?>

    <?= $form->field($model, 'album_id')->textInput() ?>

    <?= $form->field($model, 'brand_tag')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'servicer_strategy_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'supplier_user_id')->textInput() ?>

    <?= $form->field($model, 'shipping_id')->textInput() ?>

    <?= $form->field($model, 'discount')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
