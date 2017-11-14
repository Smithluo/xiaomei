<?php

use yii\db\Migration;

class m170724_062044_modify_article_desc extends Migration
{
    public function safeUp()
    {
        $this->alterColumn('o_article', 'description', " TEXT NULL DEFAULT NULL ");
        $this->alterColumn('o_touch_article', 'description', " TEXT NULL DEFAULT NULL ");
    }

    public function safeDown()
    {
        $this->alterColumn('o_article', 'description', " VARCHAR(255) NULL DEFAULT NULL ");
        $this->alterColumn('o_touch_article', 'description', " VARCHAR(255) NULL DEFAULT NULL ");
    }
}
