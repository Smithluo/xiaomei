<?php

use yii\db\Migration;

class m170506_014051_update_o_order_add_offline extends Migration
{
    private $tableNameOrderGroup = 'o_order_group';
    private $tableNameOrderInfo = 'o_order_info';

    public function safeUp()
    {
        $this->addColumn($this->tableNameOrderGroup, 'offline', 'TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT "是否线下订单"');
        $this->addColumn($this->tableNameOrderInfo, 'offline', 'TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT "是否线下订单"');

        $this->createIndex('offline', $this->tableNameOrderGroup, 'offline');
        $this->createIndex('offline', $this->tableNameOrderInfo, 'offline');
    }

    public function safeDown()
    {
        $this->dropColumn($this->tableNameOrderGroup, 'offline');
        $this->dropColumn($this->tableNameOrderInfo, 'offline');
        return true;
    }

}
