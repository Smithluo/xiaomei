<?php

use yii\db\Migration;

class m160603_063428_update_o_brand extends Migration
{
    public function up()
    {
        $this->addColumn('o_brand', 'servicer_strategy_id', 'INT(10) UNSIGNED NOT NULL DEFAULT "0"');
    }

    public function down()
    {
        $this->dropColumn('o_brand', 'servicer_strategy_id');
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
