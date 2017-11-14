<?php

use yii\db\Migration;

class m170314_085852_udpate_o_wechat_pay_info_add_group_id extends Migration
{
    private $wechatPayInfoTableName = 'o_wechat_pay_info';
    private $alipayInfoTableName = 'o_alipay_info';
    private $yeepayInfoTableName = 'o_yee_payinfo';
    private $yinlianInfoTableName = 'o_yinlian_payinfo';

    public function safeUp()
    {
        $this->addColumn($this->wechatPayInfoTableName, 'group_id', 'VARCHAR(22) NOT NULL DEFAULT "" COMMENT "总单ID"');
        $this->addColumn($this->alipayInfoTableName, 'group_id', 'VARCHAR(22) NOT NULL DEFAULT "" COMMENT "总单ID"');
        $this->addColumn($this->yeepayInfoTableName, 'group_id', 'VARCHAR(22) NOT NULL DEFAULT "" COMMENT "总单ID"');
        $this->addColumn($this->yinlianInfoTableName, 'group_id', 'VARCHAR(22) NOT NULL DEFAULT "" COMMENT "总单ID"');

        $this->createIndex('group_id', $this->wechatPayInfoTableName, 'group_id');
        $this->createIndex('group_id', $this->alipayInfoTableName, 'group_id');
        $this->createIndex('group_id', $this->yeepayInfoTableName, 'group_id');
        $this->createIndex('group_id', $this->yinlianInfoTableName, 'group_id');
    }

    public function safeDown()
    {
        $this->dropColumn($this->wechatPayInfoTableName, 'group_id');
        $this->dropColumn($this->alipayInfoTableName, 'group_id');
        $this->dropColumn($this->yeepayInfoTableName, 'group_id');
        $this->dropColumn($this->yinlianInfoTableName, 'group_id');
        return true;
    }

}
