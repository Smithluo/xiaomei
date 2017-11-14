<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\IndexZhifaYouxuanSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="index-zhifa-youxuan-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-lg-2">
            <?= $form->field($model, 'id') ?>
        </div>
        <div class="col-lg-2">
            <?= $form->field($model, 'image') ?>
        </div>
        <div class="col-lg-2">
            <?= $form->field($model, 'url') ?>
        </div>
        <div class="col-lg-2">
            <?= $form->field($model, 'sort_order') ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
