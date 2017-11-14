<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/17 0017
 * Time: 16:01
 */

namespace data\models;


use yii\db\ActiveRecord;

class DataAnalysis extends ActiveRecord
{
    public static function tableName()
    {
        return 'o_analysis_data';
    }

    public function attributes()
    {
        return [
            'id',   //自增id
            'user_id',  //用户id
            'consignee',    //收货人
            'goods_id', //商品id
            'goods_sn', //商品条形码
            'goods_name',   //商品名称
            'goods_number', //购买数量
            'goods_amount', //购买金额
            'group_id', //总订单id
            'group_status', //总单状态
            'order_amount', //总单金额
            'cat_id',   //品类id
            'cat_name', //品类名称
            'brand_id', //品牌id
            'brand_name',   //品牌名称
            'create_time',  //下单时间 -时间戳
            'pay_time', //支付时间 -时间戳
            'date'  //日期
        ];
    }

    public function rules()
    {
        return [
            [
                [
                    'id',   //自增id
                    'user_id',  //用户id
                    'consignee',    //收货人
                    'goods_id', //商品id
                    'goods_sn', //商品条形码
                    'goods_name',   //商品名称
                    'goods_number', //购买数量
                    'goods_amount', //购买金额
                    'group_id', //总订单id
                    'group_status', //总单状态
                    'order_amount', //总单金额
                    'cat_id',   //品类id
                    'cat_name', //品类名称
                    'brand_id', //品牌id
                    'brand_name',   //品牌名称
                    'create_time',  //下单时间 -时间戳
                    'pay_time', //支付时间 -时间戳
                    'date'  //日期
                ], 'safe'
            ]
        ];
    }
}