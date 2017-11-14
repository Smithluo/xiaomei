<?php

use yii\db\Migration;

class m170411_113932_update_favourite_search_add_search_count extends Migration
{
    private $tableName = 'o_favourite_search';
    public function up()
    {
        $this->addColumn($this->tableName, 'search_count', 'SMALLINT(4) UNSIGNED NOT NULL DEFAULT 0 COMMENT "搜索次数"');
        $this->createIndex('content', $this->tableName, 'content');
        $this->createIndex('search_time', $this->tableName, 'search_time');
    }

    public function down()
    {
        $this->dropColumn($this->tableName, 'search_count');
        $this->dropIndex($this->tableName, 'content');
        $this->dropIndex($this->tableName, 'search_time');
    }

}
