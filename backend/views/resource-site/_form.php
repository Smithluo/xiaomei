<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\ResourceSite */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="resource-site-form">

    <?php $form = ActiveForm::begin([
        'options' => ['enctype'=>'multipart/form-data'],
        'fieldConfig' => [
            'template' => "<div class='row'>
                {label}\n
                <div class=\"col-lg-8\">{input}</div>\n
                <div class=\"col-lg-4\"></div>
                <div class=\"col-lg-8\">{error}</div>
            </div>",
            'labelOptions' => ['class' => 'col-lg-2 control-label text-right'],
        ],
    ]); ?>

    <div class="row">
        <div class="col-lg-6">
            <?= $form->field($model, 'site_name')->textInput(['maxlength' => 40]) ?>
            <?= $form->field($model, 'site_logo')->fileInput(['accept' => 'image/*']) ?>
            <?= Html::img($model->getUploadUrl('site_logo'), ['height' => '200']) ?>
        </div>
        <div class="col-lg-6">

        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
