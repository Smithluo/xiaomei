<?php

use yii\db\Migration;

class m170808_080610_update_o_gift_pkg_add_sort_order extends Migration
{
    private $tableName = 'o_gift_pkg';

    public function safeUp()
    {
        $this->addColumn($this->tableName, 'sort_order', 'SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0 COMMENT "排序值"');
        $this->createIndex('sort_order', $this->tableName, 'sort_order');
    }

    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'sort_order');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170808_080610_update_o_gift_pkg_add_sort_order cannot be reverted.\n";

        return false;
    }
    */
}
