<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\IndexActivityGroup */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="index-activity-group-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'type')->widget(\kartik\select2\Select2::className(), [
        'data' => \common\models\IndexActivityGroup::$typeMap,
    ]) ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'desc')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sort_order')->textInput() ?>

    <?= $form->field($model, 'is_show')->widget(\kartik\widgets\SwitchInput::className()) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
