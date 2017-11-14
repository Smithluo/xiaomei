<?php

use yii\db\Migration;

class m161009_132240_update_o_order_info_add_uniq_id extends Migration
{
    public function up()
    {
        $this->addColumn('o_order_info', 'group_id', 'VARCHAR(13) NOT NULL DEFAULT "" COMMENT "分组ID(多订单合并支付时使用)"');
    }

    public function down()
    {
        $this->dropColumn('o_order_info', 'group_id');
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
