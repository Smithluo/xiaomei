<?php

use yii\db\Migration;

class m170803_070914_create_o_index_keywords extends Migration
{
    private $tableKeywordsGroup = 'o_index_keywords_group';
    private $tableKeywords = 'o_index_keywords';
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM';
        $this->createTable(
            $this->tableKeywordsGroup,
            [
                'id' => $this->primaryKey()->comment('ID'),
                'title' => $this->string(60)->notNull()->defaultValue('')->comment('标题'),
                'cat_id' => $this->smallInteger(5)->notNull()->defaultValue(0)->comment('分类ID'),
                'scene' => $this->string(20)->notNull()->defaultValue('')->comment('场景'),
                'sort_order' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue(1000)->comment('排序值'),
                'is_show' => $this->boolean()->unsigned()->notNull()->defaultValue(1)->comment('是否显示'),
            ],
            $tableOptions
        );

        $this->createIndex('scene', $this->tableKeywordsGroup, 'scene');
        $this->createIndex('sort_order', $this->tableKeywordsGroup, 'sort_order');
        $this->createIndex('is_show', $this->tableKeywordsGroup, 'is_show');

        $this->createTable(
            $this->tableKeywords,
            [
                'id' => $this->primaryKey()->comment('ID'),
                'group_id' => $this->integer()->unsigned()->notNull()->defaultValue(0)->comment('关键词组'),
                'title' => $this->string(20)->notNull()->defaultValue('')->comment('标题'),
                'sort_order' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue(0)->comment('排序值'),
                'is_show' => $this->boolean()->unsigned()->notNull()->defaultValue(1)->comment('是否显示'),
                'url' => $this->string(255)->notNull()->defaultValue('')->comment('跳转链接'),
            ],
            $tableOptions
        );

        $this->createIndex('group_id', $this->tableKeywords, 'group_id');
        $this->createIndex('is_show', $this->tableKeywords, 'is_show');
        $this->createIndex('sort_order', $this->tableKeywords, 'sort_order');
    }

    public function safeDown()
    {
        $this->dropTable($this->tableKeywordsGroup);
        $this->dropTable($this->tableKeywords);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170803_070914_create_o_index_keywords cannot be reverted.\n";

        return false;
    }
    */
}
