<?php

use yii\db\Migration;

use common\models\Shipping;
use common\models\Goods;

class m170408_025111_v26_goodsActivtiy_add_sample_and_recover_shipping extends Migration
{
    /**
     * 团采/秒杀 等活动 配置独立的 物料配比， 与商品属性中的物料配比无关
     * 预付运费 配送规则 改为
     */
    public function safeUp()
    {
        $this->addColumn('o_goods_activity', 'sample', " VARCHAR(255) NULL COMMENT '物料配比' ");
        $this->update(
            Shipping::tableName(),
            ['shipping_name' => '发货方包邮'],
            ['shipping_code' => 'free']
        );
        //  修改所有直发商品的配送方式为 小美直发(满额包邮)
        $this->update(
            Goods::tableName(),
            ['shipping_id' => 5],
            ['supplier_user_id' => '1257']
        );
    }

    public function safeDown()
    {
        $this->dropColumn('o_goods_activity', 'sample');
        $this->update(
            Shipping::tableName(),
            ['shipping_name' => '包邮'],
            ['shipping_code' => 'free']
        );
        echo ' goods.shipping_id = 5 小美直发(满额包邮) 无法回撤 ';
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
