<?php

use yii\db\Migration;

class m161008_005623_update_o_goods_add_buy_by_box extends Migration
{
    public function up()
    {
        $this->addColumn('o_goods', 'buy_by_box', 'TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT "是否按箱购买"');
    }

    public function down()
    {
        $this->dropColumn('o_goods', 'buy_by_box');
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
