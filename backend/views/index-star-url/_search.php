<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\IndexStarUrlSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="index-star-url-search">

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
            $tabs = \common\models\IndexStarGoodsTabConf::find()->asArray()->all();
            $data = array_column($tabs, 'tab_name', 'id');
            echo $form->field($model, 'tab_id')->widget(kartik\widgets\Select2::className(), [
                'data' => $data,
                'options' => [
                    'placeholder' => '选择楼层',
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])
            ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'title') ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'url') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3">
        <?= $form->field($model, 'sort_order') ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
