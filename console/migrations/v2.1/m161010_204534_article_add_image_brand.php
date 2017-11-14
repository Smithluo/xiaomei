<?php

use yii\db\Migration;

class m161010_204534_article_add_image_brand extends Migration
{
    public function up()
    {
        $this->addColumn('o_article', 'pic', "VARCHAR(255) NULL COMMENT '文章列表展示图' AFTER `title`");
        $this->addColumn('o_article', 'brand_id', "SMALLINT UNSIGNED NULL COMMENT '关联品牌' AFTER `pic`");
    }

    public function down()
    {
        $this->dropColumn('o_article', 'pic');
        $this->dropColumn('o_article', 'brand_id');
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
