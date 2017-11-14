<?php

use yii\db\Migration;

class m161026_072056_goods_activity_set_goods_list_null extends Migration
{
    public function up()
    {
        $this->alterColumn('o_goods_activity', 'goods_list', " VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '团拼的商品列表 图片路径' ");
    }

    public function down()
    {
        $this->alterColumn('o_goods_activity', 'goods_list', " VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '团拼的商品列表 图片路径' ");
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
