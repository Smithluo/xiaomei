<?php

use yii\db\Migration;

class m161011_151707_brand_add_up_time extends Migration
{
    public function up()
    {
        $this->addColumn('o_brand', 'turn_show_time', " DATETIME NOT NULL COMMENT '上架时间' AFTER `discount`");
    }

    public function down()
    {
        $this->dropColumn('o_brand', 'turn_show_time');
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
