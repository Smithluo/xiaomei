<?php

use yii\db\Migration;

class m170302_100829_update_o_order_goods_add_back_number extends Migration
{
    private $tableName = 'o_order_goods';

    public function safeUp()
    {
        $this->addColumn($this->tableName, 'back_number', 'SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0 COMMENT "退款/退货商品数量" AFTER send_number');
    }

    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'back_number');
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
