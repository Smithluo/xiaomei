<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\ActivityManzengSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="activity-manzeng-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-lg-3">
            <?php
            $data = \backend\models\Goods::getGoodsMap();
            echo $form->field($model, 'goods_id')->widget(\kartik\widgets\Select2::classname(), [
                'data' => $data,
                'options' => ['placeholder' => '选择商品'],
            ]);
            ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'sort_order')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
