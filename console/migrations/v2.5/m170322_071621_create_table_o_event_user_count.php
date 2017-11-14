<?php

use yii\db\Migration;

class m170322_071621_create_table_o_event_user_count extends Migration
{
    private $tableName = 'o_event_user_count';

    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable(
            $this->tableName,
            [
                'user_id' => $this->integer()->unsigned()->notNull()->defaultValue(0)->comment('用户ID'),
                'event_id' => $this->integer()->unsigned()->notNull()->defaultValue(0)->comment('用户参与活动的ID'),
                'count' => $this->integer()->unsigned()->notNull()->defaultValue(0)->comment('参与活动的次数'),
                'PRIMARY KEY (user_id, event_id)',
            ],
            $tableOptions
        );
    }

    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
