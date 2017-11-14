<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\BrandPolicy */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="brand-policy-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-lg-2">
            <?= $form->field($model, 'brand_id')->widget(kartik\widgets\Select2::className(), [
                'data' => $brands,
            ]) ?>
        </div>

        <div class="col-lg-6">
        <?= $form->field($model, 'policy_content')->textarea(['maxlength' => true]) ?>
        </div>

        <div class="col-lg-4">
            <?= $form->field($model, 'policy_link')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-2">
            <?= $form->field($model, 'sort_order')->textInput() ?>
        </div>
        <div class="col-lg-2">
            <?= $form->field($model, 'status')->dropDownList(\common\models\BrandPolicy::$statusMap) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
