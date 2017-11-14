<?php

use yii\db\Migration;

class m170227_074219_update_o_goods_action_add_goods_number extends Migration
{

    private $tableName = 'o_goods_action';

    public function safeUp()
    {
        $this->addColumn($this->tableName, 'goods_number', 'SMALLINT(5) UNSIGNED COMMENT "商品库存"');
    }

    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'goods_number');
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
