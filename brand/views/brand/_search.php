<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\BrandSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="brand-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'brand_id') ?>

    <?= $form->field($model, 'brand_name') ?>

    <?= $form->field($model, 'brand_depot_area') ?>

    <?= $form->field($model, 'brand_logo') ?>

    <?= $form->field($model, 'brand_logo_two') ?>

    <?php // echo $form->field($model, 'brand_bgcolor') ?>

    <?php // echo $form->field($model, 'brand_policy') ?>

    <?php // echo $form->field($model, 'brand_desc') ?>

    <?php // echo $form->field($model, 'brand_desc_long') ?>

    <?php // echo $form->field($model, 'short_brand_desc') ?>

    <?php // echo $form->field($model, 'site_url') ?>

    <?php // echo $form->field($model, 'sort_order') ?>

    <?php // echo $form->field($model, 'is_show') ?>

    <?php // echo $form->field($model, 'album_id') ?>

    <?php // echo $form->field($model, 'brand_tag') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
