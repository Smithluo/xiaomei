<?php

use yii\db\Migration;
use common\models\Event;
use common\models\OrderGoods;

/**
 * 订单中的商品填写参与活动的  活动ID
 * Class m170106_071500_order_goods_add_event_id
 */
class m170106_071500_order_goods_add_event_id extends Migration
{
    public function safeUp()
    {
        $this->addColumn(OrderGoods::tableName(), 'event_id', " INT NULL DEFAULT '0' COMMENT '结算时参与的活动' ");
        $this->createIndex('event_id', Event::tableName(), 'event_id');
    }

    public function safeDown()
    {
        $this->dropIndex('event_id', Event::tableName());
        $this->dropColumn(OrderGoods::tableName(), 'event_id');
    }


}
