<?php

use yii\db\Migration;

class m160628_111707_alter_o_brand_admin_back_address extends Migration
{
    public function up()
    {
        $this->alterColumn('o_brand_admin', 'back_address', "VARCHAR(255) NULL DEFAULT '' COMMENT '退货地址'");
    }

    public function down()
    {
        echo "m160628_111707_alter_o_brand_admin_back_address cannot be reverted.\n";

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
