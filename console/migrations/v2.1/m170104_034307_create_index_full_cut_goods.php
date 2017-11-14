<?php

use yii\db\Migration;

class m170104_034307_create_index_full_cut_goods extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable('o_index_full_cut_goods', [
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
        $this->dropTable('o_index_full_cut_goods');
        return true;
    }
}
