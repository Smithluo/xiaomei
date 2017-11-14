<?php

use yii\db\Migration;

class m160706_025953_alter_goods_add_start_num extends Migration
{
    public function up()
    {
        $this->addColumn('o_goods', 'start_num', " TINYINT UNSIGNED NOT NULL DEFAULT '1' COMMENT '起售数量' AFTER `servicer_strategy_id`");
    }

    public function down()
    {
        echo "m160706_025953_alter_goods_add_start_num cannot be reverted.\n";

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
