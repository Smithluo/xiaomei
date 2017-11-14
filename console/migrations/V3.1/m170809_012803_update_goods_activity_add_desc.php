<?php

use yii\db\Migration;

class m170809_012803_update_goods_activity_add_desc extends Migration
{
    private $tableName = 'o_goods_activity';

    public function safeUp()
    {
        $this->addColumn($this->tableName, 'desc', 'VARCHAR(50) NOT NULL DEFAULT "" COMMENT "简短描述，显示在首页活动区"');
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
        echo "m170809_012803_update_goods_activity_add_desc cannot be reverted.\n";

        return false;
    }
    */
}
