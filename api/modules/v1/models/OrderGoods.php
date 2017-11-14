<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/27 0027
 * Time: 19:53
 */

namespace api\modules\v1\models;

use common\helper\ImageHelper;
use common\helper\NumberHelper;

class OrderGoods extends \common\models\OrderGoods
{
    public $gift;           //  赠品
    public $goods_thumb;    //  商品详情图

    public function fields()
    {
        return [
            'rec_id' => function ($model) {
                return (int)$model->rec_id;
            },  //  '购物车ID',
            'order_id' => function ($model) {
                return (int)$model->order_id;
            },  //  '订单ID',
            'goods_id' => function ($model) {
                return (int)$model->goods_id;
            },  //  '商品ID',
            'goods_number' => function ($model) {
                return (int)$model->goods_number;
            },  //  '商品数量',
            'send_number' => function ($model) {
                return (int)$model->send_number;
            },  //  '发货数量',
            'is_real' => function ($model) {
                return (int)$model->is_real;
            },  //  '是否是实际商品',
            'parent_id' => function ($model) {
                return (int)$model->parent_id;
            },  //  '父级ID',表示 赠品与主商品的关系
            'is_gift' => function ($model) {
                return (int)$model->is_gift;
            },  //  '是否赠品',    //  是否赠品,0否; 1赠品; 2物料; 其他:未定义

            'market_price' => function ($model) {
                return NumberHelper::price_format($model->market_price);
            },  //  '市场价格',
            'goods_price' => function ($model) {
                return NumberHelper::price_format($model->goods_price);
            },  //  '实际售价',
            'pay_price' => function ($model) {
                return NumberHelper::price_format($model->pay_price);
            },  //  '均摊优惠后的实际支付价',
            'goods_total' => function ($model) {
                return NumberHelper::price_format($model->goods_price * $model->goods_number);
            },  //  '小计',
            'goods_name' => function ($model) {
                return (string)$model->goods_name;
            },  //  '商品名称',
            'goods_sn' => function ($model) {
                return (string)$model->goods_sn;
            },  //  '货号',

            'gift', //  赠品的具体信息
            'goods_thumb' => function ($model) {
                if (empty($model->goods)) {
                    return '';
                }
                return ImageHelper::get_image_path($model->goods->goods_thumb ?: '');
            },  //  商品详情图
            'goodsActivity',  //  团拼活动
            'event',
//            'orderInfo',  //  订单信息

          /*  'extension_code' => 'Extension Code',   //  商品的扩展属性,取自ecs_goods的extension_code
            'product_id' => '货品ID',
            'goods_attr_id' => '商品属性ID',        //  取自goods_attr的goods_attr_id,
            'goods_attr' => '商品属性',*/
        ];
    }

    public function getEvent()
    {
        return $this->hasOne(Event::className(), [
            'event_id' => 'event_id',
        ]);
    }
}