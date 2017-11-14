<?php

use yii\db\Migration;

class m160723_005834_update_o_order_info_add_supplier_id extends Migration
{
    public function up()
    {
            $this->addColumn('o_order_info', 'supplier_user_id', 'INT(11) UNSIGNED NOT NULL DEFAULT "0" COMMENT "供应商用户id" AFTER `brand_id`');
    }

    public function down()
    {
        echo "m160723_005834_update_o_order_info_add_supplier_id cannot be reverted.\n";

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
