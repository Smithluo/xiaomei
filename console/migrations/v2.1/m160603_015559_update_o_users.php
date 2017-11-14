<?php

use yii\db\Migration;

class m160603_015559_update_o_users extends Migration
{
    public function up()
    {
        $this->addColumn('o_users', 'servicer_user_id', 'MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT "0"');
        $this->addColumn('o_users', 'servicer_super_id', 'MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT "0"');
        $this->addColumn('o_users', 'auth_key', 'VARCHAR(32) NOT NULL DEFAULT""');
        $this->addColumn('o_users', 'access_token', 'VARCHAR(255) NOT NULL DEFAULT""');
        $this->addColumn('o_users', 'servicer_info_id', 'INT(10) UNSIGNED NOT NULL DEFAULT "0"');
    }

    public function down()
    {
        $this->dropColumn('o_users', 'servicer_user_id');
        $this->dropColumn('o_users', 'super_id');
        $this->dropColumn('o_users', 'auth_key');
        $this->dropColumn('o_users', 'access_token');
        $this->dropColumn('o_users', 'servicer_info_id');
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
