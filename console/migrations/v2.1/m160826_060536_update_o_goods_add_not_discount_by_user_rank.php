<?php

use yii\db\Migration;

class m160826_060536_update_o_goods_add_not_discount_by_user_rank extends Migration
{
    public function up()
    {
        $this->addColumn('o_goods', 'discount_disable', 'TINYINT(1) UNSIGNED NOT NULL DEFAULT "0" COMMENT "不享受全局会员折扣"');
    }

    public function down()
    {
        echo "m160826_060536_update_o_goods_add_not_discount_by_user_rank cannot be reverted.\n";

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
