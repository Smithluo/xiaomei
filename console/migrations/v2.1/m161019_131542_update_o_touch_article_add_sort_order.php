<?php

use yii\db\Migration;

class m161019_131542_update_o_touch_article_add_sort_order extends Migration
{
    public function up()
    {
        $this->addColumn('o_touch_article', 'sort_order', 'SMALLINT(4) UNSIGNED NOT NULL DEFAULT 0 COMMENT "排序值"');
    }

    public function down()
    {
        $this->dropColumn('o_touch_article', 'sort_order');
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
