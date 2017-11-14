<?php

use yii\db\Migration;

class m160908_032133_update_o_brand_add_shipping_id extends Migration
{
    public function up()
    {
        $this->addColumn('o_brand', 'shipping_id', 'SMALLINT(5) NOT NULL DEFAULT 0 COMMENT "运费模版ID"');
    }

    public function down()
    {
        $this->dropColumn('o_brand', 'shipping_id');
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
