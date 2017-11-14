<?php

use yii\db\Migration;

class m170803_061828_update_goods_add_sale_count extends Migration
{
    public function safeUp()
    {
        $this->addColumn('o_goods', 'sale_count', 'INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT "销量"');
        $this->createIndex('sale_count', 'o_goods', 'sale_count');
    }

    public function safeDown()
    {
        $this->dropColumn('o_goods', 'sale_count');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170803_061828_update_goods_add_sale_count cannot be reverted.\n";

        return false;
    }
    */
}
