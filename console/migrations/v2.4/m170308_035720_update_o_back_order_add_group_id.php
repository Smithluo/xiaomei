<?php

use yii\db\Migration;

class m170308_035720_update_o_back_order_add_group_id extends Migration
{
    private $tableName = 'o_back_order';

    public function safeUp()
    {
        $this->addColumn($this->tableName, 'group_id', 'VARCHAR(22) NOT NULL DEFAULT "" COMMENT "总单号"');
        $this->createIndex('group_id', $this->tableName, 'group_id');
    }

    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'group_id');
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
