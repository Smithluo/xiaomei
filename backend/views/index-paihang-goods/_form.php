<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\IndexPaihangFloor;

/* @var $this yii\web\View */
/* @var $model common\models\IndexPaihangGoods */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="index-paihang-goods-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-lg-2">
            <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-2">
            <?php
            $floorList = IndexPaihangFloor::find()->asArray()->all();
            $data = array_column($floorList, 'title', 'id');
            echo $form->field($model, 'floor_id')->widget(\kartik\widgets\Select2::classname(), [
                'data' => $data,
                'options' => ['placeholder' => '选择品类配置'],
            ]) ?>
        </div>
        <div class="col-lg-4">
            <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
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
            <?= $form->field($model, 'sort_order')->textInput() ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '新建' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
