<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\ShopConfig;

/* @var $this yii\web\View */
/* @var $model common\models\ShopConfigSearch */
/* @var $form yii\widgets\ActiveForm */
$parent_map = ShopConfig::$parent_map;
$storage_type_map = ShopConfig::getStorageTypeMap();
?>

<div class="shop-config-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-sm-2\">{input}</div>",
            'labelOptions' => ['class' => 'col-lg-1 control-label text-right'],
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'parent_id')->dropDownList($parent_map, ['prompt' => '请选择']) ?>

    <?= $form->field($model, 'code') ?>

    <?= $form->field($model, 'type')->dropDownList($storage_type_map, ['prompt' => '请选择']) ?>

    <?= $form->field($model, 'store_range') ?>

    <?php // echo $form->field($model, 'store_dir') ?>

    <?php  echo $form->field($model, 'value') ?>

    <?php  echo $form->field($model, 'sort_order') ?>

    <div class="form-group">
        <?= Html::submitButton('筛选', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
