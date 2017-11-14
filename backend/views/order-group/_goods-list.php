<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/15 0015
 * Time: 14:04
 */
use yii\helpers\Html;
use yii\helpers\Url;

?>

<div>

    <?php foreach ($model->orders as $orderInfo): ?>
        <?php foreach ($orderInfo->ordergoods as $index => $goods):?>

            <div class="row" style="height: 60px;">
                <div class="col-lg-1">
                    商品ID：<?= Html::a($goods->goods_id, 'http://www.xiaomei360.com/goods.php?id='. $goods->goods_id, [
                        'target' => '_blank',
                    ]) ?>
                </div>
                <div class="col-lg-1">
                    商品缩略图：<?= Html::img($goods->getGoodsThumb(), [
                        'width' => '50px',
                        'height' => '50px',
                    ]) ?>
                </div>
                <div class="col-lg-4">
                    商品名：<?= Html::a($goods->goods_name, Url::to([
                        '/goods/view', 'id' => $goods->goods_id,
                    ]), [
                        'target' => '_blank',
                    ]) ?>
                </div>
                <div class="col-lg-2">
                    货号：<?= $goods->goods_sn ?>
                </div>
                <div class="col-lg-2">
                    会员折扣：<?= $goods['goods']['discount_disable'] == 1 ? '不参与': '参与' ?>
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

    <?php endforeach ?>
</div>