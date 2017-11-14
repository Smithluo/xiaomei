<?php

use yii\db\Migration;
use common\models\Goods;
use common\models\OrderInfo;
use common\models\OrderGoods;
use common\models\ShopConfig;
use common\models\DeliveryGoods;

class m170221_054240_version23_operation_demand extends Migration
{

    /**
     *  商品添加有效期字段
     *  修改订单的备注，把商品的开样配比些在备注的前边
     */
    public function safeUp()
    {
        $this->addColumn(Goods::tableName(), 'expire_date', "  CHAR(10) NULL COMMENT '有效期至' ");
        $this->alterColumn(
            OrderInfo::tableName(),
            'postscript',
            " TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL ");

        //  管理员登录方式修改后，关联到的admin_id 字段 修改字段类型
        $this->alterColumn(
            'o_link_goods',
            'admin_id',
            " INT UNSIGNED NOT NULL DEFAULT '0' COMMENT '管理员userId' ");
        $this->alterColumn(
            'o_goods_article',
            'admin_id',
            " INT UNSIGNED NOT NULL DEFAULT '0' COMMENT '管理员userId' ");
        $this->alterColumn(
            'o_group_goods',
            'admin_id',
            " INT UNSIGNED NOT NULL DEFAULT '0' COMMENT '管理员userId' ");
        $this->alterColumn(
            'o_package_goods',
            'admin_id',
            " INT UNSIGNED NOT NULL DEFAULT '0' COMMENT '管理员userId' ");

        //  修改减库存时机为 支付减库存
        $this->update(
            ShopConfig::tableName(),
            [
                'store_range' => '2,1,0',
                'value' => 2
            ],
            ['id' => 423]
        );

        //  修复商品的实际支付价格
        $updatePayPrice = ' UPDATE '.OrderGoods::tableName().' SET pay_price = goods_price WHERE pay_price = 0 ';
        $this->execute($updatePayPrice);
    }

    public function safeDown()
    {
        $this->dropColumn(Goods::tableName(), 'expire_date');
        $this->alterColumn(
            OrderInfo::tableName(),
            'postscript',
            "  VARCHAR(255) NOT NULL DEFAULT '' ");

        $this->alterColumn(
            'o_link_goods',
            'admin_id',
            " TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '管理员userId' ");
        $this->alterColumn(
            'o_goods_article',
            'admin_id',
            " TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '管理员userId' ");
        $this->alterColumn(
            'o_group_goods',
            'admin_id',
            " TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '管理员userId' ");
        $this->alterColumn(
            'o_package_goods',
            'admin_id',
            " TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '管理员userId' ");

        //  修改减库存时机为 下单减库存
        $this->update(
            ShopConfig::tableName(),
            [
                'store_range' => '1,0',
                'value' => 1
            ],
            ['id' => 423]
        );

    }
}
