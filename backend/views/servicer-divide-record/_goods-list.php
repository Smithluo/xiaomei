<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/15 0015
 * Time: 14:04
 */

?>

<div>

<?php foreach ($model->ordergoods as $index => $goods): ?>

    <div class="row" style="height: 60px;">
        <div class="col-lg-1">
            商品ID：<?= $goods->goods_id ?>
        </div>
        <div class="col-lg-1">
            商品缩略图：<?= \yii\helpers\Html::img(\common\helper\ImageHelper::get_image_path($goods->goods->goods_thumb), [
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
            数量：x<?= $goods->goods_number ?>
        </div>
        <div class="col-lg-1">
            小计：￥<?= \common\helper\NumberHelper::price_format($goods->goods_number * $goods->goods_price) ?>
        </div>
        <div class="col-lg-4"></div>
    </div>

<?php endforeach ?>
</div>