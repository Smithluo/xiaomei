<?php

use yii\db\Migration;
use common\models\Event;
use common\models\Tags;

class m170302_071259_add_coupon_record extends Migration
{
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable(
            'o_coupon_record',
            [
                'coupon_id' => $this->primaryKey()->comment('优惠券流水号'),
                'event_id' => $this->integer()->notNull()->comment('优惠券活动ID'),
                'rule_id' => $this->integer()->notNull()->comment('优惠券规则ID'),
                'coupon_sn' => $this->string(10)->notNull()->comment('优惠券编号'),
                'user_id' => $this->integer()->notNull()->defaultValue(0)->comment('拥有者'),

                //  待用户领券则为0，自动发券则用生成时间
                'received_at' => $this->integer(10)->notNull()->defaultValue(0)->comment('获取时间'),
                'used_at' => $this->integer(10)->notNull()->defaultValue(0)->comment('使用时间'),
                'group_id' => $this->string(22)->notNull()->defaultValue(0)->comment('总单号'),
                'created_by' => $this->integer(10)->notNull()->defaultValue(0)->comment('创建人'),
            ],
            $tableOptions
        );

        //  0未使用 1已使用 2 已过期
        $this->addColumn('o_coupon_record', 'status', " TINYINT(3) NOT NULL DEFAULT '0' COMMENT '状态' ");

        $this->createIndex('coupon_sn', 'o_coupon_record', 'coupon_sn', true);
        $this->createIndex('event_id', 'o_coupon_record', 'event_id');
        $this->createIndex('rule_id', 'o_coupon_record', 'rule_id');
        $this->createIndex('user_id', 'o_coupon_record', 'user_id');
        $this->createIndex('group_id', 'o_coupon_record', 'group_id');
        $this->createIndex('created_by', 'o_coupon_record', 'created_by');

        $this->addColumn(Event::tableName(), 'times_limit', " INT NOT NULL DEFAULT '0' COMMENT '参与次数上限' ");
        $this->addColumn(Event::tableName(), 'pre_time', " DATETIME NOT NULL DEFAULT '2017-03-01 00:00:00' COMMENT '预热开始时间' ");
        $this->addColumn(Event::tableName(), 'sub_type', " VARCHAR(20) NULL DEFAULT '' COMMENT '子类别' ");

        //  修改活动的起止日期为datetime
        $this->dropColumn(Event::tableName(), 'start_time');
        $this->dropColumn(Event::tableName(), 'end_time');
        $this->addColumn(Event::tableName(), 'start_time', " DATETIME NOT NULL DEFAULT '2016-08-01 00:00:00' COMMENT '开始时间' ");
        $this->addColumn(Event::tableName(), 'end_time', " DATETIME NOT NULL DEFAULT '2026-01-01 23:25:59' COMMENT '结束时间' ");
        $this->update(Event::tableName(), ['start_time' => '2016-08-01 00:00:00', 'end_time' => '2026-01-01 23:25:59']);

            //  优惠券的标签不显示
//        $this->insert(Tags::tableName(), [
//            'id' => 8,
//            'type' => 1,
//            'name' => '优惠券',
//            'desc' => '优惠券',
//            'sort' => '29000',
//            'enabled' => 1,
//            'code' => '<i class="coupon">券</i>',
//            'mCode' => '<i class="coupon">券</i>',
//        ]);
    }

    public function safeDown()
    {
        $this->dropTable('o_coupon_record');

        $this->dropColumn(Event::tableName(), 'times_limit');
        $this->dropColumn(Event::tableName(), 'pre_time');
        $this->dropColumn(Event::tableName(), 'sub_type');

        $this->delete(Tags::tableName(), ['id' => 8]);
        $this->execute('ALTER TABLE '.Tags::tableName().' auto_increment = 8');
    }

}
