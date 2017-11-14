<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\AdPositionSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ad-position-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'position_id') ?>

    <?= $form->field($model, 'position_name') ?>

    <?= $form->field($model, 'ad_width') ?>

    <?= $form->field($model, 'ad_height') ?>

    <?= $form->field($model, 'position_desc') ?>

    <?php // echo $form->field($model, 'position_style') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
