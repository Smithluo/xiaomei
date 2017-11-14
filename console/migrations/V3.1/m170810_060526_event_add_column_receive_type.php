<?php

use yii\db\Migration;

/**
 * 优惠券活动 区分 领券的方式，满减活动 不支持修改领券方式，默认0 被动生效
 * Class m170810_060526_event_add_column_receive_type
 */
class m170810_060526_event_add_column_receive_type extends Migration
{
    private $tbName = 'o_event';

    public function safeUp()
    {
        $this->addColumn($this->tbName, 'receive_type', "  TINYINT NOT NULL DEFAULT '0' COMMENT '获取方式' ");
    }

    public function safeDown()
    {
        $this->dropColumn($this->tbName, 'receive_type');
    }

}
