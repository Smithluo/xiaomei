<?php

use yii\db\Migration;

/**
 * Handles the creation for table `o_goods_tag`.
 */
class m160824_101255_create_o_goods_tag extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable('o_goods_tag', [
            'goods_id' => $this->integer(11)->unsigned()->notNull()->defaultValue(0)->comment('商品id'),
            'tag_id' => $this->integer(11)->unsigned()->notNull()->defaultValue(0)->comment('标签id'),
        ], $tableOptions);

        $this->createIndex('goods_id', 'o_goods_tag', 'goods_id');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('o_goods_tag');
    }
}
