<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model brand\models\TouchBrand */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="touch-brand-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'brand_id')->textInput() ?>

    <?= $form->field($model, 'brand_banner')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'brand_content')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'brand_qualification')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
