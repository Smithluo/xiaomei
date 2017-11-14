<?php

use yii\db\Migration;

/**
 * 修改 event 支持 满赠、物料一对多后，废弃rule_id字段php
 * Class m170815_064929_modify_event_ruleId_notNull
 */
class m170815_064929_modify_event_ruleId_notNull extends Migration
{
    public function safeUp()
    {
        $this->alterColumn('o_event', 'rule_id', " INT(11) NULL DEFAULT '0' COMMENT '策略ID' ");
    }

    public function safeDown()
    {
        $this->alterColumn('o_event', 'rule_id', " INT(11) NOT NULL COMMENT '策略ID' ");
    }

}
