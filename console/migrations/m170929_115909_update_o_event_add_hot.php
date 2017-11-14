<?php

use yii\db\Migration;

class m170929_115909_update_o_event_add_hot extends Migration
{
    private $tableEvent = 'o_event';

    public function safeUp()
    {
        $this->addColumn($this->tableEvent, 'hot', 'TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT "是否热门"');
        $this->createIndex('hot', $this->tableEvent, 'hot');
        return true;
    }

    public function safeDown()
    {
        $this->dropColumn($this->tableEvent, 'hot');
        return true;
    }

}
