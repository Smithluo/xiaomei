<?php

use yii\db\Migration;

class m170608_114411_update_o_favourite_search_add_type extends Migration
{
    private $tableName = 'o_favourite_search';

    public function up()
    {
        $this->addColumn($this->tableName, 'type', 'SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0 COMMENT "类型"');
        $this->createIndex('type', $this->tableName, 'type');
    }

    public function down()
    {
        $this->dropColumn($this->tableName, 'type');
        return true;
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
