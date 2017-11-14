<?php

use yii\db\Migration;

/**
 * Handles the creation for table `o_event_to_goods`.
 */
class m160823_033929_create_o_event_to_goods extends Migration
{
    public $table_name = 'o_event_to_goods';
    /**
     * @inheritdoc
     */
    public function safeUp()
    {

        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable($this->table_name, [
            'id' => $this->primaryKey()->comment('活动与商品的关联关系'),
            'event_id' => $this->integer()->notNull()->comment('活动ID'),
            'goods_id' => $this->integer()->notNull()->comment('商品ID'),
        ], $tableOptions);

        $this->createIndex('event_id', $this->table_name, 'event_id', false);
        $this->createIndex('goods_id', $this->table_name, 'goods_id', false);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('o_event_to_goods');
    }
}
