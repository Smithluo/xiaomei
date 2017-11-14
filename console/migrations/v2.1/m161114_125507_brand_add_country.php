<?php

use yii\db\Migration;

class m161114_125507_brand_add_country extends Migration
{
    public function up()
    {
        $this->addColumn('o_brand', 'country', 'VARCHAR(10) COMMENT "国家"');
    }

    public function down()
    {
        $this->dropColumn('o_brand', 'country');

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
