<?php

use yii\db\Migration;
use common\models\Cart;

/**
 * Class m171009_065916_group_buy_add_to_cart
 * 修改批量加入购物车的商家 在cart表中的标记 extension_code group 改为 batch
 * 支持团采商品加入购物车，团采商品的 extension_code 改为 group_buy
 * 避免混淆
 */
class m171009_065916_group_buy_add_to_cart extends Migration
{
    public function safeUp()
    {
        Cart::updateAll(['extension_code' => Cart::EXTENSION_CODE_BATCH], ['extension_code' => 'group']);
    }

    public function safeDown()
    {
        Cart::updateAll(['extension_code' => 'group'], ['extension_code' => Cart::EXTENSION_CODE_BATCH]);;
    }
}
