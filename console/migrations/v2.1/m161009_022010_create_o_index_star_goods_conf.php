<?php

use yii\db\Migration;

/**
 * Handles the creation for table `o_index_star_goods_conf`.
 */
class m161009_022010_create_o_index_star_goods_conf extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable('o_index_star_goods_conf', [
            'id' => $this->primaryKey(),
            'goods_id' => $this->integer(10)->notNull()->defaultValue(0)->comment('商品'),
            'tab_id' => $this->integer(10)->notNull()->defaultValue(0)->comment('标签'),
            'sort_order' => $this->smallInteger(5)->notNull()->defaultValue(0)->comment('排序值'),
        ], $tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('o_index_star_goods_conf');
    }
}
