<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\BrandApplicationSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="brand-application-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'company_name') ?>

    <?= $form->field($model, 'company_address') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'position') ?>

    <?php // echo $form->field($model, 'contact') ?>

    <?php // echo $form->field($model, 'brands') ?>

    <?php // echo $form->field($model, 'licence') ?>

    <?php // echo $form->field($model, 'recorded') ?>

    <?php // echo $form->field($model, 'registed') ?>

    <?php // echo $form->field($model, 'taxed') ?>

    <?php // echo $form->field($model, 'checked') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
