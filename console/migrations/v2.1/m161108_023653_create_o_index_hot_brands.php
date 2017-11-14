<?php

use yii\db\Migration;

/**
 * Handles the creation for table `o_index_hot_brands`.
 */
class m161108_023653_create_o_index_hot_brands extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable('o_index_hot_brands', [
            'id' => $this->primaryKey(),
            'brand_id' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue(0)->comment('品牌'),
            'sort_order' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue(0)->comment('排序值'),
        ], $tableOptions);
        $this->createIndex('sort_order', 'o_index_hot_brands', 'sort_order');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropIndex('sort_order', 'o_index_hot_brands');
        $this->dropTable('o_index_hot_brands');
    }
}
