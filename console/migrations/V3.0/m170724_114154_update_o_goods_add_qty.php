<?php

use yii\db\Migration;

class m170724_114154_update_o_goods_add_qty extends Migration
{
    private $goodsTable = 'o_goods';

    public function safeUp()
    {
        $this->addColumn($this->goodsTable, 'qty', 'SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0 COMMENT "装箱规格"');
    }

    public function safeDown()
    {
        $this->dropColumn($this->goodsTable, 'qty');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170724_114154_update_o_goods_add_qty cannot be reverted.\n";

        return false;
    }
    */
}
