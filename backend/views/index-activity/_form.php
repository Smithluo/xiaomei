<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\IndexActivity */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="index-zhifa-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
    <div class="row">

        <div class="col-lg-2">
            <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-2">
            <?= $form->field($model, 'sub_title')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-lg-2">
            <?= $form->field($model, 'm_url')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-lg-2">
            <?= $form->field($model, 'sort_order')->textInput() ?>
        </div>

        <div class="col-lg-4">
            <?= $form->field($model, 'index_logo')->fileInput() ?>
        </div>
        <img src="<?=$model->getUploadUrl('index_logo') ?>" alt="">

        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
