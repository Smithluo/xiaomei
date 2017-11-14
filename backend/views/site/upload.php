<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;

?>

<?php
    $form = ActiveForm::begin([
        'options' => [
            'enctype' => 'multipart/form-data'
        ],
    ]);
?>

<?= $form->field($model, 'file')->fileInput()?>

<?=Html::submitButton('上传', ['class' => 'submit'])?>

<?php
    $form->end();
?>
