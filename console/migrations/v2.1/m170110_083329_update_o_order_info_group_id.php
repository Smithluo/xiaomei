<?php

use yii\db\Migration;

class m170110_083329_update_o_order_info_group_id extends Migration
{
    public function safeUp()
    {
        $this->alterColumn('o_order_info', 'group_id', 'VARCHAR(22) NOT NULL DEFAULT "" COMMENT "大单号"');
        $this->alterColumn('o_order_group', 'group_id', 'VARCHAR(22) NOT NULL DEFAULT "" COMMENT "大单号"');

        $this->dropIndex('group_id', 'o_order_group');
        $this->createIndex('group_id', 'o_order_group', 'group_id', true);
    }

    public function safeDown()
    {
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
