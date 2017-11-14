<?php

use yii\db\Migration;
use common\models\Event;

class m161228_123030_alter_event_for_full_cut extends Migration
{
    public $tb;

    public function init()
    {
        parent::init();
        $this->tb = Event::tableName();
    }
    /*public function up()
    {

    }

    public function down()
    {
        echo "m161228_123030_alter_event_for_full_cut cannot be reverted.\n";

        return false;
    }*/


    // 活动表添加 banner、url、bgcolor 字段，允许为空，兼容 团采、满赠活动 修改pkg_id字段允许为空，逐步废弃商品包的概念
    public function safeUp()
    {
        $this->alterColumn($this->tb, 'pkg_id', " INT(11) NULL COMMENT '商品包ID' ");

        $this->addColumn($this->tb, 'event_type', " int(3) NOT NULL DEFAULT 1 COMMENT '活动类型' AFTER event_id ");
        $this->addColumn($this->tb, 'banner', " VARCHAR(255) NULL DEFAULT '' COMMENT '活动Banner图' ");
        $this->addColumn($this->tb, 'url', " VARCHAR(255) NULL DEFAULT '' COMMENT '活动页面URL' ");
        $this->addColumn($this->tb, 'bgcolor', " VARCHAR(10) NULL DEFAULT ''  COMMENT '活动页背景色' ");

        $this->createIndex('event_type', $this->tb, 'event_type');
    }

    public function safeDown()
    {
        $this->alterColumn($this->tb, 'pkg_id', " INT(11) NOT NULL COMMENT '商品包ID' ");

        $this->dropColumn($this->tb, 'event_type');
        $this->dropColumn($this->tb, 'banner');
        $this->dropColumn($this->tb, 'url');
        $this->dropColumn($this->tb, 'bgcolor');
    }

}
