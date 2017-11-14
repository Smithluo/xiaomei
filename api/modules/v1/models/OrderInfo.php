<?php
/**
 * Created by PhpStorm.
 * User: clark
 * Date: 2016/11/23
 * Time: 9:35
 */

namespace api\modules\v1\models;

use common\helper\DateTimeHelper;
use common\helper\ImageHelper;
use common\helper\NumberHelper;

class OrderInfo extends \common\models\OrderInfo
{
    public $ordergoods; //  订单中的商品
    public $csStatus;   //  订单的综合状态
    public $format_order_status;    //综合状态的文案

    public function fields()
    {
        return [
            'order_id' => function ($model) {
                return (int)$model->order_id;
            },
            'user_id' => function ($model) {
                return (int)$model->user_id;
            },  //  '用户ID',
            'order_status' => function ($model) {
                return (int)$model->order_status;
            },  //  '订单状态',
            'shipping_status' => function ($model) {
                return (int)$model->shipping_status;
            },  //  '配送状态',
            'pay_status' => function ($model) {
                return (int)$model->pay_status;
            },  //  '支付状态',
            'country' => function ($model) {
                return (int)$model->country;
            },  //  '国家',
            'province' => function ($model) {
                return (int)$model->province;
            },  //  '省份',
            'city' => function ($model) {
                return (int)$model->city;
            },  //  '城市',
            'district' => function ($model) {
                return (int)$model->district;
            },  //  '区域',
            'shipping_id' => function ($model) {
                return (int)$model->shipping_id;
            },  //  '配送方式ID',
            'pay_id' => function ($model) {
                return (int)$model->pay_id;
            },  //  '支付方式ID',
            'is_separate' => function ($model) {
                return (int)$model->is_separate;
            },  //  '是否已分成', //  0未分成或等待分成;1已分成;2取消分成
            'extension_id' => function ($model) {
                return (int)$model->extension_id;
            },  //  通过活动购买的商品ID
            'mobile_pay' => function ($model) {
                return (int)$model->mobile_pay;
            },  //  '是否微信支付',
            'mobile_order' => function ($model) {
                return (int)$model->mobile_order;
            },  //  '是否微信下单',
            'brand_id' => function ($model) {
                return (int)$model->brand_id;
            },  //  '品牌',
            'supplier_user_id' => function ($model) {
                return (int)$model->supplier_user_id;
            },  //  '品牌商',
            'integral' => function ($model) {
                return (int)$model->integral;
            },  //  '积分',

            //  -------int 分割线 float-------

            'goods_amount' => function ($model) {
                return NumberHelper::price_format($model->goods_amount);
            },  //  商品总金额
            'shipping_fee' => function ($model) {
                return NumberHelper::price_format($model->shipping_fee);
            },  //  运费
            'discount' => function ($model) {
                return NumberHelper::price_format($model->discount);
            },  //  '折扣金额',
            'money_paid' => function ($model) {
                return NumberHelper::price_format($model->money_paid);
            },  //  '已付款金额',
            'order_amount' => function ($model) {
                return NumberHelper::price_format($model->order_amount);
            },  //  '应付款金额',

            //  -------float 分割线 datetime-------

            'add_time' => function ($model) {
                return (string)DateTimeHelper::getFormatCNDateTime($model->add_time);
            },  //  '下单时间',
            'confirm_time' => function ($model) {
                if ($model->confirm_time == 0) {
                    return '';
                } else {
                    return (string)DateTimeHelper::getFormatCNDateTime($model->confirm_time);
                }
            },  //  '订单确认时间',
            'pay_time' => function ($model) {
                if ($model->pay_time == 0) {
                    return '';
                } else {
                    return (string)DateTimeHelper::getFormatCNDateTime($model->pay_time);
                }
            },  //  '订单支付时间',
            'shipping_time' => function ($model) {
                if ($model->shipping_time == 0) {
                    return '';
                } else {
                    return (string)DateTimeHelper::getFormatCNDateTime($model->shipping_time);
                }
            },  //  '订单发货时间',
            'recv_time' => function ($model) {
                if ($model->recv_time == 0) {
                    return '';
                } else {
                    return (string)DateTimeHelper::getFormatCNDateTime($model->recv_time);
                }
            },  //  '确认收货时间',

            //  -------datetime 分割线 string-------

            'extension_code' => function ($model) {
                if (empty($model->extension_code)) {
                    return '';
                } else {
                    return (string)$model->extension_code;
                }
            },  //  '扩展代码', 活动类型：group_buy
            'zipcode' => function ($model) {
                return (string)$model->zipcode;
            },  //  '邮编',

            'to_buyer' => function ($model) {
                return (string)$model->to_buyer;
            },  //  '商城留言',   //  商家给客户的留言,当该字段值时可以在订单查询看到
            'pay_note' => function ($model) {
                return (string)$model->pay_note;
            },  //  '付款备注',
            'order_sn' => function ($model) {
                return (string)$model->order_sn;
            },  //  '订单编号',
            'consignee' => function ($model) {
                return (string)$model->consignee;
            },  //  '收货人',
            'address' => function ($model) {
                return (string)$model->address;
            },  //  '详细地址',
            'tel' => function ($model) {
                return (string)$model->tel;
            },  //  '电话',
            'mobile' => function ($model) {
                return (string)$model->mobile;
            },  //  '手机号',
            'postscript' => function ($model) {
                return (string)$model->postscript;
            },  //  '订单附言',
            'shipping_name' => function ($model) {
                return (string)$model->shipping_name;
            },  //  '快递名称',
            'total_fee' => function ($model) {
                return NumberHelper::price_format($model['goods_amount'] + $model['shipping_fee'] - $model['discount']);
            },
            'order_goods_count' => function ($model) {
                return (int)$model->getOrderGoodsCount();
            },
            'orderGoods',   //  订单商品
            'csStatus',   //  订单综合状态
            'format_order_status',  //订单综合状态文案
            'brand',

            'group_id' => function ($model) {
                return (string)$model->group_id;
            },

            /*  当前未使用到的字段
            'integral_money' => '使用积分金额',
            'bonus' => '使用红包金额',
            'pack_fee' => '包装费用',
            'card_fee' => '贺卡费用',
            'insure_fee' => function ($model) {
                return (float)$model->insure_fee;
            },  //  '保价费用',
            'pay_fee' => 'Pay Fee', //  支付费用,跟支付方式的配置相关,取值表o_payment
            'best_time' => '最佳送货时间',
            'sign_building' => '标志性建筑',
            'email' => '电子邮件',
            'how_oos' => '缺货处理方式',
            'how_surplus' => '余额处理方式',
            'pack_name' => '包装名称',
            'card_name' => '贺卡名称',
            'card_message' => '贺卡内容',
            'inv_payee' => '发票抬头',
            'inv_content' => '发票内容',    //  用户页面选择,取值o_shop_config的code字段的值 为invoice_content的value
            'inv_type' => '发票类型',
            'tax' => '发票税额',
            'invoice_no' => '发票编号',
            'from_ad' => '来源广告',
            'referer' => '订单来源页面',
            'pack_id' => '包装ID',
            'card_id' => '贺卡ID',
            'bonus_id' => '红包ID',
            'parent_id' => '推荐人 ID',
            'agency_id' => '办事处 ID',
            'surplus' => '用户可用余额',
            'pay_name' => '支付方式名称',
            */
        ];
    }


    /**
     * 订单 与 订单中的商品  的关联关系
     * @return \yii\db\ActiveQuery
     */
    public function getOrderGoods()
    {
        return $this->hasMany(OrderGoods::className(), ['order_id' => 'order_id']);
    }

    /**
     * 获取订单中商品的总数量
     * @return int
     */
    public function getOrderGoodsCount() {
        $result = 0;
        if (empty($this->orderGoods)) {
            return $result;
        }
        foreach ($this->orderGoods as $orderGoods) {
            $result += $orderGoods->goods_number;
        }
        return $result;
    }

    /**
     * 格式化商品信息
     *
     * 处理赠品的所属关系
     * @param $list     订单列表 orderList
     * @param $type     订单类型：needPay、needReceive、refuse、all 等
     * @return mixed
     */
    public static function orderListFormat($list, $type)
    {
        foreach ($list as &$item) {
            $goodsIdList = array_column($item['orderGoods'], 'goods_id');
            $goodsThumbMap = Goods::getThumbMap($goodsIdList);

            $formatGoods = self::orderGoodsFormat($item['orderGoods'], $goodsThumbMap);

//            if ($type == 'needPay') {
//                $item['csStatus'] = OrderInfo::ORDER_CS_STATUS_TO_BE_PAID;
//            } elseif ($type == 'needReceive') {
//                $item['csStatus'] = OrderInfo::ORDER_CS_STATUS_SHIPPED;
//            } else {
                $item['csStatus'] = OrderInfo::getOrderCsStatusNo([
                    'order_status'      => $item['order_status'],
                    'shipping_status'   => $item['shipping_status'],
                    'pay_status'        => $item['pay_status'],
                ]);
//            }

            $item['format_order_status'] = OrderInfo::$order_cs_status_map_no_style[$item['csStatus']];
//            $item['total_fee'] = NumberHelper::price_format($item['goods_amount'] + $item['shipping_fee'] - $item['discount']);
            $item['orderGoods'] = $formatGoods;
        }

        return $list;
    }

    /**
     * 格式化 订单中的商品
     * @param array $orderGoods   订单的商品列表
     * @return array
     */
    public static function orderGoodsFormat($orderGoods, $goodsThumbMap)
    {
        $gifts = [];
        $wuliaoList = [];
        $formatGoods = [];

        //  分离赠品 与 非赠品
        foreach ($orderGoods as $goods) {
            //  分配 商品缩略图
            if (!empty($goodsThumbMap) && $goodsThumbMap[$goods['goods_id']]) {
                $goods['goods_thumb'] = ImageHelper::get_image_path($goodsThumbMap[$goods['goods_id']]);
            }

            if ($goods['is_gift'] == OrderGoods::IS_GIFT_GIFT) {
                $formatGoods[$goods['parent_id']]['gift'][] = $goods;
            } elseif ($goods['is_gift'] == OrderGoods::IS_GIFT_WULIAO) {
                $formatGoods[$goods['parent_id']]['wuliaoList'][] = $goods;
            } else {
                $formatGoods[$goods['goods_id']] = $goods;
            }
        }

        return array_values($formatGoods);
    }

    /**
     * 获取用户已支付的总金额
     *
     * @param $userId   用户ID
     * @return int|number   支付总额
     */
    public function getUserTotalAmount($userId)
    {
        $rs = OrderInfo::find()
            ->select('goods_amount')
            ->where([
                'user_id' => $userId,
                'pay_status' => self::PAY_STATUS_PAYED,
            ])->asArray()
            ->all();

        if ($rs) {
            $amount = array_column($rs, 'goods_amount');
            $totalAmount = array_sum($amount);
            return NumberHelper::price_format($totalAmount);
        } else {
            return 0;
        }
    }

    /**
     * 获取pay_log
     * @return \yii\db\ActiveQuery
     */
    public function getPaylog() {
        return $this->hasOne(PayLog::className(), ['order_id' => 'order_id']);
    }

    public function getBrand() {
        return $this->hasOne(Brand::className(), ['brand_id' => 'brand_id']);
    }
}