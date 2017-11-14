<?php

use yii\db\Migration;

class m161118_112221_alter_o_users_add_index_to_token_mobile extends Migration
{
    public function safeUp()
    {
        $this->createIndex('access_token', 'o_users', 'access_token');
        $this->createIndex('mobile_phone', 'o_users', 'mobile_phone');
    }

    public function safeDown()
    {
        $this->dropIndex('access_token', 'o_users');
        $this->dropIndex('mobile_phone', 'o_users');
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
