<?php

use yii\db\Migration;

class m170221_121742_update_o_delivery_goods_add_order_goods_rec_id extends Migration
{
    private $tableName = 'o_delivery_goods';

    public function safeUp()
    {
        $this->addColumn($this->tableName, 'order_goods_rec_id', 'MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 COMMENT "关联的订单商品记录id"');
        $this->createIndex('order_goods_rec_id', $this->tableName, 'order_goods_rec_id');
    }

    public function safeDown()
    {
        $this->dropIndex('order_goods_rec_id', $this->tableName);
        $this->dropColumn($this->tableName, 'order_goods_rec_id');
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
