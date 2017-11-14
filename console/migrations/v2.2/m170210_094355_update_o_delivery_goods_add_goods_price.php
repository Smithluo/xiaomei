<?php

use yii\db\Migration;

class m170210_094355_update_o_delivery_goods_add_goods_price extends Migration
{
    private $tableName = 'o_delivery_goods';

    public function up()
    {
        $this->addColumn($this->tableName, 'goods_price', 'DECIMAL(10,2) UNSIGNED NOT NULL DEFAULT 0');
    }

    public function down()
    {
        $this->dropColumn($this->tableName, 'goods_price');
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
