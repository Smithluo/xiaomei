<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\IndexHotBrandSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="index-hot-brand-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-lg-3">
        <?= $form->field($model, 'id') ?>
        </div>
        <div class="col-lg-3">
        <?php
        $brandList = \backend\models\Brand::find()->where(['is_show' => 1])->asArray()->all();
        $data = array_column($brandList, 'brand_name', 'brand_id');
        echo $form->field($model, 'brand_id')->widget(kartik\widgets\Select2::className(), [
            'data' => $data,
            'options' => ['placeholder' => '选择品牌'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ])?>
        </div>
        <div class="col-lg-3">
        <?= $form->field($model, 'sort_order') ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
