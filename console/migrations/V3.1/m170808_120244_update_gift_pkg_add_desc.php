<?php

use yii\db\Migration;

class m170808_120244_update_gift_pkg_add_desc extends Migration
{
    private $tableName = 'o_gift_pkg';
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'desc', 'VARCHAR(50) NOT NULL DEFAULT "" COMMENT "显示在首页活动区的短描述"');
    }

    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'desc');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170808_120244_update_gift_pkg_add_desc cannot be reverted.\n";

        return false;
    }
    */
}
