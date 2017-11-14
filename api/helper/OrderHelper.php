<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/27 0027
 * Time: 20:27
 */

namespace api\helper;

use api\modules\v1\models\OrderGoods;
use api\modules\v1\models\OrderInfo;
use api\modules\v1\models\Payment;
use api\modules\v1\models\TouchPayment;
use common\helper\ImageHelper;
use Yii;
use api\modules\v1\models\Event;
use api\modules\v1\models\Cart;
use api\modules\v1\models\Goods;
use api\modules\v1\models\GoodsActivity;
use api\modules\v1\models\Shipping;
use api\modules\v1\models\Moq;
use api\modules\v1\models\ShippingArea;
use common\helper\NumberHelper;

class OrderHelper
{
    public function getPayFee($amount) {
        return $amount * 0.006;
    }

    public static function getUserStartNum($userRank, $goodsId)
    {
        $gTb = Goods::tableName();
        $mTb = Moq::tableName();
        $rs = Goods::find()->joinWith('moqs')->andOnCondition([$mTb.'.user_rank' => $userRank])
                ->select([
                    $gTb.'.start_num', $mTb.'.moq'
                ])->where([
                    $gTb.'.goods_id' => $goodsId,
                ])->one();

        $startNum = $rs['start_num'];
        if (!empty($rs->moq)) {
            $startNum = $rs->moq;
        }

        return $startNum;
    }

    /**
     * @param array $order
     * @param array $orderGoods
     * @param array $consignee
     * @param string $flowType
     * @return array
     */
    public static function getOrderFee($order, $orderGoods, $consignee, $flowType) {
        //  初始化订单的扩展code 区分普通订单和 团拼订单
        if (!isset($order['extension_code'])) {
            $order['extension_code'] = '';
        } elseif ($order['extension_code'] == 'group_buy') {
            $group_buy = GoodsActivity::getGroupBuyInfo($order['extension_id']);
        }

        //  商品分组
        $brand_or_supplier_grouped_goods_list = [];
        foreach ($orderGoods as $goods) {
            if ($goods['supplier_user_id'] > 0) {
                $goods['is_supplier'] = 1;
                $brand_or_supplier_grouped_goods_list[$goods['supplier_user_id']][] = $goods;
            }
            elseif ($goods['brand_id']) {
                $goods['is_supplier'] = 0;
                $brand_or_supplier_grouped_goods_list[$goods['brand_id']][] = $goods;
            }

        }

        //  初始化
        $total = [
            'real_goods_count' => 0,    //  真实商品的件数
            'gift_amount' => 0.00,  //  赠品需要支付的总额
            'goods_number' => 0,  //  商品总数
            'goods_price' => 0.00,  //  售价的总额
            'market_price' => 0.00, //  市场价的总额
            'discount' => 0.00, //  满减 满足条件减去的钱数
            'pack_fee' => 0.00, //  打包费用
            'card_fee' => 0.00, //  贺卡费用
            'shipping_fee' => 0.00, //  运费
            'shipping_insure' => 0,
            'integral_money' => 0,  //  赠送积分
            'bonus' => 0,   //  红包
            'surplus' => 0, //  余额支付
            'cod_fee' => 0.00,  //
            'pay_fee' => 0.00,  //  交易手续费
            'tax' => 0.00,  //  税
            'saving' => 0.00    //  节省的费用
        ];

        //  计算折扣(全局活动的折扣) 暂时没有逻辑，需要时 去 M站的UsersModel.class.php order_fee 找代码  团购商品不参与折扣

        //  商品总价, 分组小计， 运费计算
        foreach ($brand_or_supplier_grouped_goods_list as $brand_or_supplier_id => $goods_list) {
            $shipping_id = 0;

            $brandGoodsInfo = [
                'goods_number' => 0,
                'goods_weight' => 0,
                'goods_amount' => 0,
            ];

            foreach ($goods_list as $goods) {
                if($goods['goods_id'] > 0) {
                    $shipping_id = $goods['shipping_id'];   //  商品配置上要求 能拆到一个订单里的商品 对应的运费规则是一致的

                    $brandGoodsInfo['goods_number'] += $goods['goods_number'];
                    $brandGoodsInfo['goods_weight'] += $goods['goods_number'] * $goods['goods_weight'];
                    $brandGoodsInfo['goods_amount'] += $goods['goods_number'] * $goods['goods_price'];
                }
            }
            //  如果商品没有设置配送方式， 则取默认配送方式
            if ($shipping_id == 0) {
                $defaultShippingId = Shipping::getDefaultShippingId();
                $shipping_id = $defaultShippingId;
            }

            //  容错，用户可能没有地址
            $region = [];
            if (!empty($consignee['province'])) {
                $region['province'] = $consignee['province'];
            }
            if (!empty($consignee['city'])) {
                $region['city'] = $consignee['city'];
            }
            if (!empty($consignee['district'])) {
                $region['district'] = $consignee['district'];
            }

            $shipping_info = ShippingArea::shippingRreaInfo($shipping_id, $region);

            //  如果配送方式对应的区域($shipping_info) 有配置参数，并且配置参数中有 回调的配送方式代码(backup_shipping_code) 则重新计算配送费用
            if (!empty($shipping_info['shipping_config']) && !empty($shipping_info['shipping_config']['backup_shipping_code'])) {
                $backupShippingInfo = Shipping::find()->where([
                    'shipping_code' => $shipping_info['shipping_config']['backup_shipping_code'],
                    'enabled' => 1
                ])->one();

                if ($backupShippingInfo) {
                    $shipping_info = ShippingArea::shippingRreaInfo($backupShippingInfo->shipping_id, $region);
                }
            }

            $shipping_fee = 0;
            /**
             *  暂时不涉及有具体费用的运费，需要时再处理
            if (!empty($shipping_info)) {
                // 查看采购车中是否全为免运费商品，若是则把运费赋为零
                $res = $this->row($sql);
                $shipping_count =Cart::find()
                    ->where([
                        '!=', 'extension_code', 'package_buy'
                    ])->andWhere([
                        'is_shipping' => 0,
                        'selected' => 1,
                    ])->count();

                $shipping_fee = ($shipping_count == 0) ? 0 : shipping_fee(
                    $shipping_info['shipping_code'],
                    $shipping_info['configure'],
                    $brandGoodsInfo['goods_weight'],
                    $brandGoodsInfo['goods_amount'],
                    $brandGoodsInfo['goods_number']
                );
                $total['shipping_fee'] += $shipping_fee;

                if ($shipping_info['support_cod']) {
                    $shipping_cod_fee = $shipping_info['pay_fee'];
                }
            }
            */
            $total['shipping_fee'] = 0;

            if (!empty($shipping_info)) {
                $shipping_cod_fee = $shipping_info['pay_fee'];
            }

            foreach ($goods_list as $index => $goods) {
                if($goods['goods_id'] > 0) {
                    if ($goods['is_real']) {
                        $total['real_goods_count']++;
                    }
                    $total['goods_number'] += $goods['goods_number'];

                    //  可能会按照配送区域纠正成邮费到付
                    if (!empty($shipping_info)) {
                        $brand_or_supplier_grouped_goods_list[$brand_or_supplier_id][$index]['shipping_id'] = $shipping_info['shipping_id'];
                        $brand_or_supplier_grouped_goods_list[$brand_or_supplier_id][$index]['shipping_code'] = $shipping_info['shipping_code'];
//                        $brand_or_supplier_grouped_goods_list[$brand_or_supplier_id][$index]['shipping_desc'] = $shipping_info['shipping_desc'];
                        $brand_or_supplier_grouped_goods_list[$brand_or_supplier_id][$index]['shipping_fee_format'] = $shipping_info['shipping_name'];
                    }

                    $total['goods_price'] += bcmul($goods['goods_price'], $goods['goods_number'], 2);
                    $total['market_price'] += bcmul($goods['market_price'], $goods['goods_number'], 2);

                    if (
                        isset($total['summary'][$goods['brand_id']]['goods_number']) &&
                        $total['summary'][$goods['brand_id']]['goods_number'] > 0)
                    {
                        $total['summary'][$goods['brand_id']]['goods_number'] += $goods['goods_number'];
                        $total['summary'][$goods['brand_id']]['goods_price'] += bcmul($goods['goods_number'], $goods['goods_price'], 2);
                    } else {
                        $total['summary'][$goods['brand_id']]['goods_number'] = $goods['goods_number'];
                        $total['summary'][$goods['brand_id']]['goods_price'] = bcmul($goods['goods_number'], $goods['goods_price'], 2);

                        //为每个品牌算运费,如果当前是按照供应商拆的单，就把邮费按照重量分配到每个品牌上显示
                        $is_supplier = $goods['is_supplier'];
                        if(!$is_supplier) {
                            $total['summary'][$goods['brand_id']]['shipping_fee'] = bcmul($shipping_fee, 1.00, 2);
                        }
                        else {
                            $brand_percent = empty($brand_supplier_weight_percent[$goods['brand_id']])
                                ? 1.0
                                : $brand_supplier_weight_percent[$goods['brand_id']];

                            $total['summary'][$goods['brand_id']]['shipping_fee'] = bcmul($shipping_fee, $brand_percent, 2);
                        }
//                        $total['summary'][$goods['brand_id']]['shipping_fee_format'] = '¥'. $total['summary'][$goods['brand_id']]['shipping_fee'].'(不含到付运费)';
                    }
                    $total['summary'][$goods['brand_id']]['goods_price'] = bcmul($total['summary'][$goods['brand_id']]['goods_price'], 1.00, 2);
                }
            }
        }

        //  只考虑到付和包邮
        $format_free = '包邮';
        $format_fpd = '运费到付';
        $format_mix = '部分运费到付';

        foreach ($brand_or_supplier_grouped_goods_list as $goods_list) {
            foreach ($goods_list as $goods) {
                if (!is_array($goods)) {
                    continue;
                }
                $brand_id = $goods['brand_id'];
                $total['summary'][$brand_id]['shipping_code'] = $goods['shipping_code'];
                if (!empty($total['summary'][$brand_id]['shipping_fee_format'])) {

                    if (
                        ($total['summary'][$brand_id]['shipping_fee_format'] == $format_fpd && $goods['shipping_code'] != 'fpd')
                        || ($total['summary'][$brand_id]['shipping_fee_format'] == $format_free && $goods['shipping_code'] != 'free')
                    ) {
                        $total['summary'][$brand_id]['shipping_fee_format'] = $format_mix;
                    }
                }
                else {
                    switch ($goods['shipping_code']) {
                        case 'free':
                            $total['summary'][$brand_id]['shipping_fee_format'] = $format_free;
                            break;
                        case 'fpd':
                            $total['summary'][$brand_id]['shipping_fee_format'] = $format_fpd;
                            break;
                        default:
                            $total['summary'][$brand_id]['shipping_fee_format'] = $format_fpd;
                            break;
                    }
                }
            }
        }

        //原来按配送方式选择的配送价格体系
        $total['shipping_fee_formated'] = NumberHelper::price_format($total['shipping_fee']);

        /*
        // 采购车中的商品能享受红包支付的总额
        $bonus_amount = model('Order')->compute_discount_amount();
        // 红包和积分最多能支付的金额为商品总额
        $max_amount = $total['goods_price'] == 0 ? $total['goods_price'] : $total['goods_price'] - $bonus_amount;
        */
        $bonus_amount = 0;
        $max_amount = 0;

        //  计算订单总额
        if ($order['extension_code'] == 'group_buy' && $group_buy['deposit'] > 0) {
            $total['amount'] = $total['goods_price'];
        } else {
            $total['amount'] = $total['goods_price'] - $total['discount']
                + $total['tax'] + $total['pack_fee'] + $total['card_fee']
                + $total['shipping_fee'] + $total['shipping_insure'] + $total['cod_fee'];

            // 减去红包金额
            $use_bonus = min($total['bonus'], $max_amount); // 实际减去的红包金额
            if (isset($total['bonus_kill'])) {
                $use_bonus_kill = min($total['bonus_kill'], $max_amount);
                $total['amount'] -= $price = $total['bonus_kill']; // 还需要支付的订单金额
            }

            $total['bonus'] = $use_bonus;
            $total['bonus_formated'] = $total['bonus'];

            $total['amount'] -= $use_bonus; // 还需要支付的订单金额
            $max_amount -= $use_bonus; // 积分最多还能支付的金额
        }

        /**
         * 余额
        $order['surplus'] = $order['surplus'] > 0 ? $order['surplus'] : 0;
        if ($total['amount'] > 0) {
            if (isset($order['surplus']) && $order['surplus'] > $total['amount']) {
                $order['surplus'] = $total['amount'];
                $total['amount'] = 0;
            } else {
                $total['amount'] -= floatval($order['surplus']);
            }
        } else {
            $order['surplus'] = 0;
            $total['amount'] = 0;
        }
        $total['surplus'] = $order['surplus'];
        $total['surplus_formated'] = price_format($order['surplus'], false);
        */

        /**
         * 积分
        $order['integral'] = $order['integral'] > 0 ? $order['integral'] : 0;
        if ($total['amount'] > 0 && $max_amount > 0 && $order['integral'] > 0) {
            $integral_money = value_of_integral($order['integral']);

            // 使用积分支付
            $use_integral = min($total['amount'], $max_amount, $integral_money); // 实际使用积分支付的金额
            $total['amount'] -= $use_integral;
            $total['integral_money'] = $use_integral;
            $order['integral'] = integral_of_value($use_integral);
        } else {
            $total['integral_money'] = 0;
            $order['integral'] = 0;
        }
        $total['integral'] = $order['integral'];
        $total['integral_formated'] = bcmul($total['integral_money'], 1, 2);
        */

        //  保存订单信息
//        $_SESSION['flow_order'] = $order; -------订单处理流程中的session 是否需要回传----------

        //  支付费用
        if (!empty($order['pay_id']) && ($total['real_goods_count'] > 0 || $flowType != Cart::CART_EXCHANGE_GOODS)) {
            $total['pay_fee'] = self::payFee($order['pay_id'], $total['amount'], $shipping_cod_fee);
        }
        $total['pay_fee_formated'] = $total['pay_fee'];

        $total['amount'] += $total['pay_fee']; // 订单总额累加上支付费用
        $total['amount_formated'] = $total['amount'];


        /**
         *  取得可以得到的积分和红包
        if ($order['extension_code'] == 'group_buy') {
            $total['will_get_integral'] = $group_buy['gift_integral'];
        } elseif ($order['extension_code'] == EXTENSION_CODE_EXCHANGE_GOODS) {
            $total['will_get_integral'] = 0;
        } else {
            $total['will_get_integral'] = model('Order')->get_give_integral();
        }
        $total['will_get_bonus'] = $order['extension_code'] == EXTENSION_CODE_EXCHANGE_GOODS ? 0 : price_format(model('Order')->get_total_bonus(), false);
        */

        /**
         *  积分兑换
        if ($order['extension_code'] == EXTENSION_CODE_EXCHANGE_GOODS) {
            $sql = 'SELECT SUM(eg.exchange_integral) ' .
                'as sum FROM ' . $this->pre . 'cart AS c,' .
                $this->pre . 'exchange_goods AS eg ' .
                "WHERE c.goods_id = eg.goods_id AND c.session_id= '" . SESS_ID . "' " .
                "  AND c.rec_type = '" . CART_EXCHANGE_GOODS . "' " .
                '  AND c.is_gift = 0 AND c.goods_id > 0 AND c.selected = 1' .
                'GROUP BY eg.goods_id';
            $res = $this->row($sql);
            $exchange_integral = $res['sum'];
            $total['exchange_integral'] = $exchange_integral;
        }
         */

        //  数据格式化
        $total['formated_goods_price'] = NumberHelper::price_format($total['goods_price']);
        $total['market_price_formated'] = NumberHelper::price_format($total['market_price']);
        $total['saving'] = bcsub($total['market_price'], $total['goods_price'], 2);
        $total['saving_formated'] = NumberHelper::price_format($total['saving']);
        $total['total_amount'] = NumberHelper::price_format($total['formated_goods_price']);    //  订单总额
        $total['amount'] = NumberHelper::price_format($total['amount']);    //  减去红包后需要支付的总额
        $total['goods_number'] = (int)$total['goods_number'];    //  用于结算的商品的总数
        $total['real_goods_count'] = (int)$total['real_goods_count'];
        $total['gift_amount'] = NumberHelper::price_format($total['gift_amount']);
        $total['goods_price'] = NumberHelper::price_format($total['goods_price']);
        $total['market_price'] = NumberHelper::price_format($total['market_price']);
        $total['discount'] = NumberHelper::price_format($total['discount']);
        $total['pack_fee'] = NumberHelper::price_format($total['pack_fee']);
        $total['card_fee'] = NumberHelper::price_format($total['card_fee']);
        $total['shipping_fee'] = NumberHelper::price_format($total['shipping_fee']);
        $total['cod_fee'] = NumberHelper::price_format($total['cod_fee']);
        $total['pay_fee'] = NumberHelper::price_format($total['pay_fee']);
        $total['tax'] = NumberHelper::price_format($total['tax']);
        $total['pay_fee_formated'] = NumberHelper::price_format($total['pay_fee_formated']);
        $total['surplus'] = NumberHelper::price_format($total['surplus']);
        $total['amount_formated'] = NumberHelper::price_format($total['amount_formated']);

        if ($total['market_price'] > 0) {
            $total['save_rate'] = bcdiv($total['saving'] * 100 , $total['market_price'], 2).'%';
        } else {
            $total['market_price'] = 0;
        }

        $summary = [];
        if (!empty($total['summary'])) {
            foreach ($total['summary'] as $key => $value) {
                $summary[] = array_merge($value, ['brand_id' => $key]);
            }
        }
        $total['summary'] = $summary;

        \Yii::trace(__METHOD__.' total='. json_encode($total));

        return [
            'total' => $total,
            'grouped_goods_list' => $brand_or_supplier_grouped_goods_list,
        ];
    }


    /**
     * 获得订单需要支付的支付费用
     *
     * @access  public
     * @param   integer $payment_id
     * @param   float   $order_amount
     * @param   mix     $cod_fee
     * @return  float
     */
    public static function payFee($payment_id, $order_amount, $cod_fee = null) {
        $payment = TouchPayment::paymentInfo($payment_id);

        $rate = ($payment->is_cod && !is_null($cod_fee)) ? $cod_fee : $payment->pay_fee;
        if (strpos($rate, '%') !== false) {
            //  支付费用是一个比例
            $val = $rate / 100;
            $pay_fee = $val > 0 ? $order_amount * $val / (1 - $val) : 0;
        } else {
            $pay_fee = $rate;
        }

        return bcmul($pay_fee, 1.00, 2);
    }

    /**
     * 获取购物车中的已选中的商品
     *
     * @param $userId
     * @param $userRank
     * @return array
     */
    public static function cartGoods($userId, $userRank)
    {
        $cartGoods = [];
        $goodsList = Goods::find()
            ->joinWith('cart')
            ->joinWith('brand')
            ->joinWith('moqs')
            ->joinWith('volumePrice')
            ->where([
                'o_goods.is_on_sale' => Goods::IS_ON_SALE,
                'o_goods.is_delete' => Goods::IS_NOT_DELETE,
                Cart::tableName().'.user_id' => $userId,
                Cart::tableName().'.selected' => 1,
            ])->all();

        if ($goodsList) {
            foreach ($goodsList as $goods) {
                $gift = Event::getGiftForSingleGoods($goods->goods_id, $goods['cart']['goods_number']);

                //  如果有moq 修正用户的起售数量
                if (!empty($goods['moqs'])) {
                    foreach ($goods['moqs'] as $moq) {
                        if ($moq['user_rank'] == $userRank) {
                            $goods->start_num = $moq['moq'];
                            if ($goods->start_num > $goods['cart']['goods_number']) {
                                \Yii::trace(__CLASS__.' | '.__FUNCTION__.' | 当前商品的起售数量为'.$goods->start_num.
                                    ' | goodsId:'.$goods->goods_id.' | goodsNumber:'.$goods['cart']['goods_number']);
                                continue;
                            }
                        }
                    }
                }

                $brandId = 0;
                if (!empty($goods['brand'])) {
                    $brandId = $goods['brand']['brand_id'];
                    if (!$goods->shipping_id) {
                        $goods->shipping_id = $goods['brand']['shipping_id'];
                    }
                }
                if ($goods->shipping_id > 0) {
                    $shipping_id = (int)$goods->shipping_id;
                } elseif ($goods['brand']['shipping_id'] > 0) {
                    $shipping_id = (int)$goods['brand']['shipping_id'];
                } else {
                    $shipping_id = (int)Shipping::getDefaultShippingId();
                }


                //  修正价格
                $goods_price = Goods::getGoodsPriceForBuy($goods->goods_id, $goods['cart']['goods_number'], $userRank);
                Yii::trace('修正价格 Goods::getGoodsPriceForBuy('.$goods->goods_id.', '.$goods['cart']['goods_number'].', '.
                    $userRank.') $goods_price = '.$goods_price);
                //  o_goods.shipping_code 当前未启用
                $currentShippingCode = Shipping::getShippingCodeById($shipping_id);
                $shipping_fee_format = OrderHelper::getShippingCode($goods);

                $cartGoods[] = [
                    'code' => 0,
                    'goods_id' => (int)$goods->goods_id,
                    'goods_sn' => $goods->goods_sn,
                    'goods_name' => $goods->goods_name,
                    'goods_number' => (int)$goods['cart']['goods_number'],
                    'goods_number_max' => (int)$goods->goods_number,
                    'goods_price' => $goods_price,  //  计算优惠活动之前的结算价格
                    'pay_price' => $goods_price,    //  计算优惠活动之后的实际购买价格
                    'gift' => $gift,  //  赠品
                    'brand_id' => (int)$brandId,  //  品牌ID
                    'brand_name' => $goods['brand']['brand_name'],  //  品牌名称
                    'supplier_user_id' => $goods->supplier_user_id,  //  供应商ID
                    'is_gift' => 0,  //  是否赠品
                    'goods_weight' => $goods->goods_weight,  //  配送方式
                    'is_real' => (int)$goods->is_real,  //  是否是真实商品
                    'shipping_id' => (int)$goods->shipping_id,  //
                    'market_price' => $goods->market_price,  //  市场价
                    'goods_thumb' => ImageHelper::get_image_path($goods->goods_thumb),  //  缩略图
                    'shipping_id' =>  $shipping_id,  //  配送模板ID
                    'shipping_code' => $currentShippingCode,    //  运费code
                    'shipping_fee_format' => $shipping_fee_format,
                    'selected' => 1,    //  购物车中的状态是被勾选
                    'event_id' => 0,    //  购物车中的状态是被勾选
                ];
            }
        }

        return $cartGoods;
    }


    /**
     * 通过 pay_code 获取支付方式的 pay_id
     * 分平台 在不同的表中获取
     * @param $payCode  string
     * @return int
     */
    public static function getPaymentId($payCode, $platForm)
    {
        switch ($platForm) {
            case 'm':
                $rs = Payment::find()->select(['pay_id'])->where(['pay_code' => $payCode])->one();
                break;
            case 'pc':
                $rs = TouchPayment::find()->select(['pay_id'])->where(['pay_code' => $payCode])->one();
                break;
            case 'ios':
                //  ios 是否支持微信支付，如果支持，需要修改支付逻辑
                $rs = Payment::find()->select(['pay_id'])->where(['pay_code' => $payCode])->one();
                break;
            default :
                break;
        }

        if ($rs && $rs->pay_id) {
            return $rs->pay_id;
        } else {
            if ($platForm != 'm') {
                return 1;   //  其他平台默认支付宝支付
            } else {
                return 3;   //  微信站默认微信支付
            }

        }
    }

    /**
     * 生成唯一的订单号
     *
     * 如果生成的订单号已存在，则重新生成
     */
    public static function getUniqidOrderSn()
    {
        mt_srand((double) microtime() * 1000000);
        $orderSn = date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);

        $order = OrderInfo::find()->where(['order_sn' => $orderSn])->one();
        if ($order) {
            return self::getUniqidOrderSn();
        } else {
            return $orderSn;
        }
    }

    /**
     * 减库存
     */
    public static function changeOrderGoodsStorage($orderId)
    {
        //  查询订单商品信息
        $oTb = OrderGoods::tableName();
        $gTb = Goods::tableName();
        $orderGoods = OrderGoods::find()
            ->joinWith('goods')
            ->where(['order_id' => $orderId])
            ->all();

        $map = [];
        if ($orderGoods) {
            //  有赠品可能与购买的商品 是同款SKU
            foreach ($orderGoods as $goods) {
                //  第一次遍历到的商品，获取库存
                if (!isset($map[$goods->goods_id])) {
                    $map[$goods->goods_id] = $goods['goods']['goods_number'];  //  商品库存
                }
                $map[$goods->goods_id] = $map[$goods->goods_id] - ($goods->goods_number - $goods->send_number);

                if ($map[$goods->goods_id] < 0) {
                    return [
                        'code' => 2,
                        'msg' => $goods->goods_name.'库存不足',
                    ];
                }

                $goodsModel = Goods::find()->where(['goods_id' => $goods->goods_id])->one();
                $goodsModel->setAttribute('goods_number', $map[$goods->goods_id]);

            }

            foreach ($map as $goods_id => $goods_number) {
                if ($goods_number < 0) {
                    return [
                        'code' => 2,
                        'msg' => '',
                    ];
                }
            }

        } else {
            return [
                'code' => 1,
                'msg' => '没有获取到订单的商品信息',
            ];
        }

    }

    /**
     * 根据不同的第三方支付生成不同的out_trade_no
     * @param $orderList
     * @param $paycode
     * @return bool|string
     */
    public static function generateOutTradeNo($orderList, $paycode) {
        if ($paycode == 'alipay') {
            $slice = 'A';
        }
        else if ($paycode == 'wxpay') {
            $slice = 'O';
        }
        else {
            return false;
        }
        $out_trade_no = $orderList[0]['order_sn'] . $slice . $orderList[0]->paylog->log_id . $slice . (time() - date('Z'));
        return $out_trade_no;
    }

    /**
     * 获取商品的配送方式名称
     *
     * 调用getShippingCode 方法前， $goods model中已经修正shipping_id（即$goods->shipping_id为空调用$goods->brand['shipping_id']）
     * @param $goods
     * @return string
     */
    public static function getShippingCode($goods)
    {
        $format_free = '包邮';
        $format_fpd = '运费到付';
        $format_mix = '部分运费到付';

        $currentShippingCode = Shipping::getShippingCodeById($goods->shipping_id);

        switch ($currentShippingCode) {
            case 'fpd':
                $shipping_fee_format = $format_fpd;
                break;
            default:
                $shipping_fee_format = $format_free;
                break;
        }

        return $shipping_fee_format;
    }
}