<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\GoodsActionSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="goods-action-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-lg-3">
            <?= $form->field($model, 'user_name') ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'goods_id') ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'goods_name') ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'shop_price') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3">
            <?php  echo $form->field($model, 'disable_discount')->dropDownList([
                0 => '0(参与)',
                1 => '1(不参与)'
            ], ['prompt' => '选择是否参与折扣']) ?>
        </div>
        <div class="col-lg-3">
            <?php  echo $form->field($model, 'volume_price') ?>
        </div>
        <div class="col-lg-3">
            <?php  echo $form->field($model, 'time') ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
