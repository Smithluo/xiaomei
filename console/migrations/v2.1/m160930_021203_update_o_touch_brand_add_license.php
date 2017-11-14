<?php

use yii\db\Migration;

class m160930_021203_update_o_touch_brand_add_license extends Migration
{
    public function safeUp()
    {
        $this->addColumn('o_touch_brand', 'license', 'TEXT NOT NULL COMMENT "品牌授权"');
    }

    public function safeDown()
    {
        $this->dropColumn('o_touch_brand', 'license');
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
