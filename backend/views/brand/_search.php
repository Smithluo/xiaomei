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
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-2\">{input}</div>",
            'labelOptions' => ['class' => 'col-lg-1 control-label text-right'],
        ],
    ]); ?>

    <?php
    $brandList = \backend\models\Brand::find()->asArray()->all();
    $data = array_column($brandList, 'brand_name', 'brand_id');
    foreach ($data as $k => $value) {
        $data[$k] = $value. '('. $k. ')';
    }
    echo $form->field($model, 'brand_id')->widget(\kartik\widgets\Select2::classname(), [
        'data' => $data,
        'options' => ['placeholder' => '选择品牌'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]);
    ?>

    <?= $form->field($model, 'brand_depot_area') ?>

    <?php // echo $form->field($model, 'brand_bgcolor') ?>

    <?php // echo $form->field($model, 'brand_policy') ?>

    <?php // echo $form->field($model, 'brand_desc') ?>

    <?php // echo $form->field($model, 'brand_desc_long') ?>

    <?php  echo $form->field($model, 'short_brand_desc') ?>

<!--    --><?php // echo $form->field($model, 'site_url') ?>

    <?php // echo $form->field($model, 'sort_order') ?>

<!--    --><?php // echo $form->field($model, 'is_show') ?>

    <?php // echo $form->field($model, 'album_id') ?>

    <?php // echo $form->field($model, 'brand_tag') ?>

<!--    --><?php // echo $form->field($model, 'servicer_strategy_id') ?>

<!--    --><?php // echo $form->field($model, 'supplier_user_id') ?>

    <div class="form-group">
        <?= Html::submitButton('筛选', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('重置', ['brand/index'], ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
