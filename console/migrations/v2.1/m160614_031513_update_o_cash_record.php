<?php

use yii\db\Migration;

class m160614_031513_update_o_cash_record extends Migration
{
    public function up()
    {
        $this->addColumn('o_cash_record', 'balance', 'DECIMAL(10,2) UNSIGNED NOT NULL DEFAULT "0"');
    }

    public function down()
    {
        echo "m160614_031513_update_o_cash_record cannot be reverted.\n";

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
