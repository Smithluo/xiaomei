<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ArticleCatSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="article-cat-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-lg-3">
        <?= $form->field($model, 'cat_id') ?>
        </div>
        <div class="col-lg-3">
        <?= $form->field($model, 'cat_name') ?>
        </div>
        <div class="col-lg-3">
        <?= $form->field($model, 'cat_type') ?>
        </div>
        <div class="col-lg-3">
        <?= $form->field($model, 'keywords') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3">
        <?= $form->field($model, 'cat_desc') ?>
        </div>
        <div class="col-lg-3">
            <?php  echo $form->field($model, 'sort_order') ?>
        </div>
        <div class="col-lg-3">
            <?php  echo $form->field($model, 'show_in_nav')->dropDownList([
                0 => '否',
                1 => '是',
            ], ['prompt'=>'选择...']) ?>
        </div>
        <div class="col-lg-3">
            <?php
            $data = [];
            $cats = \common\models\ArticleCat::find()->where(['not', ['cat_id' => $model->cat_id]])->all();
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
    </div>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
