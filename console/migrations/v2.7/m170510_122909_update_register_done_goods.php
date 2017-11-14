<?php

use yii\db\Migration;

class m170510_122909_update_register_done_goods extends Migration
{


    private  $tableName = 'o_register_done_goods';
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'sort_order', 'INT(5) UNSIGNED NOT NULL DEFAULT "0" COMMENT"排序值"');
    }

    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'sort_order');
    }

}
