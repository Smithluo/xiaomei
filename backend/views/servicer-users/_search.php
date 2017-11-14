<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\UsersSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="users-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-lg-2">
            <?= $form->field($model, 'user_id') ?>
        </div>
        <div class="col-lg-2">
            <?= $form->field($model, 'user_name') ?>
        </div>
        <div class="col-lg-2">
            <?php  echo $form->field($model, 'mobile_phone') ?>
        </div>
        <div class="col-lg-2">
            <?php  echo $form->field($model, 'office_phone') ?>
        </div>
        <div class="col-lg-2">
            <?php  echo $form->field($model, 'company_name') ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('筛选', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
