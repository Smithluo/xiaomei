<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\BrandSpecGoods */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="brand-spec-goods-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-lg-3">
            <?= $form->field($model, 'spec_goods_cat_id')->widget(\kartik\widgets\Select2::className(), [
                'data' => \common\models\BrandSpecGoodsCat::getAllSpecGoodsCatMap(),
            ]) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'goods_id')->widget(\kartik\widgets\Select2::className(), [
                'data' => \backend\models\Goods::getGoodsMap(),
            ]) ?>
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
