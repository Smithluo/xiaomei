<?php

use yii\db\Migration;

class m170410_022642_create_o_season_category extends Migration
{
    private $tableName = 'o_season_category';

    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM';
        $this->createTable(
            $this->tableName,
            [
                'id' => $this->primaryKey(),
                'title' => $this->string(20)->notNull()->defaultValue('')->comment('标题'),
                'sort_order' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue(100)->comment('排序值'),
            ],
            $tableOptions
        );
        $this->createIndex('sort_order', $this->tableName, 'sort_order');
    }

    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }


}
