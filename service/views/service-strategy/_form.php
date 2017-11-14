<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model service\models\ServiceStrategy */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="service-strategy-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'percent_level_1')->textInput() ?>

    <?= $form->field($model, 'percent_level_2')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
