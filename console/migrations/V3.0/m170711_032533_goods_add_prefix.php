<?php

use yii\db\Migration;

/**
 * Class m170711_032533_goods_add_prefix
 * 商品添加条码前缀，用于区分 普通商品、直发、物料等
 */
class m170711_032533_goods_add_prefix extends Migration
{
    public function safeUp()
    {
        $this->addColumn('o_goods', 'prefix', " VARCHAR(4) NULL DEFAULT 'NO' COMMENT '条码前缀' AFTER `expire_date` ");
    }

    public function safeDown()
    {
        $this->dropColumn('o_goods', 'prefix');
    }
}
