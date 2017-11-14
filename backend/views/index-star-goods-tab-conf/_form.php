<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\IndexStarGoodsTabConf */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="index-star-goods-tab-conf-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-lg-3">
            <?= $form->field($model, 'tab_name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'sort_order')->textInput() ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'm_url')->textInput(['maxLength' => true]) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'pc_url')->textInput(['maxLength' => true]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-3">
            <?= $form->field($model, 'image')->fileInput(['accept' => 'image/*']) ?>
        </div>

        <div class="col-lg-3">
            <!-- Original image -->
            <?= Html::img($model->getUploadUrl('image'), ['class' => 'img-thumbnail']) ?>
        </div>

    </div>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
