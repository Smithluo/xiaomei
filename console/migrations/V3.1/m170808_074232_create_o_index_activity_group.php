<?php

use yii\db\Migration;

class m170808_074232_create_o_index_activity_group extends Migration
{
    private $tableName = 'o_index_activity_group';

    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM';
        $this->createTable(
            $this->tableName,
            [
                'id' => $this->primaryKey()->comment('ID'),
                'type' => $this->smallInteger()->unsigned()->notNull()->defaultValue(0)->comment('类型'),
                'title' => $this->string(30)->notNull()->defaultValue('')->comment('标题'),
                'desc' => $this->string(50)->notNull()->defaultValue('')->comment('描述'),
                'sort_order' => $this->smallInteger()->unsigned()->notNull()->defaultValue(1000)->comment('排序值'),
                'is_show' => $this->boolean()->unsigned()->notNull()->defaultValue(1)->comment('是否显示'),
            ],
            $tableOptions
        );

        $this->createIndex('is_show', $this->tableName, 'is_show');
        $this->createIndex('type', $this->tableName, 'type');
        $this->createIndex('sort_order', $this->tableName, 'sort_order');
    }

    public function safeDown()
    {
        $this->dropTable($this->tableName);
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170808_074232_create_o_index_activity_group cannot be reverted.\n";

        return false;
    }
    */
}
