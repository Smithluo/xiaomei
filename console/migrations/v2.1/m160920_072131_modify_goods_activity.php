<?php

use yii\db\Migration;

class m160920_072131_modify_goods_activity extends Migration
{
    public function safeUp()
    {
        $this->addColumn('o_goods_activity', 'start_num', " INT UNSIGNED NOT NULL DEFAULT '1' COMMENT '起售数量' AFTER `goods_id` ");
        $this->addColumn('o_goods_activity', 'match_num', " INT UNSIGNED NOT NULL DEFAULT '100' COMMENT '成团数量' AFTER `limit_num` ");
        $this->addColumn('o_goods_activity', 'old_price', " decimal(10,2) NOT NULL DEFAULT '1.00' COMMENT '原价' AFTER `match_num` ");
        $this->addColumn('o_goods_activity', 'act_price', " decimal(10,2) NOT NULL DEFAULT '1.00' COMMENT '团采价' AFTER `old_price` ");
        $this->addColumn('o_goods_activity', 'production_date', " DATETIME NOT NULL DEFAULT '2016-01-01 00:00:00' COMMENT '商品有效期' AFTER `act_price` ");
        $this->addColumn('o_goods_activity', 'show_banner', " VARCHAR(255) NOT NULL COMMENT '展示图' AFTER `production_date` ");
        $this->addColumn('o_goods_activity', 'qr_code', " VARCHAR(255) NULL COMMENT '二维码' AFTER `show_banner` ");
        $this->alterColumn('o_goods_activity', 'limit_num', " INT UNSIGNED NOT NULL DEFAULT '1000' COMMENT '活动期间限购数量，默认1000' ");
    }

    public function safeDown()
    {
        $this->dropColumn('o_goods_activity', 'start_num');
        $this->dropColumn('o_goods_activity', 'match_num');
        $this->dropColumn('o_goods_activity', 'old_price');
        $this->dropColumn('o_goods_activity', 'act_price');
        $this->dropColumn('o_goods_activity', 'production_date');
        $this->dropColumn('o_goods_activity', 'show_banner');
        $this->dropColumn('o_goods_activity', 'qr_code');
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

