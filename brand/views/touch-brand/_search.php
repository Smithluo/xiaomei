<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\TouchBrandSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="touch-brand-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'brand_id') ?>

    <?= $form->field($model, 'brand_banner') ?>

    <?= $form->field($model, 'brand_content') ?>

    <?= $form->field($model, 'brand_qualification') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
