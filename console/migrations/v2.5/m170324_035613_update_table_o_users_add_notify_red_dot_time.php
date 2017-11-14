<?php

use yii\db\Migration;

class m170324_035613_update_table_o_users_add_notify_red_dot_time extends Migration
{
    private $tableName = 'o_users_notify_time';

    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable(
            'o_users_notify_time',
            [
                'user_id' => $this->integer(8)->unsigned()->notNull()->defaultValue(0)->comment('用户ID'),
                'notify_time' => $this->dateTime()->notNull()->defaultValue(0)->comment('最后查看公告列表的时间'),
                'PRIMARY KEY (user_id)',
            ],
            $tableOptions
        );
    }

    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }

}
