<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\FeedbackSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="feedback-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'msg_id') ?>

    <?= $form->field($model, 'parent_id') ?>

    <?= $form->field($model, 'user_id') ?>

    <?= $form->field($model, 'user_name') ?>

    <?= $form->field($model, 'user_email') ?>

    <?php // echo $form->field($model, 'msg_title') ?>

    <?php // echo $form->field($model, 'msg_type') ?>

    <?php // echo $form->field($model, 'msg_status') ?>

    <?php // echo $form->field($model, 'msg_content') ?>

    <?php // echo $form->field($model, 'msg_time') ?>

    <?php // echo $form->field($model, 'message_img') ?>

    <?php // echo $form->field($model, 'order_id') ?>

    <?php // echo $form->field($model, 'msg_area') ?>

    <?php // echo $form->field($model, 'user_phone') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
