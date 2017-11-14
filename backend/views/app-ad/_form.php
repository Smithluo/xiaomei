<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\AppAd */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="app-ad-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'position_id')->widget(\kartik\select2\Select2::className(), [
            'data' => \common\models\AppAdPosition::getAllAdPositionMap(),
    ]) ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'desc')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'start_time')->widget(\kartik\widgets\DateTimePicker::className()) ?>

    <?= $form->field($model, 'end_time')->widget(\kartik\widgets\DateTimePicker::className()) ?>

    <?= $form->field($model, 'enable')->widget(\kartik\widgets\SwitchInput::className()) ?>

    <?= $form->field($model, 'route')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'params')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'sort_order')->textInput() ?>

    <?= $form->field($model, 'image')->fileInput() ?>

    <?= Html::img($model->getUploadUrl('image')) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
