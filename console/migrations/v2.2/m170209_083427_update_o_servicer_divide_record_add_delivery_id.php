<?php

use yii\db\Migration;

class m170209_083427_update_o_servicer_divide_record_add_delivery_id extends Migration
{
    private $tableName = 'o_servicer_divide_record';

    public function safeUp()
    {
        $this->addColumn($this->tableName, 'delivery_id', 'INT(10) UNSIGNED NOT NULL DEFAULT "0"');
    }

    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'delivery_id');
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
