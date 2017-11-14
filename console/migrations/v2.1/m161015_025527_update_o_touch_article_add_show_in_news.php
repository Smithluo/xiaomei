<?php

use yii\db\Migration;

class m161015_025527_update_o_touch_article_add_show_in_news extends Migration
{
    public function up()
    {
        $this->addColumn('o_touch_article_cat', 'show_in_news', "TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否在资讯模块展示'");
        $this->addColumn('o_touch_article', 'type', 'SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0 COMMENT "类型(1为广告，其它是普通资讯)"');
    }

    public function down()
    {
        $this->dropColumn('o_touch_article_cat', 'show_in_news');
        $this->dropColumn('o_touch_article', 'type');
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
