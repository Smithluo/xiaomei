<?php

use yii\db\Migration;

class m160615_130652_alter_touch_brand extends Migration
{
    public function up()
    {
        $this->addPrimaryKey('brand_id', 'o_touch_brand', 'brand_id');
    }

    public function down()
    {
        echo "m160615_130652_alter_touch_brand cannot be reverted.\n";

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
