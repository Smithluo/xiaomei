<?php

use yii\db\Migration;

class m161107_063511_o_index_star_brand_conf extends Migration
{
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable('o_index_star_brand_conf', [
            'id' => $this->primaryKey(),
            'brand_id' => $this->integer(10)->notNull()->defaultValue(0)->comment('品牌id'),
            'tab_id' => $this->integer(10)->notNull()->defaultValue(0)->comment('标签'),
            'sort_order' => $this->smallInteger(5)->notNull()->defaultValue(0)->comment('排序值'),
        ], $tableOptions);

        $this->createTable('o_index_hot_goods', [
            'id' => $this->primaryKey(),
            'goods_id' => $this->integer(11)->unsigned()->notNull()->defaultValue(0)->comment('商品ID'),
            'sort_order' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue(0)->comment('排序值'),
        ], $tableOptions);

        $this->createIndex('sort_order', 'o_index_star_brand_conf', 'sort_order');
        $this->createIndex('sort_order', 'o_index_hot_goods', 'sort_order');
        $this->createIndex('tab_id', 'o_index_star_brand_conf', 'tab_id');
    }

    public function safeDown()
    {
        $this->dropIndex('sort_order', 'o_index_star_brand_conf');
        $this->dropIndex('sort_order', 'o_index_hot_goods');
        $this->dropIndex('tab_id', 'o_index_star_brand_conf');
        $this->dropTable('o_index_star_brand_conf');
        $this->dropTable('o_index_hot_goods');
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
