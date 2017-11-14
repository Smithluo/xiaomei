<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\NewArrivedGoods */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="new-arrived-goods-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-lg-2">
            <?= $form->field($model, 'goods_id')->widget(kartik\select2\Select2::className(), [
                'data' => \backend\models\Goods::getGoodsMap(),
            ]) ?>
        </div>
        <div class="col-lg-2">
            <?= $form->field($model, 'sort_order')->textInput() ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
