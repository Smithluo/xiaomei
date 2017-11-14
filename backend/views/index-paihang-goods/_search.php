<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\IndexPaihangGoodsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="index-paihang-goods-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-lg-2">
            <?= $form->field($model, 'id') ?>
        </div>
        <div class="col-lg-2">
            <?= $form->field($model, 'title') ?>
        </div>
        <div class="col-lg-2">
            <?= $form->field($model, 'description') ?>
        </div>
        <div class="col-lg-2">
            <?php
            $goodsList = \backend\models\Goods::find()->where(['is_on_sale' => 1, 'is_delete' => 0])->asArray()->all();
            $data = array_column($goodsList, 'goods_name', 'goods_id');
            echo $form->field($model, 'goods_id')->widget(\kartik\widgets\Select2::classname(), [
                'data' => $data,
                'options' => ['placeholder' => '选择商品'],
            ]);
            ?>
        </div>
        <div class="col-lg-2">
            <?= $form->field($model, 'sort_order') ?>
        </div>
        <div class="col-lg-2">
            <?php
            $floorList = \common\models\IndexPaihangFloor::find()->asArray()->all();
            $data = array_column($floorList, 'title', 'id');
            echo $form->field($model, 'floor_id')->widget(\kartik\widgets\Select2::classname(), [
                'data' => $data,
                'options' => ['placeholder' => '选择品类配置'],
            ]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-2">
            <?php
            $goodsList = \backend\models\Goods::find()->where(['is_on_sale' => 1, 'is_delete' => 0])->asArray()->all();
            $data = array_column($goodsList, 'goods_name', 'goods_id');
            echo $form->field($model, 'goods_id')->widget(\kartik\widgets\Select2::classname(), [
                'data' => $data,
                'options' => ['placeholder' => '选择商品'],
            ]);
            ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
