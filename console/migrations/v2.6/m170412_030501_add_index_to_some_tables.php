<?php

use yii\db\Migration;

class m170412_030501_add_index_to_some_tables extends Migration
{
    private $indexGroupBuy = 'o_index_group_buy';

    public function up()
    {
        $this->createIndex('activity_id', $this->indexGroupBuy, 'activity_id');
        $this->createIndex('sort_order', 'o_guide_goods', 'sort_order');
        $this->createIndex('type', 'o_guide_goods', 'type');
        $this->createIndex('goods_id', 'o_guide_goods', 'goods_id');
    }

    public function down()
    {
        $this->dropIndex('activity_id', $this->indexGroupBuy);
        $this->dropIndex('sort_order', 'o_guide_goods');
        $this->dropIndex('type', 'o_guide_goods');
        $this->dropIndex('goods_id', 'o_guide_goods');
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
