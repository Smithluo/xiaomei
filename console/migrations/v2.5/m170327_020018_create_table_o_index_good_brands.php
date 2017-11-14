<?php

use yii\db\Migration;

class m170327_020018_create_table_o_index_good_brands extends Migration
{
    public $tableName = 'o_index_good_brands';
    public function safeUp()
    {

        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM';
        $this->createTable(
            $this->tableName,
            [
                'id' => $this->primaryKey(),
                'brand_id' => $this->integer(8)->unsigned()->notNull()->defaultValue(0)->comment('品牌id'),
                'title' => $this->string(20)->notNull()->defaultValue('')->comment('标题'),
                'sort_order' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue(100)->comment('排序值'),
                'index_logo' => $this->string(128)->notNull()->defaultValue('')->comment('首页显示的logo'),

            ],
            $tableOptions
        );
        $this->createIndex('brand_id', $this->tableName, 'brand_id');
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
