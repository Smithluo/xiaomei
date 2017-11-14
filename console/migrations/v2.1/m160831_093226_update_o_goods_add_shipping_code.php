<?php

use yii\db\Migration;

class m160831_093226_update_o_goods_add_shipping_code extends Migration
{
    public function up()
    {
        $this->addColumn('o_goods', 'shipping_code', 'VARCHAR(20) NOT NULL DEFAULT "" COMMENT "运费code"');
    }

    public function down()
    {
        echo "m160831_093226_update_o_goods_add_shipping_code cannot be reverted.\n";

        return false;
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
