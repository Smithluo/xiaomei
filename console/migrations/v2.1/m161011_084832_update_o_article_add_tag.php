<?php

use yii\db\Migration;

class m161011_084832_update_o_article_add_tag extends Migration
{
    public function safeUp()
    {
        $this->addColumn('o_article', 'tag', 'VARCHAR(6) NOT NULL DEFAULT "" COMMENT "文章标签(显示在首页的文章的中括号内)"');
        $this->addColumn('o_touch_article', 'tag', 'VARCHAR(6) NOT NULL DEFAULT "" COMMENT "文章标签(显示在首页的文章的中括号内)"');
    }

    public function safeDown()
    {
        $this->dropColumn('o_article', 'tag');
        $this->dropColumn('o_touch_article', 'tag');
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
