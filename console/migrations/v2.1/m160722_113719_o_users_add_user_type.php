<?php

use yii\db\Migration;

class m160722_113719_o_users_add_user_type extends Migration
{
    public function safeUp()
    {
        $this->addColumn('o_users', 'user_type', " TINYINT NOT NULL DEFAULT 1 COMMENT '用户类别' ");
        $this->createIndex('user_type', 'o_users', 'user_type');
    }

    public function safeDown()
    {
        echo "m160722_113719_o_users_add_user_type cannot be reverted.\n";

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
