<?php

use yii\db\Migration;

class m160606_055134_update_o_users extends Migration
{
    public function up()
    {
        $this->addColumn('o_users', 'bank_info_id', 'INT(10) NOT NULL COMMENT "财务信息ID"');
    }

    public function down()
    {
        echo "m160606_055134_update_o_users cannot be reverted.\n";

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
