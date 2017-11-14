<?php

use yii\db\Migration;

class m170628_031126_update_order_goods_add_sample extends Migration
{
    private $tableName = 'o_order_goods';

    public function safeUp()
    {
        $this->addColumn($this->tableName, 'sample', 'VARCHAR(64) NOT NULL DEFAULT "" COMMENT "小样配比"');
    }

    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'sample');
        return true;
    }

}
