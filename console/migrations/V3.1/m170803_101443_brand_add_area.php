<?php

use yii\db\Migration;

class m170803_101443_brand_add_area extends Migration
{
    private $tableName = 'o_brand';

    public function safeUp()
    {
        $this->addColumn($this->tableName,'brand_area'," VARCHAR(50) NOT NULL DEFAULT '' COMMENT '品牌所属区域' ");
    }

    public function safeDown()
    {
        $this->dropColumn($this->tableName,'brand_area');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170803_101443_brand_add_area cannot be reverted.\n";

        return false;
    }
    */
}
