<?php

use yii\db\Migration;

/**
 * Handles the creation for table `o_index_star_goods_tab_conf`.
 */
class m161009_021959_create_o_index_star_goods_tab_conf extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable('o_index_star_goods_tab_conf', [
            'id' => $this->primaryKey(),
            'tab_name' => $this->string(10)->notNull()->defaultValue('')->comment('标签名称'),
            'sort_order' => $this->smallInteger(5)->notNull()->defaultValue(0)->comment('排序值'),
        ], $tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('o_index_star_goods_tab_conf');
    }
}
