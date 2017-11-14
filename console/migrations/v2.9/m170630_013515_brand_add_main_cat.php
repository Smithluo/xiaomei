<?php

use yii\db\Migration;

class m170630_013515_brand_add_main_cat extends Migration
{
    public function up()
    {
        $this->addColumn('o_brand', 'main_cat', " VARCHAR(40) NOT NULL COMMENT '主营品类' ");
    }

    public function down()
    {
        $this->dropColumn('o_brand', 'main_cat');
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
