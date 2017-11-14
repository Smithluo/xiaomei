<?php

use yii\db\Migration;

class m160920_031235_update_o_goods_add_supplier_user_id_and_shipping_id extends Migration
{
    public function safeUp()
    {
        $this->addColumn('o_goods', 'supplier_user_id', 'INT(10) NOT NULL DEFAULT 0 COMMENT "供应商ID"');
        $this->addColumn('o_goods', 'shipping_id', 'SMALLINT(5) NOT NULL DEFAULT 0 COMMENT "运费模版ID"');
        $this->createIndex('supplier_user_id', 'o_goods', 'supplier_user_id');
    }

    public function safeDown()
    {
        $this->dropIndex('supplier_user_id', 'o_goods');
        $this->dropColumn('o_goods', 'supplier_user_id');
        $this->dropColumn('o_goods', 'shipping_id');
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
