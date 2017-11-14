<?php

use yii\db\Migration;

class m170109_025130_update_o_index_full_cut_goods_add_title_sub_title extends Migration
{
    public function safeUp()
    {
        $this->addColumn('o_index_full_cut_goods', 'title', 'VARCHAR(20) COMMENT "标题"');
        $this->addColumn('o_index_full_cut_goods', 'sub_title', 'VARCHAR(28) COMMENT "副标题"');
        $this->createIndex('sort_order', 'o_index_full_cut_goods', 'sort_order');
    }

    public function safeDown()
    {
        $this->dropIndex('sort_order', 'o_index_full_cut_goods');
        $this->dropColumn('o_index_full_cut_goods', 'title');
        $this->dropColumn('o_index_full_cut_goods', 'sub_title');
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
