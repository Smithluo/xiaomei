<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\CouponTopicGoodsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="coupon-topic-goods-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-lg-2">
            <?= $form->field($model, 'id') ?>
        </div>
        <div class="col-lg-2">
            <?= $form->field($model, 'goods_id')->widget(\kartik\widgets\Select2::className(), [
                'data' => \backend\models\Goods::getGoodsMap(),
                'options' => [
                    'placeholder' => '搜索商品',
                ],
                'pluginOptions' => [
                    'allowClear' => 1,
                ],
            ])?>
        </div>
        <div class="col-lg-2">
            <?= $form->field($model, 'sort_order') ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
