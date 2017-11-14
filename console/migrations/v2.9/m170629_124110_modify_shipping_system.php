<?php

use yii\db\Migration;
use common\models\OrderInfo;
use common\models\Brand;
use common\models\Goods;
use common\models\Shipping;
use common\models\ShippingArea;

class m170629_124110_modify_shipping_system extends Migration
{
    public function safeUp()
    {
        //  【1】订单的shipping_id、shipping_name修正
        OrderInfo::updateAll(
            [
                'shipping_name' => '包邮',
                'shipping_id' => 2,
            ],
            [
                'shipping_fee' => 0.00,
                'shipping_id' => 1,
//                'shipping_name' => '每个品牌三件以上满百包邮'
            ]
        );  //  最初的运费规则，三件以上满百包邮

        OrderInfo::updateAll(
            [
                'shipping_name' => '现付',
                'shipping_id' => 4,
            ],
            [
                'shipping_fee' => 12,
                'shipping_id' => [0, 1]
            ]
        );  //  最初的运费规则，不满足 【三件以上满百包邮】 支付12元运费

        OrderInfo::updateAll(
            [
                'shipping_name' => '到付',
                'shipping_id' => 3,
            ],
            [
                'shipping_id' => 0,
                'shipping_fee' => 0,
                'shipping_name' => ['运费到付', ''],
            ]
        );  //  修正到付 id 和 name, 没有填写运费信息的 默认到付

        OrderInfo::updateAll(
            [
                'shipping_name' => '现付',
                'shipping_id' => 4,
            ],
            [
                'shipping_id' => 1,
                'shipping_fee' => 86,
                'order_id' => 9
            ]
        );  //  早期的测试数据

        OrderInfo::updateAll(
            [
                'shipping_name' => '包邮',
            ],
            [
                'shipping_id' => 2,
                'shipping_name' => '发货方包邮'
            ]
        );  //  发货方包邮

        OrderInfo::updateAll(
            [
                'shipping_name' => '包邮',
                'shipping_id' => 2,
            ],
            [
                'shipping_id' => 0,
                'shipping_name' => '包邮'
            ]
        );  //  包邮 的shipping_id 修正


        //  【小美直发 满额包邮】
        OrderInfo::updateAll(
            [
                'shipping_name' => '包邮',
                'shipping_id' => 2,
            ],
            [
                'shipping_id' => 5,
                'shipping_name' => '小美直发(包邮)',
                'shipping_fee' => 0.00,
            ]
        );  //  小美直发(包邮) 且运费为0 简化为包邮

        OrderInfo::updateAll(
            [
                'shipping_name' => '现付',
                'shipping_id' => 4,
            ],
            [
                'and',
                ['shipping_id' => 5],
                ['shipping_name' => ['小美直发(运费现付)', '小美直发(已付运费)']],
                ['>', 'shipping_fee', 0],
            ]
        );  //  小美直发(包邮) 且运费不为0 简化为现付

        OrderInfo::updateAll(
            [
                'shipping_name' => '现付',
            ],
            [
                'and',
                ['shipping_id' => 4],
                ['shipping_name' => '运费现付'],
                ['>', 'shipping_fee', 0],
            ]
        );  //  运费现付的修正

        OrderInfo::updateAll(
            [
                'shipping_name' => '到付',
                'shipping_id' => 3,
            ],
            [
                'order_id' => [7213, 7427, 7755, 8850]
            ]
        );  //  IOS下单 入库错误，不满足包邮条件

        OrderInfo::updateAll(
            [
                'shipping_name' => '到付',
            ],
            [
                'shipping_id' => 3,
                'shipping_name' => '小美直发(到付)',
                'shipping_fee' => 0.00,
            ]
        );  //  到付的 shipping_name 精简

        OrderInfo::updateAll(
            [
                'shipping_name' => '到付',
                'shipping_id' => 3,
            ],
            [
                'shipping_id' => 5,
                'shipping_name' => '小美直发(运费到付)',
                'shipping_fee' => 0.00,
            ]
        );  //  到付的 shipping_name 精简

        OrderInfo::updateAll(
            [
                'shipping_name' => '包邮',
                'shipping_id' => 2,
            ],
            [
                'shipping_id' => 6,
                'shipping_name' => ['小美直发包邮', '小美直发(包邮)']
            ]
        );  //  【小美直发包邮】 修正


        OrderInfo::updateAll(
            [
                'shipping_name' => '到付',
            ],
            [
                'shipping_id' => 3,
                'shipping_name' => '运费到付',
                'shipping_fee' => 0.00,
            ]
        );  //  运费到付的修正

        OrderInfo::updateAll(
            [
                'shipping_name' => '到付',
                'shipping_fee' => 0.00,
            ],
            [
                'shipping_id' => 3,
                'shipping_name' => '运费到付',
                'order_id' => 4262, // 线上的测试数据
            ]
        );  //  运费到付的修正


//  品牌的默认shipping_id修正 默认到付, 品牌必须设置shipping_id
        Brand::updateAll(['shipping_id' => 3], ['not in', 'brand_id', [2, 42]]);
        $this->alterColumn('o_brand', 'shipping_id', " SMALLINT(5) NOT NULL DEFAULT '3' COMMENT '运费模版ID' ");
        Brand::updateAll(['shipping_id' => 7], ['brand_id' => 168]);

        //  商品的默认shipping_id修正 默认0，即 不设特例 普通商品解散使用品牌的配送方式；  o_goods.shipping_code 没有值，没启用
        Goods::updateAll(['shipping_id' => 0]);   //  默认到付，商品不设特例

        //  配送区域名称修正
        Shipping::updateAll(
            ['shipping_desc' => '发货方包邮, 偏远地区到付, 不支持自选物流'],
            ['shipping_id' => 2]
        );
        ShippingArea::updateAll(
            ['shipping_area_name' => '到付'], //  偏远地区
            ['shipping_area_id' => 4]
        );
        ShippingArea::updateAll(
            ['shipping_area_name' => '包邮'],
            ['shipping_area_id' => 5]
        );

        Shipping::updateAll(
            ['shipping_desc' => '到付, 不支持自选物流'],
            ['shipping_id' => 3]
        );
        ShippingArea::updateAll(
            ['shipping_area_name' => '到付'],
            ['shipping_area_id' => 3]
        );

        Shipping::updateAll(
            ['shipping_desc' => '满额包邮, 不支持自选物流'],
            ['shipping_id' => 5]
        );
        ShippingArea::updateAll(
            ['shipping_area_name' => '20元，单笔订单满2999元包邮'],
            ['shipping_area_id' => 13]
        );
        ShippingArea::updateAll(
            ['shipping_area_name' => '30元，单笔订单满3999元包邮'],
            ['shipping_area_id' => 14]
        );
        ShippingArea::updateAll(
            ['shipping_area_name' => '50元，单笔订单满4999元包邮'],
            ['shipping_area_id' => 15]
        );

        Shipping::updateAll(
            ['shipping_desc' => '小美直发包邮, 不支持自选物流'],
            ['shipping_id' => 6]
        );
        ShippingArea::updateAll(
            ['shipping_area_name' => '包邮'],
            ['shipping_area_id' => 16]
        );

        Shipping::updateAll(
            [
                'shipping_name' => '满2500元包邮',
                'shipping_desc' => '单笔订单满2500元包邮',
            ],
            ['shipping_id' => 7]
        );
        ShippingArea::updateAll(
            ['shipping_area_name' => '运费到付，单笔订单满2500元包邮'],
            ['shipping_area_id' => 17]
        );
    }

    public function safeDown()
    {
        echo ' Can Not Down! ';
    }

}
