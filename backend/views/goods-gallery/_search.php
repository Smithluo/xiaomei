<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\GoodsGallerySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="goods-gallery-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'img_id') ?>

    <?= $form->field($model, 'goods_id') ?>

    <?= $form->field($model, 'img_url') ?>

    <?= $form->field($model, 'img_desc') ?>

    <?= $form->field($model, 'thumb_url') ?>

    <?php // echo $form->field($model, 'img_original') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
