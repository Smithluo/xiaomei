<?php

use yii\db\Migration;

class m161107_130501_update_o_index_star_goods_tab_conf_add_url extends Migration
{
    public function safeUp()
    {
        $this->addColumn('o_index_spec_config', 'tip', 'VARCHAR(8) COMMENT "顶部tip"');
        $this->addColumn('o_index_spec_config', 'title', 'VARCHAR(20) COMMENT "标题"');
        $this->addColumn('o_index_spec_config', 'sub_title', 'VARCHAR(20) COMMENT "副标题"');
        $this->addColumn('o_index_star_goods_tab_conf', 'pc_url', 'VARCHAR(255) COMMENT "点击更多时的跳转链接"');
        $this->addColumn('o_index_star_goods_tab_conf', 'm_url', 'VARCHAR(255) COMMENT "点击更多时的跳转链接"');
        $this->addColumn('o_index_star_goods_tab_conf', 'image', 'VARCHAR(255) COMMENT "广告图片"');

        $this->createIndex('sort_order', 'o_index_spec_config', 'sort_order');
        $this->createIndex('sort_order', 'o_index_star_goods_conf', 'sort_order');
        $this->createIndex('sort_order', 'o_index_star_goods_tab_conf', 'sort_order');
    }

    public function safeDown()
    {
        $this->dropIndex('sort_order', 'o_index_spec_config');
        $this->dropIndex('sort_order', 'o_index_star_goods_conf');
        $this->dropIndex('sort_order', 'o_index_star_goods_tab_conf');

        $this->dropColumn('o_index_spec_config', 'tip');
        $this->dropColumn('o_index_spec_config', 'title');
        $this->dropColumn('o_index_spec_config', 'sub_title');
        $this->dropColumn('o_index_star_goods_tab_conf', 'pc_url');
        $this->dropColumn('o_index_star_goods_tab_conf', 'm_url');
        $this->dropColumn('o_index_star_goods_tab_conf', 'image');
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
