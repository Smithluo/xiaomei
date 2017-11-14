<?php

use yii\db\Migration;

/**
 * Handles the creation for table `o_index_spec_config`.
 */
class m161008_124046_create_o_index_spec_config extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable('o_index_spec_config', [
            'id' => $this->primaryKey(),
            'goods_id' => $this->integer(11)->unsigned()->notNull()->defaultValue(0)->comment('商品ID'),
            'sort_order' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue(0)->comment('排序值'),
        ], $tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('o_index_spec_config');
    }
}
