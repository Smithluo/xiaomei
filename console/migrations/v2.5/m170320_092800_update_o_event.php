<?php

use yii\db\Migration;

class m170320_092800_update_o_event extends Migration
{
    private  $table = 'o_event';
    public function safeUp()
    {
        $this->addColumn($this->table, 'sort_order', 'SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0 COMMENT "排序值"');
    }

    public function down()
    {
        $this->dropColumn($this->table,'sort_order');

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
