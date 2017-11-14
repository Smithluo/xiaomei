<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\IndexStarBrandConf */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="index-star-brand-conf-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-lg-3">
            <?php
            $brands = \backend\models\Brand::find()->where(['is_show' => 1])->asArray()->all();
            $data = array_column($brands, 'brand_name', 'brand_id');
            echo $form->field($model, 'brand_id')->widget(\kartik\widgets\Select2::classname(), [
                'data' => $data,
                'options' => ['placeholder' => '选择品牌'],
            ]);
            ?>
        </div>
        <div class="col-lg-3">
            <?php
            $tabs = \common\models\IndexStarGoodsTabConf::find()->asArray()->all();
            $data = array_column($tabs, 'tab_name', 'id');
            echo $form->field($model, 'tab_id')->widget(kartik\widgets\Select2::className(), [
                'data' => $data,
                'options' => [
                    'placeholder' => '选择tab标签',
                ],
            ])
            ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'sort_order')->textInput() ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '创建' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
