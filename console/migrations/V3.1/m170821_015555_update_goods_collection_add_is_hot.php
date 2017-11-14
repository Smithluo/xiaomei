<?php

use yii\db\Migration;

class m170821_015555_update_goods_collection_add_is_hot extends Migration
{
    public function safeUp()
    {
        $this->addColumn('o_goods_collection', 'is_hot', 'TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT "是否出现在聚合页"');
        $this->createIndex('is_hot', 'o_goods_collection', 'is_hot');

        $this->createIndex('coll_goods', 'o_goods_collection_item', [
            'coll_id',
            'goods_id',
        ], true);
    }

    public function safeDown()
    {
        $this->dropColumn('o_goods_collection', 'is_hot');
//        $this->dropIndex('coll_goods', 'o_goods_collection_item');
        return true;
    }
}
