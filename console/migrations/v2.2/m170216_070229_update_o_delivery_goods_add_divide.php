<?php

use yii\db\Migration;

class m170216_070229_update_o_delivery_goods_add_divide extends Migration
{
    private $tableName = 'o_delivery_goods';

    public function safeUp()
    {
        $this->addColumn($this->tableName, 'divide', 'DECIMAL(10,2) NOT NULL DEFAULT 0 COMMENT "业务员分成金额"');
        $this->addColumn($this->tableName, 'parent_divide', 'DECIMAL(10,2) NOT NULL DEFAULT 0 COMMENT "服务商分成金额"');
    }

    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'divide');
        $this->dropColumn($this->tableName, 'parent_divide');
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
