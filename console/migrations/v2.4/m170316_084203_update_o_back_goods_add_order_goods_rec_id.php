<?php

use yii\db\Migration;

class m170316_084203_update_o_back_goods_add_order_goods_rec_id extends Migration
{

    private $tableName = 'o_back_goods';

    public function safeUp()
    {
        $this->addColumn($this->tableName, 'order_goods_rec_id', 'MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 COMMENT "关联的订单商品记录id"');
        $this->createIndex('order_goods_rec_id', $this->tableName, 'order_goods_rec_id');
    }

    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'order_goods_rec_id');
        return true;
    }

}
