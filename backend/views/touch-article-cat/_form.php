<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\TouchArticleCat */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="touch-article-cat-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-lg-3">
        <?= $form->field($model, 'cat_name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-3">
        <?= $form->field($model, 'keywords')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-3">
        <?= $form->field($model, 'cat_desc')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-3">
        <?= $form->field($model, 'sort_order')->textInput() ?>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-3">
        <?php
        $data = [];
        $cats = \common\models\TouchArticleCat::find()->where(['not', ['cat_id' => $model->cat_id]])->all();
        foreach ($cats as $cat) {
            $data[$cat->cat_id] = $cat->cat_name;
        }
        echo $form->field($model, 'parent_id')->widget(\kartik\widgets\Select2::classname(), [
            'data' => $data,
            'options' => ['placeholder' => '选择父分类'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
        </div>

        <div class="col-lg-3">
            <?= $form->field($model, 'show_in_news')->dropDownList([
                '0' => '否',
                '1' => '是'
            ]) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
