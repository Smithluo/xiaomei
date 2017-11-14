<?php

use yii\db\Migration;

class m161205_054918_update_o_order_info_add_index_to_group_id extends Migration
{
    public function safeUp()
    {
        $this->createIndex('group_id', 'o_order_info', 'group_id');
        $this->createIndex('supplier_user_id', 'o_order_info', 'supplier_user_id');
    }

    public function safeDown()
    {
        $this->dropIndex('group_id', 'o_order_info');
        $this->dropIndex('supplier_user_id', 'o_order_info');
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
