<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\AdSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ad-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'ad_id') ?>

    <?= $form->field($model, 'position_id') ?>

    <?= $form->field($model, 'media_type') ?>

    <?= $form->field($model, 'ad_name') ?>

    <?= $form->field($model, 'ad_link') ?>

    <?php // echo $form->field($model, 'ad_code') ?>

    <?php // echo $form->field($model, 'start_time') ?>

    <?php // echo $form->field($model, 'end_time') ?>

    <?php // echo $form->field($model, 'link_man') ?>

    <?php // echo $form->field($model, 'link_email') ?>

    <?php // echo $form->field($model, 'link_phone') ?>

    <?php // echo $form->field($model, 'click_count') ?>

    <?php // echo $form->field($model, 'enabled') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
