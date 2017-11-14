<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\IndexKeywordsGroup */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="index-keywords-group-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-lg-2">
            <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-2">
            <?php
            echo $form->field($model, 'cat_id')->widget(kartik\select2\Select2::className(), [
                'data' => \common\helper\CacheHelper::getTopGoodsCategoryMap(),
                'options' => [
                    'placeholder' => '选择1级分类',
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                ],
            ]); ?>
        </div>
        <div class="col-lg-2">
            <?= $form->field($model, 'scene')->dropDownList(\common\models\IndexKeywordsGroup::$sceneMap) ?>
        </div>
        <div class="col-lg-2">
            <?= $form->field($model, 'sort_order')->textInput() ?>
        </div>
        <div class="col-lg-2">
            <?= $form->field($model, 'is_show')->widget(kartik\widgets\SwitchInput::className()) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
