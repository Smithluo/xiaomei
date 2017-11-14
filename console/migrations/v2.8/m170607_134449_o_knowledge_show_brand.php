<?php

use yii\db\Migration;

class m170607_134449_o_knowledge_show_brand extends Migration
{
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';

        $this->createTable(
            'o_knowledge_show_brand',
            [
                'id' => $this->primaryKey()->comment('ID'),
                'brand_id' => $this->integer()->notNull()->comment('推荐品牌'),
                'sort_order' => $this->integer(5)->notNull()->defaultValue(30000)->comment('排序值'),
            ],
            $tableOptions
        );

        $this->createIndex('brand_id', 'o_knowledge_show_brand', 'brand_id', true);
        $this->createIndex('sort_order', 'o_knowledge_show_brand', 'sort_order');
    }

    public function safeDown()
    {
        $this->dropTable('o_knowledge_show_brand');
    }

}
