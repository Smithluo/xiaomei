<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\GalleryImgSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="gallery-img-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-8\">{input}</div>",
            'labelOptions' => ['class' => 'col-lg-4 control-label text-right'],
        ],
    ]); ?>

    <div class="row">
        <div class="col-lg-3">
            <?= $form->field($model, 'img_id') ?>
        </div>

        <div class="col-lg-3">
            <?= $form->field($model, 'gallery_id')->widget(\kartik\widgets\Select2::classname(), [
                'data' => $galleryMap,
                'options' => ['placeholder' => '请选择'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>

        <div class="col-lg-3">
            <?= $form->field($model, 'img_desc') ?>
        </div>

        <div class="col-lg-3">
            <div class="form-group">
                <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
                <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
