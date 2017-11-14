<?php

use yii\db\Migration;

class m160712_081144_update_o_user_rank_add_price_permission extends Migration
{
    public function up()
    {
        $this->addColumn('o_user_rank', 'hide_price_num', 'TINYINT UNSIGNED NOT NULL DEFAULT "0" COMMENT "隐藏价格数" AFTER `special_rank`');
        $this->addColumn('o_user_rank', 'shipping_fee_level', 'TINYINT UNSIGNED NOT NULL DEFAULT "0" COMMENT "运费级别" AFTER `hide_price_num`');
    }

    public function down()
    {
        echo "m160712_081144_update_o_user_rank_add_price_permission cannot be reverted.\n";
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
