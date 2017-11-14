<?php

use yii\db\Migration;

class m170325_021824_create_table_o_index_group_buy extends Migration
{
    private $tableName = 'o_index_group_buy';
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM';
        $this->createTable(
            $this->tableName,
            [
                'id' => $this->primaryKey(),
                'activity_id' => $this->integer(8)->unsigned()->notNull()->defaultValue(0)->comment('团采ID'),
                'sort_order' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue(100)->comment('排序值'),
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
