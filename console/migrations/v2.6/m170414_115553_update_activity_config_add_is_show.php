<?php

use yii\db\Migration;

class m170414_115553_update_activity_config_add_is_show extends Migration
{

    private $tableName = 'o_activity_config';
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'is_show', 'SMALLINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT "是否显示"');
        $this->createIndex('is_show', $this->tableName, 'is_show');
    }

    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'is_show');
    }

}
