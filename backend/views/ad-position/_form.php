<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\AdPosition */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ad-position-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'position_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'position_desc')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'position_style')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
