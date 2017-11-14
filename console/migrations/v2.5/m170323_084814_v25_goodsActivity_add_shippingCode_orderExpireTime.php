<?php

use yii\db\Migration;

class m170323_084814_v25_goodsActivity_add_shippingCode_orderExpireTime extends Migration
{
    public $tbName = 'o_goods_activity';

    /**
     * 活动添加 配送方式 默认为到付
     * 活动添加 订单过期时间(下单后多少秒内未支付的订单将被取消)，默认为两天时间与普通商品一致
     */
    public function safeUp()
    {
        $this->addColumn($this->tbName, 'shipping_code', " VARCHAR(20) NOT NULL DEFAULT 'fpd' COMMENT '配送方式' ");
        //  修改历史数据的配送方式为到付
        $this->update($this->tbName, ['shipping_code' => 'fpd']);

        $this->addColumn($this->tbName, 'order_expired_time', "  INT NOT NULL DEFAULT '172800' COMMENT '订单有效期(s)' ");
        //  修改历史数据的订单过期是件为2天
        $this->update($this->tbName, ['order_expired_time' => 172800]);
        $this->update('o_shipping', ['shipping_name' => '小美直发(满额包邮)'], ['shipping_id' => 5]);
        $this->update('o_shipping', ['enabled' => 0], ['shipping_id' => 4]);
    }

    public function safeDown()
    {
        $this->dropColumn($this->tbName, 'shipping_code');
        $this->dropColumn($this->tbName, 'order_expired_time');

        $this->update('o_shipping', ['shipping_name' => '小美直发(运费已付)'], ['shipping_id' => 5]);
        $this->update('o_shipping', ['enabled' => 1], ['shipping_id' => 4]);
    }
}
