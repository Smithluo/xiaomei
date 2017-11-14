<?php

use yii\db\Migration;

class m160912_010358_udpate_o_brand_add_discount extends Migration
{
    public function up()
    {
        $this->addColumn('o_brand', 'discount', 'VARCHAR(4) NOT NULL DEFAULT "" COMMENT "品牌折扣(在品牌政策上显示)"');
    }

    public function down()
    {
        $this->dropColumn('o_brand', 'discount');
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
