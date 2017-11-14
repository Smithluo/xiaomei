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

                <div class="col-lg-4">
                    <div class="col-lg-4">
                        货号：<?= $goods->goods_sn ?>
                    </div>
                    <div class="col-lg-3">
                        单价：￥<?= $goods->goods_price ?>
                    </div>
                    <div class="col-lg-2">
                        数量：x<?= $goods->goods_number ?>
                    </div>
                    <div class="col-lg-3">
                        小计：￥<?= \common\helper\NumberHelper::price_format($goods->goods_number * $goods->goods_price) ?>
                    </div>
                </div>

                <div class="col-lg-2">
                    <?php
                    if (!empty($model->deliveryOrders)) {
                        foreach ($model->deliveryOrders as $deliveryOrder) {
                            if (!empty($deliveryOrder->invoice_no)) {
                                foreach ($deliveryOrder->deliveryGoods as $deliveryGoods) {
                                    if ($deliveryGoods->goods_id == $goods->goods_id) {
                                        echo '物流单号: <span>'.$deliveryOrder->invoice_no.'</span>';
                                    }
                                }
                            }
                        }
                    }
                    ?>
                </div>
            </div>

        <?php endforeach ?>

    <?php endforeach ?>
</div>