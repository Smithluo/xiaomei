<?php

use yii\db\Migration;

class m161024_070710_update_o_servicer_divide_record_add_index extends Migration
{
    public function safeUp()
    {
        $this->createIndex('user_id', 'o_servicer_divide_record', 'user_id');
        $this->createIndex('servicer_user_id', 'o_servicer_divide_record', 'servicer_user_id');
        $this->createIndex('parent_servicer_user_id', 'o_servicer_divide_record', 'parent_servicer_user_id');
        $this->createIndex('user_id', 'o_cash_record', 'user_id');
    }

    public function safeDown()
    {
        $this->dropIndex('user_id', 'o_servicer_divide_record');
        $this->dropIndex('servicer_user_id', 'o_servicer_divide_record');
        $this->dropIndex('parent_servicer_user_id', 'o_servicer_divide_record');
        $this->dropIndex('user_id', 'o_cash_record');
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
