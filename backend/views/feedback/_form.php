<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Feedback */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="feedback-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'parent_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'user_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'user_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'user_email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'msg_title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'msg_type')->textInput() ?>

    <?= $form->field($model, 'msg_status')->textInput() ?>

    <?= $form->field($model, 'msg_content')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'msg_time')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'message_img')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'order_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'msg_area')->textInput() ?>

    <?= $form->field($model, 'user_phone')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
