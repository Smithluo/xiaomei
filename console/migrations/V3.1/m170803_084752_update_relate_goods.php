<?php

use yii\db\Migration;

/**
 * Handles the creation of table `o_spu`.
 *
 * 创建SPU表
 */
class m170803_084752_update_relate_goods extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM';

        $this->createTable(
            'o_spu',
            [
                'id' => $this->primaryKey(),
                'name' => $this->string('255')->notNull()->unique()->comment('SPU名称'),
            ],
            $tableOptions
        );

        $this->addColumn('o_goods', 'spu_id', " INT UNSIGNED NOT NULL DEFAULT '0' COMMENT 'SPU_ID' ");
        $this->addColumn('o_goods', 'sku_size', " VARCHAR(255) NOT NULL DEFAULT '' COMMENT '规格' ");

        $this->createIndex('spu_id', 'o_goods', 'spu_id');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('o_spu');
        $this->dropColumn('o_goods', 'spu_id');
        $this->dropColumn('o_goods', 'sku_size');
    }
}
