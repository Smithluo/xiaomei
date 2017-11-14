<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\IndexStarUrl */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="index-star-url-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-lg-3">
            <?php
            $tabs = \common\models\IndexStarGoodsTabConf::find()->asArray()->all();
            $data = array_column($tabs, 'tab_name', 'id');
            echo $form->field($model, 'tab_id')->widget(kartik\widgets\Select2::className(), [
                'data' => $data,
                'options' => [
                    'placeholder' => '选择楼层',
                ],
            ])
            ?>
        </div>
        <div class="col-lg-3">
        <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-3">
        <?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-3">
        <?= $form->field($model, 'sort_order')->textInput() ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
