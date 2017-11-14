<?php

use yii\db\Migration;

class m170824_131358_update_goods_add_base_sale_count extends Migration
{
    public function safeUp()
    {
        $this->addColumn('o_goods', 'base_sale_count', 'INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT "基础销量"');
        $this->createIndex('base_sale_count', 'o_goods', 'base_sale_count');
    }

    public function safeDown()
    {
        $this->dropColumn('o_goods', 'base_sale_count');
        return true;
    }
}
