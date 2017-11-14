<?php

use yii\db\Migration;

class m170407_084523_create_o_index_zhifa_youxuan extends Migration
{
    private $tableName = 'o_index_zhifa_youxuan';
    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM';
        $this->createTable(
            $this->tableName,
            [
                'id' => $this->primaryKey(),
                'image' => $this->string(255)->notNull()->defaultValue('')->comment('图片'),
                'url' => $this->string(255)->notNull()->defaultValue('')->comment('跳转链接'),
                'sort_order' => $this->smallInteger(5)->notNull()->defaultValue(0)->comment('排序值'),
            ],
            $tableOptions
        );
        $this->createIndex('sort_order', $this->tableName, 'sort_order');
    }

    public function down()
    {
        $this->dropTable($this->tableName);
    }
}
