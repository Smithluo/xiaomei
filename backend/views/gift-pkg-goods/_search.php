<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\GiftPkgGoodsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="gift-pkg-goods-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'fieldConfig' => [
            'template' => "<div class='row'>
                {label}\n
                <div class=\"col-lg-8\">{input}</div>\n
                <div class=\"col-lg-4\"></div>
                <div class=\"col-lg-8\">{error}</div>
            </div>",
            'labelOptions' => ['class' => 'col-lg-4 control-label text-right'],
        ],
    ]); ?>

    <div class="row">
        <div class="col-lg-4">
            <?=$form->field($model, 'gift_pkg_id')->widget(\kartik\widgets\Select2::classname(), [
                'data' => $giftPkgList,
                'options' => ['placeholder' => '请选择礼包互动'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);?>
        </div>
        <div class="col-lg-4">
            
            <?=$form->field($model, 'goods_id')->widget(\kartik\widgets\Select2::classname(), [
                'data' => $goodsList,
                'options' => ['placeholder' => '请选择商品'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);?>
        </div>
        <div class="col-lg-4">
            <div class="form-group">
                <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
                <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
