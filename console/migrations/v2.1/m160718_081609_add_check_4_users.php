<?php

use yii\db\Migration;

class m160718_081609_add_check_4_users extends Migration
{
    public $tb_name = 'o_users';

    public function safeUp()
    {
        //  添加用户审核状态
        $this->addColumn($this->tb_name, 'is_checked', "SMALLINT NULL DEFAULT '0' COMMENT '审核状态' AFTER `nickname`");
        $this->createIndex('is_checked', $this->tb_name, 'is_checked');

        //  添加审核备注（预留给2期审核不通过时填写）
        $this->addColumn($this->tb_name, 'checked_note', "VARCHAR(255) NULL COMMENT '审核意见' AFTER `is_checked`");
    }

    public function safeDown()
    {
        $this->dropColumn($this->tb_name, 'is_checked');
        $this->dropColumn($this->tb_name, 'checked_note');
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
