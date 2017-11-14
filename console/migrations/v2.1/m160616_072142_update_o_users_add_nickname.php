<?php

use yii\db\Migration;

class m160616_072142_update_o_users_add_nickname extends Migration
{
    public function up()
    {
        $this->addColumn('o_users', 'nickname', 'VARCHAR(20) NOT NULL DEFAULT""');
    }

    public function down()
    {
        echo "m160616_072142_update_o_users_add_nickname cannot be reverted.\n";

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
