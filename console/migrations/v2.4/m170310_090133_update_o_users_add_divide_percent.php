<?php

use yii\db\Migration;

class m170310_090133_update_o_users_add_divide_percent extends Migration
{
    public function safeUp()
    {
        $this->addColumn('o_users', 'divide_percent', 'TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT "下属业务员分成比例"');
        $this->addColumn('o_delivery_order', 'group_id', 'VARCHAR(22) NOT NULL DEFAULT "" COMMENT "总单序列号"');
        $this->createIndex('group_id', 'o_delivery_order', 'group_id');
    }

    public function safeDown()
    {
        $this->dropColumn('o_users', 'divide_percent');
        $this->dropColumn('o_delivery_order', 'group_id');
        return true;
    }

}
