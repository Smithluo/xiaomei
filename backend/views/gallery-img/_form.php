<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\GalleryImg */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="gallery-img-form">

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

        <?= $form->field($model, 'gallery_id')->widget(\kartik\widgets\Select2::classname(), [
            'data' => $galleryMap,
            'options' => ['placeholder' => '请选择'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>

        <?= $form->field($model, 'img_original')->fileInput([['accept' => 'image/*']]) ?>

        <?= $form->field($model, 'img_url')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'img_desc')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'sort_order')->textInput(['value' => 30000]) ?>

        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

    </div>


    <?php ActiveForm::end(); ?>

</div>
