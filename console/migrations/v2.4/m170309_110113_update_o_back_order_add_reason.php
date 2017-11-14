<?php

use yii\db\Migration;

class m170309_110113_update_o_back_order_add_reason extends Migration
{
    private $backOrderTableName = 'o_back_order';
    private $backGoodsTableName = 'o_back_goods';

    public function up()
    {
        $this->addColumn($this->backOrderTableName, 'reason', 'TEXT COMMENT "退款退货原因"');
        $this->addColumn($this->backGoodsTableName, 'goods_price', 'DECIMAL(10,2) UNSIGNED NOT NULL DEFAULT 0 COMMENT "商品下单时候的价格"');
    }

    public function down()
    {
        $this->dropColumn($this->backOrderTableName, 'reason');
        $this->dropColumn($this->backGoodsTableName, 'goods_price');
        return true;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
