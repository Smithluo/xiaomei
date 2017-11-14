<?php

use yii\db\Migration;

class m170310_073859_update_o_order_group_add_event_id_and_rule_id extends Migration
{
    private $orderGroupTableName = 'o_order_group';
    private $couponTableName = 'o_coupon_record';

    public function safeUp()
    {
        $this->addColumn($this->orderGroupTableName, 'event_id', 'INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT "参与活动的ID"');
        $this->addColumn($this->orderGroupTableName, 'rule_id', 'INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT "参与活动的规则ID"');

        $this->createIndex('event_id', $this->orderGroupTableName, 'event_id');
        $this->createIndex('rule_id', $this->orderGroupTableName, 'rule_id');
    }

    public function safeDown()
    {
        $this->dropColumn($this->orderGroupTableName, 'event_id');
        $this->dropColumn($this->orderGroupTableName, 'rule_id');
        $this->dropColumn($this->couponTableName, 'group_id');
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
