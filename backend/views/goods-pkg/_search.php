<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\GoodsPkgSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="goods-pkg-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'pkg_id') ?>

    <?= $form->field($model, 'pkg_name') ?>

    <?= $form->field($model, 'allow_goods_list') ?>

    <?= $form->field($model, 'deny_goods_list') ?>

    <?= $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
