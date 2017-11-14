<?php

use yii\db\Migration;

class m161205_062502_update_goods_extension_code extends Migration
{
    /**
     * 商品扩展字段能用于表示商品类型，修改已有商品的默认类型为general
     */
    public function safeUp()
    {
        $this->alterColumn(
            'o_goods',
            'extension_code',
            " VARCHAR(30) NOT NULL DEFAULT 'general' COMMENT '交易类型' ");
        $this->addColumn('o_goods', 'need_rank', " INT(1) NOT NULL DEFAULT '1' COMMENT '需要等级' AFTER `buy_by_box`");
        $this->createIndex('extension_code', 'o_goods', 'extension_code');
        $this->createIndex('need_rank', 'o_goods', 'need_rank');
    }

    public function safeDown()
    {
        $this->alterColumn(
            'o_goods',
            'extension_code',
            " VARCHAR(30) NOT NULL DEFAULT '' COMMENT '交易类型' ");
        $this->dropIndex('extension_code', 'o_goods');
    }

}
