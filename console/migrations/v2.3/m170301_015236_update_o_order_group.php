<?php

use yii\db\Migration;

class m170301_015236_update_o_order_group extends Migration
{
    private $tableOrderGroup = 'o_order_group';
    private $tableServicerDivideRecord = 'o_servicer_divide_record';

    public function safeUp()
    {
        $this->addColumn($this->tableOrderGroup, 'group_status', 'TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT "总单综合状态"');
        $this->addColumn($this->tableOrderGroup, 'consignee', 'VARCHAR(60) NOT NULL DEFAULT "" COMMENT "收货人"');
        $this->addColumn($this->tableOrderGroup, 'country', 'SMALLINT(5) NOT NULL DEFAULT 0 COMMENT "收货地址的国家"');
        $this->addColumn($this->tableOrderGroup, 'province', 'SMALLINT(5) NOT NULL DEFAULT 0 COMMENT "收货地址的省份"');
        $this->addColumn($this->tableOrderGroup, 'city', 'SMALLINT(5) NOT NULL DEFAULT 0 COMMENT "收货地址的城市"');
        $this->addColumn($this->tableOrderGroup, 'district', 'SMALLINT(5) NOT NULL DEFAULT 0 COMMENT "收货地址的区县"');
        $this->addColumn($this->tableOrderGroup, 'address', 'VARCHAR(255) NOT NULL DEFAULT "" COMMENT "收货地址的详细地址"');
        $this->addColumn($this->tableOrderGroup, 'mobile', 'VARCHAR(60) NOT NULL DEFAULT "" COMMENT "收货地址的手机号码"');
        $this->addColumn($this->tableOrderGroup, 'pay_id', 'TINYINT(3) NOT NULL DEFAULT 0 COMMENT "支付方式ID"');
        $this->addColumn($this->tableOrderGroup, 'pay_name', 'VARCHAR(120) NOT NULL DEFAULT "" COMMENT "支付方式名称"');
        $this->addColumn($this->tableOrderGroup, 'goods_amount', 'DECIMAL(10,2) NOT NULL DEFAULT 0 COMMENT "总货款"');
        $this->addColumn($this->tableOrderGroup, 'shipping_fee', 'DECIMAL(10,2) NOT NULL DEFAULT 0 COMMENT "总运费"');
        $this->addColumn($this->tableOrderGroup, 'money_paid', 'DECIMAL(10,2) NOT NULL DEFAULT 0 COMMENT "总已付款"');
        $this->addColumn($this->tableOrderGroup, 'order_amount', 'DECIMAL(10,2) NOT NULL DEFAULT 0 COMMENT "总待付款"');
        $this->addColumn($this->tableOrderGroup, 'pay_time', 'INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT "支付时间"');
        $this->addColumn($this->tableOrderGroup, 'shipping_time', 'INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT "发货处理完成的时间"');
        $this->addColumn($this->tableOrderGroup, 'recv_time', 'INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT "总单确认收货的时间"');
        $this->addColumn($this->tableOrderGroup, 'discount', 'DECIMAL(10,2) UNSIGNED NOT NULL DEFAULT 0 COMMENT "总单的总折扣"');

        $this->addColumn($this->tableServicerDivideRecord, 'group_id', 'VARCHAR(22) NOT NULL DEFAULT "" COMMENT "分成对应的总单ID"');

        $this->createIndex('country', $this->tableOrderGroup, 'country');
        $this->createIndex('province', $this->tableOrderGroup, 'province');
        $this->createIndex('city', $this->tableOrderGroup, 'city');
        $this->createIndex('district', $this->tableOrderGroup, 'district');
        $this->createIndex('group_status', $this->tableOrderGroup, 'group_status');

        $this->createIndex('group_id', $this->tableServicerDivideRecord, 'group_id');
    }

    public function safeDown()
    {
        $this->dropColumn($this->tableOrderGroup, 'group_status');
        $this->dropColumn($this->tableOrderGroup, 'consignee');
        $this->dropColumn($this->tableOrderGroup, 'country');
        $this->dropColumn($this->tableOrderGroup, 'province');
        $this->dropColumn($this->tableOrderGroup, 'city');
        $this->dropColumn($this->tableOrderGroup, 'district');
        $this->dropColumn($this->tableOrderGroup, 'address');
        $this->dropColumn($this->tableOrderGroup, 'mobile');
        $this->dropColumn($this->tableOrderGroup, 'pay_id');
        $this->dropColumn($this->tableOrderGroup, 'pay_name');
        $this->dropColumn($this->tableOrderGroup, 'goods_amount');
        $this->dropColumn($this->tableOrderGroup, 'shipping_fee');
        $this->dropColumn($this->tableOrderGroup, 'money_paid');
        $this->dropColumn($this->tableOrderGroup, 'order_amount');
        $this->dropColumn($this->tableOrderGroup, 'pay_time');
        $this->dropColumn($this->tableOrderGroup, 'shipping_time');
        $this->dropColumn($this->tableOrderGroup, 'recv_time');
        $this->dropColumn($this->tableOrderGroup, 'discount');

        $this->dropColumn($this->tableServicerDivideRecord, 'group_id');
        return true;
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
