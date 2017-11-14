<?php

use yii\db\Migration;

class m170411_105607_create_favourite_search extends Migration
{
    private $tableName = 'o_favourite_search';

    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM';
        $this->createTable(
            $this->tableName,
            [
                'id' => $this->primaryKey(),
                'user_id' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue(0)->comment('用户'),
                'content' => $this->string(64)->notNull()->defaultValue('')->comment('搜索内容'),
                'search_time' => $this->timestamp()->comment('搜索时间'),
            ],
            $tableOptions
        );
        $this->createIndex('user_id', $this->tableName, 'user_id');
    }

    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
