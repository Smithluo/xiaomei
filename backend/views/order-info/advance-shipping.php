<?php

use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\OrderInfo */

$this->title = '自定义发货';

?>
<div>

    <?php $form = ActiveForm::begin([
        'action' => ['advance-shipping', 'id' => $model['order_id']],
        'method' => 'post',
    ]) ?>

    <?php foreach ($orderGoods as $index => $goods): ?>
        <div class="row" style="height: 90px;">
            <div class="col-lg-1">
                商品ID：<?= $goods->goods_id ?>
            </div>
            <div class="col-lg-1">
                商品缩略图：<?= \yii\helpers\Html::img($goods->getGoodsThumb(), [
                    'width' => '50px',
                    'height' => '50px',
                ]) ?>
            </div>
            <div class="col-lg-4">
                商品名：<?= $goods->goods_name ?>
            </div>
            <div class="col-lg-2">
                货号：<?= $goods->goods_sn ?>
            </div>
            <div class="col-lg-1">
                单价：￥<?= $goods->goods_price ?>
            </div>
            <div class="col-lg-1">
                总采购数量：x<?= $goods->goods_number ?>
            </div>
            <div class="col-lg-1">
                待发货数量：x<?= ($goods->goods_number - $goods->send_number) ?>
            </div>
            <div class="col-lg-1">
                <?= $form->field($goods, "[$index]shippingNum") ?>
            </div>
            <div class="col-lg-4"></div>
        </div>

    <?php endforeach ?>

    <div class="row">
        物流信息：<?= \yii\bootstrap\Html::input('text', 'shippingInfo') ?>
    </div>

    <div class="row">
    <?= \yii\helpers\Html::submitButton('发货', ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end() ?>
</div>