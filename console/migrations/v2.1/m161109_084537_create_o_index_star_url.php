<?php

use yii\db\Migration;

/**
 * Handles the creation for table `o_index_star_url`.
 */
class m161109_084537_create_o_index_star_url extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable('o_index_star_url', [
            'id' => $this->primaryKey(),
            'tab_id' => $this->smallInteger(5)->notNull()->defaultValue(0)->comment('楼层'),
            'title' => $this->string(10)->defaultValue('')->comment('标题'),
            'url' => $this->string(255)->defaultValue('')->comment('跳转链接'),
            'sort_order' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue(0)->comment('排序值'),
        ], $tableOptions);
        $this->createIndex('tab_id', 'o_index_star_url', 'tab_id');
        $this->createIndex('sort_order', 'o_index_star_url', 'sort_order');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropIndex('sort_order', 'o_index_star_url');
        $this->dropIndex('tab_id', 'o_index_star_url');
        $this->dropTable('o_index_star_url');
    }
}
