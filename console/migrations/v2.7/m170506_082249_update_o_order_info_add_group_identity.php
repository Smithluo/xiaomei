<?php

use yii\db\Migration;

class m170506_082249_update_o_order_info_add_group_identity extends Migration
{
    private $tableNameOrderInfo = 'o_order_info';

    public function safeUp()
    {
        $this->addColumn($this->tableNameOrderInfo, 'group_identity', 'INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT "总单的主键"');
        $this->createIndex('group_identity', $this->tableNameOrderInfo, 'group_identity');
    }

    public function safeDown()
    {
        $this->dropColumn($this->tableNameOrderInfo, 'group_identity');
        return true;
    }
}
