<?php

use yii\db\Migration;

/**
 * 运营不能在上线前提供所有商品的SPU数据，兼容没有SPU、size的商品
 * Class m170815_081028_modify_spu_null
 */
class m170815_081028_modify_spu_null extends Migration
{
    public function safeUp()
    {
        $this->alterColumn('o_goods', 'spu_id', " INT UNSIGNED NULL DEFAULT '0' COMMENT 'SPU_ID' ");
        $this->alterColumn('o_goods', 'sku_size', " VARCHAR(255) NULL DEFAULT '' COMMENT '规格' ");
    }

    public function safeDown()
    {
        $this->alterColumn('o_goods', 'spu_id', " INT UNSIGNED NOT NULL DEFAULT '0' COMMENT 'SPU_ID' ");
        $this->alterColumn('o_goods', 'sku_size', " VARCHAR(255) NOT NULL DEFAULT '' COMMENT '规格' ");
    }

}
