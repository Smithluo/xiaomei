<?php

use yii\db\Migration;
use common\models\Brand;
use common\models\Event;
use common\models\Goods;
use common\models\FullCutRule;
use common\models\CouponRecord;

/**
 * Class m170504_073301_V27_event_coupon_extend
 *
 *
 */
class m170504_073301_V27_event_coupon_extend extends Migration
{
    public function safeUp()
    {
        //  活动表 添加 生效范围类型 字段，支持设置 全局券、品牌券、直发券、指定商品券
        $this->addColumn(
            Event::tableName(),
            'effective_scope_type',
            " VARCHAR(20) NOT NULL DEFAULT 'goods' COMMENT '生效范围类型' "
        );
        $this->createIndex('effective_scope_type', Event::tableName(), 'effective_scope_type');

        //  活动表 添加 过期是否销毁 字段，支持 优惠券过期自动销毁
        $this->addColumn(
            Event::tableName(),
            'auto_destroy',
            " TINYINT NOT NULL DEFAULT '0' COMMENT '过期是否销毁' "
        );
        $this->createIndex('auto_destroy', Event::tableName(), 'auto_destroy');

        //  满减/优惠券 规则表 添加领券后的有效时间
        //      =0 表示 优惠券的有效时段 按 互动的有效时段来设定
        //      >0 表示 优惠券的有效时段 按 领券时间 —— 领券时间 + term_of_validity （秒数）
        $this->addColumn(
            FullCutRule::tableName(),
            'term_of_validity',
            " INT NOT NULL DEFAULT '0' COMMENT '领券后有效时间(s)' "
        );
        $this->createIndex('term_of_validity', FullCutRule::tableName(), 'term_of_validity');

        //  添加 活动与品牌的关系表 支持设置品牌券(1:n)
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable(
            'o_event_to_brand',
            [
                'id' => $this->primaryKey(),
                'event_id' => $this->integer()->notNull()->comment('活动ID'),
                'brand_id' => $this->integer()->notNull()->comment('品牌ID'),
            ],
            $tableOptions
        );
        $this->createIndex('event_id', 'o_event_to_brand', 'event_id');
        $this->createIndex('brand_id', 'o_event_to_brand', 'brand_id');

        //  优惠券添加生效起止时段, 如果 FullCutRule::tableName() 表的 term_of_validity 字段不为0 则 绑定券时 修正券的可用时间
        $this->addColumn(
            CouponRecord::tableName(),
            'start_time',
            " DATETIME NOT NULL DEFAULT 0 COMMENT '可用时段开始时间' "
        );
        $this->addColumn(
            CouponRecord::tableName(),
            'end_time',
            " DATETIME NOT NULL DEFAULT 0 COMMENT '可用时段开始时间' "
        );

        //  修正小美直发品牌的 默认配送方式为  小美直发(满额包邮)
        $this->update(Brand::tableName(), ['shipping_id' => 5], ['brand_id' => 42]);
        $this->update(
            Goods::tableName(),
            ['shipping_id' => 5],
            [
                'brand_id' => 42,
            ]
        );
    }

    public function safeDown()
    {
        $this->dropIndex('effective_scope_type', Event::tableName());
        $this->dropColumn(Event::tableName(), 'effective_scope_type');

        $this->dropIndex('auto_destroy', Event::tableName());
        $this->dropColumn(Event::tableName(), 'auto_destroy');

        $this->dropIndex('term_of_validity', FullCutRule::tableName());
        $this->dropColumn(FullCutRule::tableName(), 'term_of_validity');

        $this->dropTable('o_event_to_brand');

        $this->dropColumn(CouponRecord::tableName(), 'start_time');
        $this->dropColumn(CouponRecord::tableName(), 'end_time');
    }
}
