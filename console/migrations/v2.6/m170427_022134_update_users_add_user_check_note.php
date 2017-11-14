<?php

use yii\db\Migration;

class m170427_022134_update_users_add_user_check_note extends Migration
{

    private $tableName = 'o_users';
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'user_check_note','TEXT COMMENT "用户审核意见"');
    }

    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'user_check_note');
    }
}
