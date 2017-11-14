<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\CategorySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="category-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'cat_id') ?>

    <?= $form->field($model, 'cat_name') ?>

    <?= $form->field($model, 'keywords') ?>

    <?= $form->field($model, 'cat_desc') ?>

    <?= $form->field($model, 'parent_id') ?>

    <?php // echo $form->field($model, 'sort_order') ?>

    <?php // echo $form->field($model, 'template_file') ?>

    <?php // echo $form->field($model, 'show_in_nav') ?>

    <?php // echo $form->field($model, 'style') ?>

    <?php // echo $form->field($model, 'is_show') ?>

    <?php // echo $form->field($model, 'grade') ?>

    <?php // echo $form->field($model, 'filter_attr') ?>

    <?php // echo $form->field($model, 'album_id') ?>

    <?php // echo $form->field($model, 'brand_list') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
