<?php

use yii\db\Migration;

class m160701_020904_o_brand_addcolumn_supplier_user_id extends Migration
{
    public function safeUp()
    {
        $this->addColumn('o_brand', 'supplier_user_id', "INT NOT NULL DEFAULT '0' COMMENT '所属品牌商的user_id' AFTER `servicer_strategy_id`");
        $this->createIndex('supplier_user_id', 'o_brand','supplier_user_id');
    }

    public function safeDown()
    {
        echo "m160701_020904_o_brand_addcolumn_supplier_user_id cannot be reverted.\n";

        return false;
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
