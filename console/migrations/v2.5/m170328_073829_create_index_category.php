<?php

use yii\db\Migration;

class m170328_073829_create_index_category extends Migration
{
    private $tableName = 'o_index_category';

    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM';
        $this->createTable(
            $this->tableName,
            [
                'id' => $this->primaryKey(),
                'title' => $this->string(20)->notNull()->defaultValue('')->comment('标题'),
                'm_url' => $this->string(50)->notNull()->defaultValue('')->comment('目标url'),
                'sort_order' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue(100)->comment('排序值'),
                'logo' => $this->string(50)->notNull()->defaultValue('')->comment('首页显示图片'),
            ],
            $tableOptions
        );
        $this->createIndex('sort_order', $this->tableName, 'sort_order');
    }

    public function safeDown()
    {
        $this->dropTable($this->tableName);
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
