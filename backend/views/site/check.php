<?php
/**
 * Created by PhpStorm.
 * User: clark
 * Date: 2017/5/19
 * Time: 17:11
 */

use yii\helpers\Html;
?>

<div class="row">
    <div class="col-lg-4">
        <ol>
            <li>
                商品库存为0 的商品数量：<?=$goodsStockEmptyNum?>
                <?php
                    if ($goodsStockEmptyNum > 0) {
                        echo Html::a('查看详情', '/check/goods-stock-empty');
                    }
                ?>
            </li>
            <li>
                商品名称为空 的商品数量：<?=$goodsNameEmptyNum?>
                <?php
                    if ($goodsNameEmptyNum > 0) {
                        echo Html::a('查看详情', '/check/goods-name-empty');
                    }
                ?>
            </li>
            <li>
                商品市场价为0 的商品数量：<?=$marketPriceUnsetNum?>
                <?php
                    if ($marketPriceUnsetNum > 0) {
                        echo Html::a('查看详情', '/check/market-price-unset');
                    }
                ?>
            </li>
            <li>
                直发商品重量为0 的商品数量：<?=$directGoodsNoWeightNum?>
                <?php
                    if ($directGoodsNoWeightNum > 0) {
                        echo Html::a('查看详情', '/check/direct-goods-no-weight');
                    }
                ?>
            </li>
        </ol>
    </div>
</div>
