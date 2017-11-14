<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ActivityConfig */
/* @var $form yii\widgets\ActiveForm */
?>
<p>接口填写 /default/activity/hot.html?type='xxx' xxx 可选值 tuancai miaosha manzeng manjian libao</p>
<div class="activity-config-form">
    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-lg-2">
            <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-2">
            <?= $form->field($model, 'sort_order')->textInput() ?>
        </div>
        <div class="col-lg-2">
            <?= $form->field($model, 'api')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-2">
            <?= $form->field($model, 'is_show')->dropDownList(\common\models\ActivityConfig::$is_show_map) ?>
        </div>
    </div>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
