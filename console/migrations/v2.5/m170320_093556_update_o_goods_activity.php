<?php

use yii\db\Migration;

class m170320_093556_update_o_goods_activity extends Migration
{
    private  $table = 'o_goods_activity';
    public function safeUp()
    {
        $this->addColumn($this->table, 'sort_order', 'SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0 COMMENT "排序值"');
    }

    public function safeDown()
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
