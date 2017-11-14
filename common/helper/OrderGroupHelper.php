<?php
/**
 * Created by PhpStorm.
 * User: clark
 * Date: 2017/1/17
 * Time: 15:29
 */

namespace common\helper;

use \Yii;
use common\models\ArrivalReminder;
use common\models\GiftPkg;
use common\models\ShippingArea;
use common\models\Payment;
use common\models\payment\PayLog;
use common\models\payment\TouchPayment;
use common\models\Cart;
use common\models\CouponRecord;
use common\models\Event;
use common\models\Goods;
use common\models\OrderGoods;
use common\models\OrderGroup;
use common\models\OrderInfo;
use common\models\GoodsActivity;
use common\models\Shipping;
use common\models\UserAddress;
use common\models\Users;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\ServerErrorHttpException;

class OrderGroupHelper
{
    /**
     * 生成唯一的支付单号（总单号）
     *
     * 如果生成的订单号已存在，则重新生成
     */
    public static function getUniqidGroupId($userId, $date = null)
    {
        mt_srand((double) microtime() * 1000000 + $userId);

        if (empty($date)) {
            $date = date('Ymd');
        }

        $groupId = $date . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);

        //  判重
        $record = OrderGroup::find()->where(['group_id' => $groupId])->one();
        if (!empty($record)) {
            return self::getUniqidGroupId($userId, $date);
        } else {
            return $groupId;
        }
    }

    /**
     * 选择一个随机的方案 生成子单的唯一编码
     * 判重，如果orderSn 重复，递归
     * @return string
     */
    public static function getUniqueOrderSn($date = null)
    {
        mt_srand((double) microtime() * 1000000);

        if (empty($date)) {
            $date = date('Ymd');
        }

        $orderSn = $date . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);

        //  判重
        $record = OrderInfo::find()->where(['order_sn' => $orderSn])->one();
        if (!empty($record)) {
            return self::getUniqueOrderSn($date);
        } else {
            return $orderSn;
        }
    }

    /**
     * 校验订单是否可以支付
     *
     * 当前只校验秒杀活动
     * @param $orderList    通过order_id 获取的 [$order] 或 通过group_id 获取的 $orderList
     * @return array        [code => 0 可以支付，1不可以支付, msg=不能支付的原因]
     */
    public static function checkPayable($orderList, $userId)
    {
        Yii::warning(__FILE__.'::'.__FUNCTION__.' 入参 $orderList = '.json_encode($orderList).', $userId = '.$userId);
        $rs = [];

        if (empty($orderList)) {
            $rs = [
                'code' => 1,
                'msg' => '无效订单，不能支付。',
                'redirectMsg' => '待支付订单列表',
            ];
        } else {
            $payStatusList = array_column($orderList, 'pay_status');
            $payStatusList = array_unique($payStatusList);
            //  已支付订单不能重复提交
            if (count($payStatusList) == 1 && $payStatusList[0] != OrderInfo::PAY_STATUS_UNPAYED) {
                $rs = [
                    'code' => 2,
                    'msg' => '该订单已支付过，不需要重复提交。',
                    'redirectMsg' => '订单列表'
                ];
            } else {
                //  任意一个子单的状态不是未支付，则不能整单支付
                foreach ($payStatusList as $value) {
                    if ($value != OrderInfo::PAY_STATUS_UNPAYED) {
                        $rs = [
                            'code' => 3,
                            'msg' => '总单中存在已支付的子单，不能整单支付。',
                            'redirectMsg' => '待支付订单列表'
                        ];

                        break;
                    }
                }

                if (!empty($rs)) {
                    //  $rs 有判定结果则不继续执行。 统一在结尾返回，便于输出日志
                }
                //  团采、秒杀 订单是 单个订单 单个order_goods 只会遍历一次；
                elseif (count($orderList) == 1 && in_array($orderList[0]['extension_code'], ['flash_sale', 'group_buy'])) {
                    $order = $orderList[0];
                    if ($order['extension_id'] != 0) {
                        $activityInfo = GoodsActivity::find()
                            ->joinWith('goods')
                            ->joinWith('orderInfo')
                            ->joinWith('orderGoods')
                            ->where(['act_id' => $order['extension_id']])
                            ->asArray()
                            ->one();
                        Yii::warning(__LINE__.' $activityInfo = '.json_encode($activityInfo));

                        //  计算活动的销量
                        $countRs = GoodsActivityHelper::getCount(
                            $activityInfo['orderInfo'],
                            $activityInfo['orderGoods'],
                            $userId
                        );
                        Yii::warning(__LINE__.' $countRs = '.json_encode($countRs));

                        //  计算用户当前的最大可购买数量
                        if ($countRs['saleCount'] > 0) {
                            $activityInfo['match_num'] -= $countRs['payCount'];

                            //  如果当前用户有当前活动的有效订单
                            if (!empty($countRs['userPayCount'])) {
                                $activityInfo['limit_num'] -= $countRs['userPayCount'];
                            }
                        }

                        $currentUserCanBuyMax = min(
                            $activityInfo['match_num'],
                            $activityInfo['limit_num'],
                            $activityInfo['goods']['goods_number']
                        );

                        if ($currentUserCanBuyMax < 0) {
                            $currentUserCanBuyMax = 0;
                        }

                        //  获取用户当前要支付的商品数量
                        if (!empty($orderItem['order_id']) && !empty($activityInfo['goods']['goods_id'])) {
                            $orderGoods = OrderGoods::find()
                                ->select(['goods_number'])
                                ->where([
                                    'order_id' => $orderItem['order_id'],
                                    'goods_id' => $activityInfo['goods']['goods_id'],
                                ])->one();

                            if (!empty($orderGoods)) {

                                if ($currentUserCanBuyMax >= $orderGoods->goods_number) {
                                    $rs = [
                                        'code' => 0,
                                        'msg' => '可以支付。'
                                    ];
                                } else {
                                    $rs = [
                                        'code' => 4,
                                        'msg' => '当前订单商品的最大可够买数量为'.$currentUserCanBuyMax.',请重新下单。',
                                        'redirectMsg' => '重新下单',
                                        'extensionId' => $order->extension_id
                                    ];
                                }

                            }
                        }
                    }

                    //  搂底，没有判定到情况都是无效订单
                    if (!empty($rs)) {
                        $rs = [
                            'code' => 1,
                            'msg' => '无效订单，不能支付',
                            'redirectMsg' => '待支付订单列表',
                        ];
                    }
                }
                //  遍历子单，判定是否可以支付
                else {

                    $orderIdList = ArrayHelper::getColumn($orderList, 'order_id');

                    $orderGoods = OrderGoods::find()
                        ->joinWith('goods')
                        ->where([
                            'order_id' => $orderIdList,
                            'is_gift' => OrderGoods::IS_GIFT_NO
                        ])->all();
                    foreach ($orderGoods as $item) {
                        //  如果购物车的商品数量 > 商品库存,则不能支付
                        Yii::warning('goods_number = '. $item->goods_number. ', goods->goods_number = '. $item['goods']['goods_number']);
                        if ($item->goods_number > $item->goods['goods_number']) {
//                            OrderGroup::cancelOrderAndAddToCart($orderList[0]['group_id'], '订单中部分商品的购买数量超出当前库存');
                            $rs = [
                                'code' => 5,
                                'msg' => '订单中部分商品的购买数量超出当前库存，系统已自动把您订单中的商品放回购物车,请重新下单。',
                                'redirectMsg' => '重新下单',
                                'backToCart' => 1,
                                'backToCartMsg' => '订单中部分商品的购买数量超出当前库存',
                            ];

                            break;
                        }
                    }

                    if (empty($rs)) {
                        $rs = [
                            'code' => 0,
                            'msg' => '可以支付。'
                        ];
                    }
                }
            }
        }

        Yii::warning('判定 团采/秒杀 订单是否可以支付 $rs = '.json_encode($rs), __METHOD__);
        return $rs;
    }

    /**
     * @param int $userId           用户ID
     * @param str $extensionCode    购买类型
     * @param obj/arr $validAddress 有效的收获地址
     * @param int $prepay           是否现付运费
     * @param int $couponId         用户选择的优惠券ID
     * @param array $extParams = [
     *  'buy_goods_id' => 0,
     *  'buy_goods_num' => 0,
     *  'pkg_id' => 0,
     *  'pkg_num' => 0,
     *  'act_id' => 0
     * ]
     * @return array
     */
    public static function checkoutGoods($userId, $extensionCode, $validAddress, $prepay, $couponId, $extParams)
    {
        $rs = [];

        $extParamsDefault = [
            'buy_goods_id' => 0,
            'buy_goods_num' => 0,
            'pkg_id' => 0,
            'pkg_num' => 0,
            'act_id' => 0,
            'goodsGroup' => [],
        ];
        $extParams = array_merge($extParamsDefault, $extParams);

        switch ($extensionCode) {
            //  购物车购买
            case 'general':
                $rs = OrderGroupHelper::checkoutCart($userId, $validAddress, $prepay, $couponId);
                break;
            //  普通商品立即购买
            case 'general_buy_now':
                $rs = OrderGroupHelper::checkoutBuyNow(
                    $userId,
                    $validAddress,
                    $extParams['buy_goods_id'],
                    $extParams['buy_goods_num'],
                    $prepay,
                    $couponId
                );
                break;
            //  一组商品立即购买
            case 'general_batch':
                $rs = OrderGroupHelper::checkoutBatchGoods(
                    $userId,
                    $validAddress,
                    $extParams['goodsGroup'],
                    $prepay,
                    $couponId
                );
                break;
            //  团采
            case 'group_buy':
                $rs = OrderGroupHelper::checkoutGroupBuy(
                    $userId,
                    $validAddress,
                    $extParams['act_id'],
                    $extParams['buy_goods_num'],
                    $prepay
                );
                break;
            //  秒杀
            case 'flash_sale':
                $rs = OrderGroupHelper::checkoutFlashSale(
                    $userId,
                    $validAddress,
                    $extParams['act_id'],
                    $extParams['buy_goods_num'],
                    $prepay
                );
                break;
            //  积分兑换
            case 'integral_exchange':
                $rs = OrderGroupHelper::checkoutExchange(
                    $userId,
                    $validAddress,
                    $extParams['buy_goods_id'],
                    $extParams['buy_goods_num']
                );
                break;
            //  礼包活动
            case 'gift_pkg':
                $rs = OrderGroupHelper::checkoutGiftPkg($userId, $extensionCode, $validAddress, $extParams, $prepay);
                break;
            default :
                break;
        }

        return $rs;
    }

    /**
     * 校验购物车下单
     * 【1】校验收货地址，如果没有收货地址获取默认地址
     * 【2】校验可购买商品、计算满赠、满减、优惠券活动
     * 【3】订单分组，计算运费、
     * 【4】计算满减、优惠券活动 均摊折扣
     * 【5】数据汇总
     * @param int $userId
     * @param $validAddress
     * @param int $prepay
     * @param int $couponId
     * @return array
     */
    public static function checkoutCart($userId, $validAddress, $prepay = 0, $couponId = 0)
    {
        $extensionCode = 'general';
        //  【1】校验可购买商品
        Cart::check($userId);  //  不满足购买条件的商品的取消勾选状态

        $userRankDiscount = Users::getUserRankDiscount($userId);
        $cartGoodsRs = self::cartGoods('checkout', $userId, $userRankDiscount, $couponId);

        $cartGoods = $cartGoodsRs['cartGoods'];
        $total = $cartGoodsRs['total'];

        //  计算配送方式和运费
        $cartGoods = self::getOrderFee($extensionCode, $cartGoods, $validAddress, $prepay, $total, $total['selectedCut']);
        //  修正总单总价
        $totalAmount = $total['goods_amount'] + $total['shippingFee'] - $total['discount'];
        $total['totalAmount'] = NumberHelper::price_format($totalAmount);
        $total['goods_amount'] = NumberHelper::price_format($total['goods_amount']);
        $total['shippingFee'] = NumberHelper::price_format($total['shippingFee']);
        $total['discount'] = NumberHelper::price_format($total['discount']);

        return [
            'cartGoods' => $cartGoods,
            'total'     => $total,
        ];
    }

    /**
     * 校验立即下单的单个商品
     */
    public static function checkoutBuyNow($userId, $validAddress, $goodsId, $goodsNum, $prepay, $couponId = 0)
    {
        $extensionCode = 'general_buy_now';
        //  【1】校验立即购买的商品
        $goods = Goods::getGoodsInfo($goodsId, 'general', $userId, $goodsNum, $couponId);

        if ($goods->extInfo['buyGoodsNum'] > 0) {
            $cartGoodsRs = self::formatCartGoodsForBuyNow($goods);

            $cartGoods = $cartGoodsRs['cartGoods'];
            $total = $cartGoodsRs['total'];
            $total['remarks'] = $goods->extInfo['remarks'];

            //  计算配送方式和运费
            $cartGoods = self::getOrderFee($extensionCode, $cartGoods, $validAddress, $prepay, $total, $total['selectedCut']);
            //  修正总单总价
            $total['totalAmount'] = NumberHelper::price_format($total['goods_amount'] + $total['shippingFee'] - $total['discount']);
            //  修正订单总体的配送信息 如果只有小美直发，前端做特殊处理
            $orderCount = count($cartGoods);
//            $total = self::formatTotalShippingDesc($total, $orderCount, $prepay);
        } else {
            $cartGoods = [];
            $total = [];
        }

        return [
            'cartGoods' => $cartGoods,
            'total'     => $total,
        ];
    }

    /**
     * 校验立即下单的一组商品
     */
    public static function checkoutBatchGoods($userId, $validAddress, $goodsGroup, $prepay, $couponId = 0)
    {
        //  $goodsId, $goodsNum,
        $extensionCode = 'general_batch';
        if (!empty($goodsGroup)) {
            //  修正商品库存
            $checkGoods = Goods::checkGoodsList($goodsGroup, $userId);
            if (!empty($checkGoods) && $checkGoods['code'] == 0 && !empty($checkGoods['data']['resetGoodsGroup'])) {

                //  【1】校验立即购买的商品 构造类似于 购物车结算的cartGoods
                $goodsGroup = $checkGoods['data']['resetGoodsGroup'];
                $goodsMap = array_column($goodsGroup, 'goodsNum', 'goodsId');
                $goodsIdList = array_keys($goodsMap);
                $now = date('Y-m-d H:i:s');

                $goodsList = Goods::find()
                    ->joinWith([
                        'brand',
                        'brand.eventList brandEventList' => function($eventQuery) use ($now) {
                            $eventQuery->andOnCondition(['brandEventList.is_active' => Event::IS_ACTIVE])
                                ->andOnCondition([
                                    'and',
                                    ['<=', 'brandEventList.start_time', $now],
                                    ['>=', 'brandEventList.end_time', $now]
                                ]);
                        },
                        'volumePrice',
                        'eventList goodsEventList' => function($eventQuery) use ($now) {
                            $eventQuery->andOnCondition(['goodsEventList.is_active' => Event::IS_ACTIVE])
                                ->andOnCondition([
                                    'and',
                                    ['<=', 'goodsEventList.start_time', $now],
                                    ['>=', 'goodsEventList.end_time', $now]
                                ]);
                        },
                        'eventList.fullCutRule',
                    ])->where([
                        Goods::tableName().'.is_on_sale' => Goods::IS_ON_SALE,
                        Goods::tableName().'.is_delete' => Goods::IS_NOT_DELETE,
                        Goods::tableName().'.goods_id' => $goodsIdList,
                    ])->all();

                //  【2】遍历购物车中的商品，按子单分组
                if ($goodsList) {
                    $total = [
                        'cartAllSelected' => true,   //  默认购物车商品全部选中
                        'goods_amount' => 0.00,   //  默认购物车商品全部选中
                        'remarks' => '',   //  默认购物车商品全部选中
                        'discount' => 0.00,   //  默认没有优惠
                        'shippingFee' => 0.00,   //  默认没有运费
                        'totalAmount' => 0.00,   //  默认总金额，每次用之前重新计算 = goods_amount + shippingFee - discount
                        'selectedCut' => [],
                    ];

                    $userRankDiscount = Users::getUserRankDiscount($userId);
                    $rs = self::formatCartGoodsForGeneralBatch($goodsList, $goodsMap, $userRankDiscount);
                    $cartGoods = $rs['cartGoods'];
                    $total = array_merge($total, $rs['total']);
                }

                //  【3】汇总商品参与的所有优惠活动，计算最大优惠
                $cartGoodsRs = self::handleEventList($total, $cartGoods, $userId, $couponId);

                $cartGoods = $cartGoodsRs['cartGoods'];
                $total = $cartGoodsRs['total'];

                //  计算配送方式和运费
                $cartGoods = self::getOrderFee($extensionCode, $cartGoods, $validAddress, $prepay, $total, $total['selectedCut']);
                //  修正总单总价
                $totalAmount = $total['goods_amount'] + $total['shippingFee'] - $total['discount'];
                $total['totalAmount'] = NumberHelper::price_format($totalAmount);
                $total['goods_amount'] = NumberHelper::price_format($total['goods_amount']);
                $total['shippingFee'] = NumberHelper::price_format($total['shippingFee']);
                $total['discount'] = NumberHelper::price_format($total['discount']);

                $rs =  [
                    'cartGoods' => $cartGoods,
                    'total'     => $total,
                ];

            }
            else {
                Yii::warning('缺少有效商品 $checkGoods = '.json_encode($checkGoods), __METHOD__);
                $rs = [
                    'cartGoods' => [],
                    'total'     => [],
                    'msg'       => '没有可购买的商品'
                ];
            }

        } else {
            Yii::warning('缺少必要参数，请选择要下单的商品', __METHOD__);
            $rs = [
                'cartGoods' => [],
                'total'     => [],
            ];
        }

        return $rs;
    }
    /**
     * 校验积分兑换下单
     * 【1】校验收货地址，如果没有收货地址获取默认地址
     */
    public static function checkoutExchange($userId, $validAddress, $goodsId, $goodsNum)
    {
        $extensionCode = 'integral_exchange';
        //  【1】校验立即购买的商品
        $goods = Goods::getGoodsInfo($goodsId, 'integral_exchange', $userId, $goodsNum);

        if ($goods->extInfo['buyGoodsNum'] > 0) {
            $cartGoodsRs = self::formatCartGoodsForBuyNow($goods);

            $cartGoods = $cartGoodsRs['cartGoods'];
            $total = $cartGoodsRs['total'];
            $total['remarks'] = $goods->extInfo['remarks'];

            //  计算配送方式和运费
            $cartGoods = self::getOrderFee($extensionCode, $cartGoods, $validAddress, 0, $total, $total['selectedCut']);
            //  修正总单总价
            $total['totalAmount'] = NumberHelper::price_format($total['goods_amount'] + $total['shippingFee'] - $total['discount']);
            //  修正订单总体的配送信息 如果只有小美直发，前端做特殊处理
//            $orderCount = count($cartGoods);
//            $total = self::formatTotalShippingDesc($total, $orderCount);

            if ($goods->supplier_user_id == 1257) {
                $total['isDirectOnly'] = true;
            } else {
                $total['isDirectOnly'] = false;
            }
            $total['shippingDesc'] = '运费到付';
            $total['shippingFeeDesc'] = '到付';

            $userModel = Users::find()->select(['user_rank'])->where(['user_id' => $userId])->one();
            if ($goods->need_rank <= $userModel->user_rank) {
                $total['canBuy'] = true;
            } else {
                $total['canBuy'] = false;
            }

            //  积分兑换不显示
            $total['userRankSavePrice'] = 0.00;
        } else {
            $cartGoods = [];
            $total = [];
        }




        return [
            'cartGoods' => $cartGoods,
            'total'     => $total,
        ];
    }

    /**
     * 校验团采下单   不支持 满赠、满减、优惠券活动
     * 【1】校验收货地址，如果没有收货地址获取默认地址
     * 【2】校验团采商品
     * 【3】计算活动
     */
    public static function checkoutGroupBuy($userId, $validAddress, $actId, $goodsNum, $prepay)
    {
        $extensionCode = 'group_buy';
        //  【1】校验团采商品
        $activityGoods = GoodsActivity::getActivityInfo($actId, $goodsNum, $userId);
        Yii::warning('$activityGoods = '.VarDumper::export($activityGoods));
        if (!empty($activityGoods) && $activityGoods->extInfo['buyNum'] > 0) {
            $cartGoodsRs = self::formatActivityGoodsForBuyNow($activityGoods);
            $cartGoods = $cartGoodsRs['cartGoods'];
            $total = $cartGoodsRs['total'];

            //  计算配送方式和运费
            $cartGoods = self::getOrderFee($extensionCode, $cartGoods, $validAddress, $prepay, $total);
            //  修正总单总价
            $total['totalAmount'] = NumberHelper::price_format($total['goods_amount'] + $total['shippingFee'] - $total['discount']);

            //  修正订单总体的配送信息 如果只有小美直发，前端做特殊处理
//            $total = self::formatShippingDesc4GoodsActivity($total, $cartGoods);

            //  团采秒杀不参与会员折扣
            $total['userRankSavePrice'] = 0.00;
        } else {
            $cartGoods = [];
            $total = [];
        }

        return [
            'cartGoods' => $cartGoods,
            'total'     => $total,
        ];
    }

    /**
     * 校验秒杀下单
     * 【1】校验收货地址，如果没有收货地址获取默认地址
     * 【2】校验团采商品
     */
    public static function checkoutFlashSale($userId, $validAddress, $actId, $goodsNum, $prepay)
    {
        $extensionCode = 'flash_sale';
        //  【1】校验团采商品
        $activityGoods = GoodsActivity::getActivityInfo($actId, $goodsNum, $userId);
        Yii::warning('$activityGoods = '.VarDumper::dumpAsString($activityGoods));
        if (!empty($activityGoods) && $activityGoods->extInfo['buyNum'] > 0) {
            $cartGoodsRs = self::formatActivityGoodsForBuyNow($activityGoods);
            $cartGoods = $cartGoodsRs['cartGoods'];
            $total = $cartGoodsRs['total'];

            //  计算配送方式和运费
            $cartGoods = self::getOrderFee($extensionCode, $cartGoods, $validAddress, $prepay, $total);
            //  修正总单总价
            $total['totalAmount'] = NumberHelper::price_format($total['goods_amount'] + $total['shippingFee'] - $total['discount']);

            //  修正订单总体的配送信息 如果只有小美直发，前端做特殊处理
//            $total = self::formatShippingDesc4GoodsActivity($total, $cartGoods);

            //  团采秒杀不参与会员折扣
            $total['userRankSavePrice'] = 0.00;
        } else {
            $cartGoods = [];
            $total = [];
        }

        return [
            'cartGoods' => $cartGoods,
            'total'     => $total,
        ];
    }

    /**
     * 修正 团采/秒杀 的运费说明
     * @param $total
     * @param $cartGoods
     * @return mixed
     */
    public static function formatShippingDesc4GoodsActivity($total, $cartGoods)
    {
        $goods = current($cartGoods);
        $total['shippingDesc'] = $goods['shippingDesc'];
        $total['shippingFeeDesc'] = $goods['shipping_name'];

        $total['isDirectOnly'] = false;
        if ($goods['shipping_code'] == 'fgaf') {
            $total['isDirectOnly'] = true;
            if (!empty($prepay)) {
                if (!empty($total['shippingFee'])) {
                    $total['shippingDesc'] = '现付小美直发 ¥ '.$total['shippingFee'].'运费';
                    $total['shippingFeeDesc'] = '¥ '.$total['shippingFee'].'(现付)';
                }  elseif (empty($total['shippingFee'])) {
                    //  实际上不会走到这里来
                    //  prepay参数只在预付款有值的情况下出现
                    $total['shippingDesc'] = '现付小美直发 ¥ '.$total['shippingFee'].'运费';
                    $total['shippingFeeDesc'] = '¥ '.$total['shippingFee'].'(现付)';
                }
            } elseif (empty($prepay)) {
                //  用户选择了prepay 预付运费才会统计到  $total['shippingFee']
                if (empty($total['preShippingFee'])) {
                    $total['shippingDesc'] = '小美直发包邮';
                    $total['shippingFeeDesc'] = '包邮';
                } else {
                    $total['shippingDesc'] = '小美直发(到付运费)';
                    $total['shippingFeeDesc'] = '到付';
                }

            }
        }

        return $total;
    }

    /**
     * 如果有实体商品，则需要验证收货人信息
     *
     *      有选择收货人信息 则验证指定信息;
     *      没有选择收货人信息 则验证默认收货人信息;
     *          没有默认收货人信息 则设置默认;
     *      没有有效的收获地址 则返回[]
     * @param $userId
     * @param int $addressId
     * @return array|mixed|null|\yii\db\ActiveRecord
     */
    public static function checkAddress($userId, $addressId = 0)
    {
        if (!empty($addressId) && is_numeric($addressId)) {
            //  有选择收货人信息则验证指定信息
            $address = UserAddress::getAddressBuyId($userId, $addressId);

            if (!empty($address)) {
                if ($address->check() == UserAddress::CHECK_VALID) {
                    $validAddress = $address;
                    $validAddressId = $address->address_id;
                } else {
                    $validAddressId = 0;
                }
            } else {
                $validAddressId = 0;
            }

        } else {
            // 没有选择收货人信息 则验证默认收货人信息
            $validAddress = Users::checkDeafultAddress($userId);

            if (!empty($validAddress)) {
                $validAddressId = $validAddress->address_id;
            } else {
                $validAddressId = 0;
            }
        }

        //  如果没有默认收货地址 或 默认收货地址无效，则设置最近添加的有效地址为默认
        if (empty($validAddressId)) {
            $lastAddress = UserAddress::getLastAddress($userId);

            if (!empty($lastAddress) && $lastAddress->check() == UserAddress::CHECK_VALID) {
                $validAddress = $lastAddress;
                $validAddressId = $lastAddress->address_id;
                Users::setDefaultAddress($userId, $validAddressId);
            }
        }

        if (empty($validAddressId)) {
            return [];
        } else {
            return $validAddress;
        }
    }

    /**
     * 获取购物车中的已选中的商品
     * 【1】获取购物车的商品 及关联的 品牌、梯度价格、商品属性、当前生效的活动、活动对应的规则等
     * 【2】遍历购物车中的商品，按子单分组
     * @param int $type string ['cart', 'checkout'] 区分来源 是购物车检验，还是结算检验
     * @param int $userId
     * @param float $userRankDiscount   [0 ~ 1]
     * @param int $couponId   用户指定要使用的优惠券
     * @return array
     */
    public static function cartGoods($type, $userId, $userRankDiscount, $couponId = 0)
    {
        $cartGoods = [];

        //  【1】获取购物车的商品 及关联的 品牌、梯度价格、商品属性、当前生效的活动、活动对应的规则等
        $now = date('Y-m-d H:i:s');
        $query = Cart::find()
            ->joinWith([
                'goods',
                'goods.brand',
                'goods.brand.eventList brandEventList' => function($eventQuery) use ($now) {
                    $eventQuery->andOnCondition(['brandEventList.is_active' => Event::IS_ACTIVE])
                        ->andOnCondition([
                            'and',
                            ['<=', 'brandEventList.start_time', $now],
                            ['>=', 'brandEventList.end_time', $now]
                        ]);
                },
                'goods.volumePrice',
                'goods.eventList goodsEventList' => function($eventQuery) use ($now) {
                    $eventQuery->andOnCondition(['goodsEventList.is_active' => Event::IS_ACTIVE])
                        ->andOnCondition([
                            'and',
                            ['<=', 'goodsEventList.start_time', $now],
                            ['>=', 'goodsEventList.end_time', $now]
                        ]);
                },
                'goods.eventList.fullCutRule',
//                'moqs' ——弃用 不同会员等级对应的起售数量
            ])
            ->where([
                Cart::tableName().'.user_id' => $userId
            ]);

        //  购物车校验 获取购物车中的所有商品；结算校验值获取选中的有效商品
        if ($type == 'checkout') {
            $query->andWhere([
                'o_goods.is_on_sale' => Goods::IS_ON_SALE,
                'o_goods.is_delete' => Goods::IS_NOT_DELETE,
                Cart::tableName().'.selected' => 1
            ]);
        }

        $cartList = $query->all();

        //  【2】遍历购物车中的商品，按子单分组
        if ($cartList) {
            $total = [
                'cartAllSelected' => true,   //  默认购物车商品全部选中
                'goods_amount' => 0.00,   //  默认购物车商品全部选中
                'remarks' => '',   //  默认购物车商品全部选中
                'discount' => 0.00,   //  默认没有优惠
                'shippingFee' => 0.00,   //  默认没有运费
                'totalAmount' => 0.00,   //  默认总金额，每次用之前重新计算 = goods_amount + shippingFee - discount
                'selectedCut' => [],
            ];

            $rs = self::formatCartGoodsForCart($cartList, $userRankDiscount);
            $cartGoods = $rs['cartGoods'];
            $total = array_merge($total, $rs['total']);
        }

        //  【3】汇总商品参与的所有优惠活动，计算最大优惠
        $result = self::handleEventList($total, $cartGoods, $userId, $couponId);

        return $result;
    }

    /**
     *
     * @param $total
     * @param $cartGoods
     * @param $userId
     * @param $couponId
     * @return array
     */
    public static function handleEventList($total, $cartGoods, $userId, $couponId) {
        //  处理满减活动
        $fullCuts = self::processFullCutEventList($total['fullCulEventList']);
        $total = array_merge($total, $fullCuts);
        //  处理优惠券活动
        // 【雷】优惠券支持在绑定的时候设置券的可用时段，那么券的可用时段可能在互动结束后有延续，此时，券在个人中心可用，在下单时因活动实效而变得不可用
        $couponRs = self::processCouponEventLIst($total['couponEventList'], $userId, $couponId);
        $total = array_merge($total, $couponRs);
        //  如果用户没有选择优惠券，按 优惠幅度最大的计算
        $total['remarks'] = '';
        if (empty($total['selectedCut'])) {
            $chooseMaxCut = self::chooseMaxCutEvent($total, $couponId);
            $total = array_merge($total, $chooseMaxCut);
        }
        //  满减、或选定了优惠券才计算优惠金额， 保证：购物车只计算满减的金额，没有选择优惠券时不考虑优惠券的优惠
        if (!empty($total['selectedCut']['cut'])) {
            if (
                $total['selectedCut']['event']['event_type'] == Event::EVENT_TYPE_FULL_CUT ||
                (
                    $total['selectedCut']['event']['event_type'] == Event::EVENT_TYPE_COUPON &&
                    !empty($total['selectedCut']['coupon_id']) &&
                    $total['selectedCut']['coupon_id'] == $couponId
                )
            ) {
                $total['discount'] = $total['selectedCut']['cut'];
            }
        }

        //  如果用户没有执行要使用的优惠券且有命中满减优惠(购物车中)，则相关商品添加 满减标记
        if (!empty($total['fullCulGoodsIdList']) || !empty($total['couponGoodsIdList'])) {
            foreach ($cartGoods as $key => $item) {
                foreach ($item['goodsList'] as $k => $goods) {
                    //  满减标记
                    if (in_array($goods['goods_id'], $total['fullCulGoodsIdList'])) {
                        $cartGoods[$key]['goodsList'][$k]['fullCut'] = true;
                    } else {
                        $cartGoods[$key]['goodsList'][$k]['fullCut'] = false;
                    }
                    //  优惠券标记
                    if (in_array($goods['goods_id'], $total['couponGoodsIdList'])) {
                        $cartGoods[$key]['goodsList'][$k]['coupon'] = true;
                    } else {
                        $cartGoods[$key]['goodsList'][$k]['coupon'] = false;
                    }

                    if ($goods['selected'] == Cart::IS_NOT_SELECTED) {
                        $total['cartAllSelected'] = false;
                    }
                }
            }
        }

        return [
            'cartGoods' => $cartGoods,
            'total' => $total,
        ];
    }

    /**
     * 获取满赠活动的赠品
     *
     * 不支持同一商品同时参与多个满赠活动，如果同事参与多个，则只保留最后一个赠品入库
     * @param obj $goods    结算商品
     * @param obj $event    结算商品参与的满赠活动
     * @param int $buyNum   结算商品的数量
     *
     * @return array
     */
    public static function getGiftInfo($goods, $event, $buyNum) {
        $giftList = [];
        $ruleList = $event->fullGiftRule;

        if (empty($ruleList)) {
            return [];
        }

        $activieRule = 0;   //  当前满足最小满赠/物料配比 数量的规则数
        foreach ($ruleList as $rule) {
            $giftModel = $rule->gift;

            //  如果赠品不存在或 赠品库都存低于满赠 赠送的最小数量 则满赠活动失效
            if (!empty($giftModel)) {
                //  计算赠品数量
                if ($rule->match_value) {
                    if ($giftModel->goods_number > $rule->gift_num) {
                        $activieRule += 1;
                    }

                    $giftNum = floor($buyNum / $rule->match_value) * $rule->gift_num;

                    //  区分赠品是否是自身，修正最大可赠送数量
                    if ($goods->goods_id != $rule->gift->goods_id) {
                        $giftNum = min($giftNum, $rule->gift->goods_number);
                        $stock = $rule->gift->goods_number;
                    } else {
                        $stock = floor($rule->gift->goods_number / ($rule->match_value + $rule->gift_num)) * $rule->gift_num;
                        $giftNum = min($giftNum, $stock);
                    }
                } else {
                    $giftNum = 0;
                }

                $isGift = OrderGoods::isGift($event->event_type);

                $gift = [
                    'goods_id' => (int)$giftModel->goods_id,
                    'goods_name' => $giftModel->goods_name,
                    'goods_sn' => $giftModel->goods_sn,
                    'product_id' => 0,
                    'goods_number' => (int)$giftNum,
                    'giftNum' => (int)$giftNum,
                    'stock' => (int)$stock,
                    'market_price' => $giftModel->market_price,
                    'goods_price' => $rule->gift_need_pay,   //  这里是否应该显示商品的价值(o_goods.shop_price)
                    'expire_date' => $giftModel->expire_date,
                    'goods_attr' => '',
                    'is_real' => $giftModel->is_real,
                    'extension_code' => $giftModel->extension_code,
                    'parent_id' => $goods->goods_id,
                    'is_gift' => $isGift,
                    'event_id' => $event->event_id,
                    'pay_price' => $rule->gift_need_pay,    //  计算优惠活动之后的实际购买价格

                    'goods_weight' => $giftModel->goods_weight,
                    'goods_thumb' => ImageHelper::get_image_path($giftModel->goods_thumb),
                    'goods_img' => ImageHelper::get_image_path($giftModel->goods_img),
                    'event_id' => 0,    //  购物车中的状态是被勾选
                    'event_desc' => $event->event_desc,
                    'event_name' => $event->event_name,

                    'measure_unit' => $goods->measure_unit ? (string)$goods->measure_unit : '件',
                    'giftEnough' => true,   //  默认赠品充足，如果赠品不足，不修改商品库存；如果赠品是商品自身，要修改
                    'giftAmount' => NumberHelper::price_format($giftNum * $rule->gift_show_peice),
                ];

                //  【赠完即止】如果赠品数量不足，不影响购买，标记赠品是否足够
                if ($goods->goods_id == $rule->gift_id && ($buyNum + $giftNum) > $goods->goods_number) {
                    $gift['giftEnough'] = false;
                    $gift['goods_number'] = (int)($goods->goods_number - $buyNum);
                } elseif ($giftNum > $giftModel->goods_number) {
                    $gift['giftEnough'] = false;
                    $gift['goods_number'] = $giftModel->goods_number;
                }

                $giftList[] = $gift;
            }

        }

        //  没有符合最低赠送数量的 规则，则活动实效
        if (empty($activieRule)) {
            $event->is_active = Event::IS_NOT_ACTIVE;
            $event->save();
        }

        return $giftList;
    }

    /**
     * 获取满减活动的达成情况: 可减额度，提示语
     * @param array $fullCut  满减活动的详情 | event/valid-events 结果集的 $validEvents['data']['fullCut'] 的子元素
     * @return string
     */
    public static function getFullCutMsg($fullCut)
    {
        $event = $fullCut['event'];
        $cut = 0;
        $cutMsg = '';
        $notMatchMsg = '';
        foreach ($event->fullCutRule as $rule) {
            if ( (bccomp($fullCut['sumPrice'], $rule['above']) >= 0) && bccomp($rule['cut'], $cut, 2) ) {
                $cut = NumberHelper::price_format($rule['cut']);
                $fullCut['match'] = $rule;
                $cutMsg = '优惠 ¥'.$cut;
            } else {
                if (!isset($fullCut['notMatch'])) {
                    $fullCut['notMatch'] = $rule;
                } elseif (bccomp($fullCut['notMatch']['above'], $rule['above'], 2)) {
                    $fullCut['notMatch'] = $rule;
                }
                $sub = $fullCut['notMatch']['above'] - $fullCut['sumPrice'];
                $notMatchMsg = ', 再购 ¥'.NumberHelper::price_format($sub).
                    ' 可优惠 <em>¥'.NumberHelper::price_format($fullCut['notMatch']['cut']).' </em>';
            }
        }

        $fullCut['cut'] = $cut;
        $fullCut['sumPrice'] = NumberHelper::price_format($fullCut['sumPrice']);

        //  如果当前满减商品已满足最大数量，则不显示 去凑单的链接
        if (!empty($fullCut['notMatch'])) {
            $fullCut['fullCutMsg'] = '已购满减专区产品 ¥'.$fullCut['sumPrice'].$notMatchMsg;

            $fullCut['mFullCutMsg'] = '<a href="default/activity/hot.html?activeIndex=4">'.
                $fullCut['fullCutMsg'].
                '<span>去凑单></span></a>';
//            $fullCut['pcFullCutMsg'] = $fullCut['fullCutMsg'].'<a href="/topic_full_cut.php">去凑单></a>';
            $fullCut['pcFullCutMsg'] = '<a href="/topic_full_cut.php">去凑单>'.
                $fullCut['fullCutMsg'].
                '<span>去凑单></span></a>';
        } else {
            $fullCut['mFullCutMsg'] = '已购满减专区产品 ¥'.$fullCut['sumPrice'].' '.$cutMsg;
            $fullCut['pcFullCutMsg'] = '已购满减专区产品 ¥'.$fullCut['sumPrice'].' '.$cutMsg;
        }

        return $fullCut;
    }

    /**
     * 格式化 直接购买的商品信息， 与购物车购买的信息输出一致
     * @param obj/array $goods
     * @return mixed
     */
    public static function formatCartGoodsForBuyNow($goods)
    {
        $userRankSavePrice = 0.00;
        $goodsSubTotal = $goods->extInfo['goods_price'] * $goods->extInfo['buyGoodsNum'];   // 小计
        if (!empty($goods->extInfo['userRankSavePrice'])) {
            $userRankSavePrice = $goods->extInfo['userRankSavePrice'] * $goods->extInfo['buyGoodsNum'];
        }
        $userRankSavePrice = NumberHelper::price_format($userRankSavePrice);
//        $goods_price = $goods->extInfo['goods_price'];
        if ($goods->extension_code == Goods::GENERAL) {
            $goodsSubTotal = NumberHelper::price_format($goodsSubTotal);
//            $goods_price = NumberHelper::price_format($goods_price);
        } elseif ($goods->extension_code == Goods::INTEGRAL_EXCHANGE) {
            $goodsSubTotal = (int)$goodsSubTotal;
//            $goods_price = (int)$goods_price;
        }

        //  修正配送方式  在 Goods::FormatGoodsInfoForBuy() 中处理过

        $cartGoodsItem = [
            'goods_id' => (int)$goods->goods_id,
            'goods_name' => (string)$goods->goods_name,
            'goods_sn' => (string)$goods->goods_sn,
            'product_id' => 0,
            'goods_number' => (int)$goods->extInfo['buyGoodsNum'],
            'market_price' => (string)$goods->market_price,
            'goods_price' => NumberHelper::price_format($goods->extInfo['goods_price']),   //  计算优惠活动之前的结算价格
            'goods_attr' => '',
            'is_real' => (int)$goods->is_real,
            'extension_code' => (string)$goods->extension_code,
            'parent_id' => 0,
            'is_gift' => OrderGoods::IS_GIFT_NO,
            'event_id' => 0,
            'pay_price' => NumberHelper::price_format($goods->extInfo['goods_price']),    //  计算优惠活动之后的实际购买价格
            'goodsSubTotal' => NumberHelper::price_format($goodsSubTotal),

            'selected' => (int)Cart::IS_SELECTED,//小计
            'gift' => !empty($goods->extInfo['giftList']) ? $goods->extInfo['giftList'] : [],  //  赠品
            'wuliaoList' => !empty($goods->extInfo['wuliaoList']) ? $goods->extInfo['wuliaoList'] : [],  //  赠品
            'brand_name' => (string)$goods->brand['brand_name'],  //  品牌名称
            'brand_id' => (int)$goods->brand_id,  //  品牌ID
            'supplier_user_id' => (int)$goods->supplier_user_id,  //  供应商ID

            'goods_weight' => NumberHelper::weightFormat($goods->goods_weight),
            'goods_thumb' => ImageHelper::get_image_path($goods->goods_thumb),  //  缩略图
            'goods_img' => ImageHelper::get_image_path($goods->goods_img),
            'sample' => $goods->extInfo['goodsAttrFormat']['sample']
                ? (string)$goods->extInfo['goodsAttrFormat']['sample']
                : ' ',   //  物料配比
            'canSelect' => 1,   //  商品是否可勾选
            'goodsMaxCanBuy' => (int)$goods->goods_number,
            'buy_by_box' => (int)$goods->buy_by_box,
            'number_per_box' => (int)$goods->number_per_box,
            'errorMsg' => '',

            'fullCut' => false,
            'measure_unit' => $goods->measure_unit ? (string)$goods->measure_unit : '件',
        ];
        if ($cartGoodsItem['supplier_user_id'] > 0) {
            $brandOrSupplierId = (int)$cartGoodsItem['supplier_user_id'];
        } else {
            $brandOrSupplierId = (int)$cartGoodsItem['brand_id'];
        }

        //  标记是否参与满减
        if (
            !empty($goods->extInfo['selectedCut']) &&
            $goods->extInfo['selectedCut']['event']['event_type'] == (string)Event::EVENT_TYPE_FULL_CUT
        ) {
            $cartGoodsItem['fullCut'] = true;
        }

        $cartGoods[$brandOrSupplierId] = $goods['extInfo'];
        $cartGoods[$brandOrSupplierId]['goodsList'][] = $cartGoodsItem;
        $cartGoods[$brandOrSupplierId]['hasValidGoods'] = 1;

        //  修正支付商品的品牌名称
        if ($brandOrSupplierId == '1257') {
            $cartGoods[$brandOrSupplierId]['brand_name'] = '小美直发';
            $cartGoods[$brandOrSupplierId]['brand_id'] = 0;
            $cartGoods[$brandOrSupplierId]['supplier_user_id'] = 1257;
        } else {
            $cartGoods[$brandOrSupplierId]['brand_name'] = (string)$goods->brand['brand_name'];
            $cartGoods[$brandOrSupplierId]['brand_id'] = (int)$brandOrSupplierId;
            $cartGoods[$brandOrSupplierId]['supplier_user_id'] = 0;
        }
        $cartGoods[$brandOrSupplierId]['allSelected'] = Cart::IS_SELECTED;
        $cartGoods[$brandOrSupplierId]['goodsWeight'] = (string)$goods['goods_weight'];

        if (!empty($goods->supplier_user_id) && $goods->supplier_user_id == 1257) {
            $orderShippingId = Shipping::getShippingIdByCode(Yii::$app->params['zhiFaDefaultShippingCode']);
            $cartGoods[$brandOrSupplierId]['shipping_id'] = (int)$orderShippingId;
            $cartGoods[$brandOrSupplierId]['shipping_name'] = (string)Yii::$app->params['shippingIdShortDesc'][$orderShippingId];
        } else {
            $cartGoods[$brandOrSupplierId]['shipping_id'] = (int)$goods->brand['shipping_id'];
            $cartGoods[$brandOrSupplierId]['shipping_name'] = (string)Yii::$app->params['shippingIdShortDesc'][$goods->brand['shipping_id']];
        }


        $cartGoods[$brandOrSupplierId]['discount'] = !empty($goods->extInfo['selectedCut']['cut'])
            ? (string)$goods->extInfo['selectedCut']['cut']
            : 0.00;
        $cartGoods[$brandOrSupplierId]['brandTotalNum'] = (int)$cartGoodsItem['goods_number'];
        $cartGoods[$brandOrSupplierId]['brandGoodsAmount'] = NumberHelper::price_format($cartGoodsItem['goodsSubTotal']);
        $cartGoods[$brandOrSupplierId]['brandTotalAmount'] = NumberHelper::price_format($cartGoodsItem['goodsSubTotal']);

        $total = [
            'cartAllSelected' => 1,   //  默认购物车商品全部选中
            'goods_amount' => (string)$cartGoodsItem['goodsSubTotal'],   //  默认购物车商品全部选中
            'remarks' => '',   //  默认购物车商品全部选中
            'discount' => $cartGoods[$brandOrSupplierId]['discount']
                ? (string)$cartGoods[$brandOrSupplierId]['discount']
                : 0.00,   //  默认没有优惠

            'fullCulEventList' => !empty($goods->extInfo['fullCulEventList']) ? $goods->extInfo['fullCulEventList'] : [],
            'fullCulEventListFormat' => !empty($goods->extInfo['fullCulEventListFormat'])
                ? $goods->extInfo['fullCulEventListFormat']
                : [],
            'bestFullCut' => !empty($goods->extInfo['bestFullCut'])
                ? $goods->extInfo['bestFullCut']
                : [],

            'couponEventList' => !empty($goods->extInfo['couponEventList'])
                ? $goods->extInfo['couponEventList']
                : [],
            'couponEventListFormat' => !empty($goods->extInfo['couponEventListFormat'])
                ? $goods->extInfo['couponEventListFormat']
                : [],
            'bestCoupon' => !empty($goods->extInfo['bestCoupon']) ? $goods->extInfo['bestCoupon'] : [],
            'canNotUseCouponList' => !empty($goods->extInfo['canNotUseCouponList'])
                ? $goods->extInfo['canNotUseCouponList']
                : [],
            'canNotUseCouponCount' => !empty($goods->extInfo['canNotUseCouponCount']) ? $goods->extInfo['canNotUseCouponCount'] : 0,
            'canUseCouponList' => !empty($goods->extInfo['canUseCouponList']) ? $goods->extInfo['canUseCouponList'] : [],
            'canUseCouponCount' => !empty($goods->extInfo['canUseCouponCount']) ? $goods->extInfo['canUseCouponCount'] : 0,

            'selectedCut' => !empty($goods->extInfo['selectedCut']) ? $goods->extInfo['selectedCut'] : [],
            'hasDirectGoods' => $goods->supplier_user_id == 1257 ? 1 : 0,    //  判定是否有 结算的直发商品

            'shippingFee' => 0.00,   //  默认没有运费
            'totalAmount' => 0.00,   //  默认总金额，每次用之前重新计算 = goods_amount + shippingFee - discount
            'userRankSavePrice' => $userRankSavePrice,
        ];

        return [
            'cartGoods' => $cartGoods,
            'total' => $total,
        ];
    }

    /**
     * 处理满减活动信息，取得当前最大优惠的活动规则
     * @param $fullCulEventList
     * @return array
     */
    public static function processFullCutEventList($fullCulEventList)
    {
        $bestFullCut = [];
        $fullCulEventListFormat = [];
        if (!empty($fullCulEventList)) {
            foreach ($fullCulEventList as $fullCut) {
                $fullCut = self::getFullCutMsg($fullCut);
                $fullCulEventListFormat[] = $fullCut;
                //  满减活动如果有多个，值保留优惠幅度最大的活动—— 是否有必要保留全部，给用户选
                if (empty($bestFullCut)) {
                    $bestFullCut = $fullCut;
                } elseif ($fullCut['cut'] > $bestFullCut['cut']) {
                    $bestFullCut = $fullCut;
                }
            }
        }

        return [
            'bestFullCut' => $bestFullCut,
            'fullCulEventListFormat' => $fullCulEventListFormat,
        ];
    }

    /**
     * 处理优惠券列表
     * @param $couponEventList
     * @param $userId
     * @param int $couponId
     * @return array
     */
    public static function processCouponEventLIst($couponEventList, $userId, $couponId = 0)
    {
        $selectedCut = [];
        $bestCoupon = [];
        $couponEventListFormat = [];
        $canNotUseCouponList = [];
        $canUseCouponList = [];
        $now = date('Y-m-d');

        if (!empty($couponEventList)) {
            $userCouponList = CouponHelper::getCouponList($userId, CouponRecord::COUPON_STATUS_UNUSED);
            if (!empty($userCouponList)) {
                foreach ($userCouponList as $coupon) {
                    $coupon['useable'] = false;
                    $coupon['cut'] = 0;
                    $coupon['eventToGoods'] = [];
                    $coupon['eventToGoodsSelected'] = [];
                    $coupon['fullCutRule']['cutFormat'] = (int)$coupon['fullCutRule']['cut'];

                    $coupon['start_date'] = substr($coupon['start_time'], 0, 10);
                    $coupon['end_date'] = substr($coupon['end_time'], 0, 10);

                    $_eventId = $coupon['event_id'];
                    //  如果优惠券对应的活动当前有效，则按当前的购买量计算 是否满足优惠券的使用条件
                    if (!empty($couponEventList[$_eventId])) {
                        $coupon['sumPrice'] = $couponEventList[$_eventId]['sumPrice'];
                        $coupon['eventToGoods'] = $couponEventList[$_eventId]['eventToGoods'];
                        $coupon['eventToGoodsSelected'] = $couponEventList[$_eventId]['eventToGoods'];

                        if (
                            $coupon['sumPrice'] >= $coupon['fullCutRule']['above']
                            && $now <= $coupon['end_date']
                            && $now >= $coupon['start_date']
                        ) {
                            $coupon['useable'] = true;
                            $coupon['cut'] = $coupon['fullCutRule']['cutFormat'];

                            //  如果用户指定了要使用的优惠券
                            if ($couponId == $coupon['coupon_id']) {
                                $selectedCut = $coupon;
                            }

                            //  计算最大优惠幅度
                            if (empty($bestCoupon)) {
                                $bestCoupon = $coupon;
                            } elseif ((int)$coupon['cut'] > $bestCoupon['cut']) {
                                $bestCoupon = $coupon;
                            }
                        }
                    }

                    $couponEventListFormat[] = $coupon;
                }

                //  按优惠幅度排序 ——不满足使用条件的优惠券cut = 0，自动排序到后面
                usort($couponEventListFormat, function ($a, $b){
                    if ($a['cut'] == $b['cut']) {
                        return 0;
                    } else {
                        return $a['cut'] > $b['cut'] ? -1 : 1;
                    }
                });

                foreach ($couponEventListFormat as $coupon) {
                    //  区分可用优惠券和不可用优惠券
                    if ($coupon['useable']) {
                        $canUseCouponList[] = $coupon;
                    } else {
                        $canNotUseCouponList[] = $coupon;
                    }
                }
            }
        }

        return [
            'bestCoupon'            => $bestCoupon,
            'couponEventListFormat' => $couponEventListFormat,
            'canNotUseCouponList'   => $canNotUseCouponList,
            'canNotUseCouponCount'  => count($canNotUseCouponList),
            'canUseCouponList'      => $canUseCouponList,
            'canUseCouponCount'     => count($canUseCouponList),
            'selectedCut'           => $selectedCut,
        ];
    }

    /**
     * 用户选中优惠券则按优惠券的优惠，没选择优惠券则按最大幅度的满减优惠
     * @param $total
     * @param $couponId
     * @return array
     */
    public static function chooseMaxCutEvent($total, $couponId)
    {
        $remarks = '';
        if (empty($total['selectedCut'])) {
            if (!empty($total['bestFullCut']) && !empty($total['bestFullCut']['match'])) {
                $selectedCut = $total['bestFullCut'];
                $selectedCut['event_id'] = $total['bestFullCut']['event']['event_id'];
                $selectedCut['rule_id'] = $total['bestFullCut']['match']['rule_id'];
                if (!empty($total['canUseCouponList'])) {
                    $remarks = '如果您没有选择要使用的优惠券，系统将自动使用满减规则。';
                }
            } else {
                $selectedCut =[];
            }
        }
        else {
            $selectedCut = $total['selectedCut'];
        }

        return [
            'selectedCut' => $selectedCut,
            'remarks' => $remarks,
        ];
    }

    /**
     * 格式化 购物车中的 结算商品
     *
     * 如果是团采的商品，修改商品为 不使用全局会员折扣
     * @param $cartList
     * @return array
     */
    public static function formatCartGoodsForCart($cartList, $userRankDiscount)
    {
        $cartGoods = [];
        $total = [
            'fullCulEventList' => [],
            'couponEventList' => [],
            'fullCulGoodsIdList' => [],
            'couponGoodsIdList' => [],
            'userRankSavePrice' => 0.00,
        ];
        $selectedGoodsIdList = [];  //  已勾选的商品

        $now = date('Y-m-d H:i:s');
        $allGoodsEvent = Event::find()
            ->where([
                Event::tableName().'.is_active' => Event::IS_ACTIVE,
                'event_type' => [Event::EVENT_TYPE_FULL_CUT, Event::EVENT_TYPE_COUPON],
                'effective_scope_type' => EVENT::EFFECTIVE_SCOPE_TYPE_ALL,
            ])->andWhere([
                'and',
                ['<=', 'start_time', $now],
                ['>=', 'end_time', $now],
            ])->all();

        $zhiFaEvent = Event::find()
            ->where([
                Event::tableName().'.is_active' => Event::IS_ACTIVE,
                'event_type' => [Event::EVENT_TYPE_FULL_CUT, Event::EVENT_TYPE_COUPON],
                'effective_scope_type' => EVENT::EFFECTIVE_SCOPE_TYPE_ZHIFA,
            ])->andWhere([
                'and',
                ['<=', 'start_time', $now],
                ['>=', 'end_time', $now],
            ])->all();

        //  团采商品不使用全局会员折扣
        $aliveGroupBuyList = GoodsActivity::aliveGroupBuyList();
        $groupBuyGoodsList = array_keys($aliveGroupBuyList);
        Goods::updateAll(['discount_disable' => 1], ['goods_id' => $groupBuyGoodsList]);

        foreach ($cartList as $cart) {
            if ($cart->goods_number < 1) {
                continue;
            }
            //  如果有moq 修正用户的起售数量    ——弃用 不同会员等级对应的起售数量
//                if (!empty($goods['moqs'])) {
//                    foreach ($goods['moqs'] as $moq) {
//                        if ($moq['user_rank'] == $userRank) {
//                            $goods->start_num = $moq['moq'];
//                            if ($goods->start_num > $goods['cart']['goods_number']) {
//                                \Yii::trace(__CLASS__.' | '.__FUNCTION__.' | 当前商品的起售数量为'.$goods->start_num.
//                                    ' | goodsId:'.$goods->goods_id.' | goodsNumber:'.$goods['cart']['goods_number']);
//                                continue;
//                            }
//                        }
//                    }
//                }

            //  修正配送方式  改用品牌的配送方式，如果是团采秒杀的购买 则使用 团采秒杀活动的配送方式,直发商品使用小美诚品品牌的配送方式
            if (!empty($cart->goods->supplier_user_id) && $cart->goods->supplier_user_id == 1257) {
                $orderShippingId = Shipping::getShippingIdByCode(Yii::$app->params['zhiFaDefaultShippingCode']);
            }
            //  普通商品使用品牌的配送方式，默认到付
            else {
                $orderShippingId = !empty($cart->goods->brand['shipping_id'])
                    ? $cart->goods->brand['shipping_id'] :
                    Shipping::getDefaultShippingId();
            }
            $orderShippingId = intval($orderShippingId);

            //  修正价格
            $currentPrice = $cart->goods->getCurrentPrice($cart->goods_number, $userRankDiscount);
            if ($cart->goods_price != $currentPrice['goods_price']) {
                $cart->goods_price = $currentPrice['goods_price'];
                $cart->save();
            }
            if (!empty($currentPrice['userRankSavePrice'])) {
                $total['userRankSavePrice'] += $currentPrice['userRankSavePrice'] * $cart->goods_number;
            }

            //  处理商品参与的活动
            $gift = []; //  满赠有明确的event_rule，
            $wuliaoList = [];
            //  满减/优惠券 对应 全局、直发、eventToBrand、eventToGoods 四种模式
            $list = [
                $cart->goods->eventList,    //  eventToGoods
                $cart->goods->brand->eventList,      //  eventToBrand
                $allGoodsEvent,             //  全局
            ];
            //  非直发商品不参与直发活动
            if ($cart->goods->supplier_user_id == 1257) {
                $list[] = $zhiFaEvent;      //  直发
            }

            //  团采可加入购物车，但是不参与 物料配比以外的任何活动
            $isGroupBuyGoods = false;
            if (in_array($cart->goods->goods_id, $groupBuyGoodsList)) {
                $isGroupBuyGoods = true;
            }

            if (!empty($list)) {
                $eventList = self::uniqueEventList($list);
                foreach ($eventList as $event) {
                    switch ($event->event_type) {
                        case Event::EVENT_TYPE_FULL_GIFT:
                            //  如果赠品数量不足，返回gift为空
                            if (!$isGroupBuyGoods) {
                                $gift = self::getGiftInfo($cart->goods, $event, $cart->goods_number);
                            }
                            break;
                        case Event::EVENT_TYPE_FULL_CUT:
                            //  有活动规则的活动才是有效的
                            if (!$isGroupBuyGoods) {
                                if (!empty($event->fullCutRule)) {
                                    $total['fullCulGoodsIdList'][] = $cart->goods_id;

                                    self::assignEventList($event, $cart, $total['fullCulEventList']);
                                }
                            }
                            break;
                        case Event::EVENT_TYPE_COUPON:
                            //  有活动规则的活动才是有效的
                            if (!$isGroupBuyGoods) {
                                if (!empty($event->fullCutRule)) {
                                    $total['couponGoodsIdList'][] = $cart->goods_id;

                                    self::assignEventList($event, $cart, $total['couponEventList']);
                                }
                            }
                            break;
                        case Event::EVENT_TYPE_WULIAO:
                            //  如果赠品数量不足，返回gift为空
                            $wuliaoList = self::getGiftInfo($cart->goods, $event, $cart->goods_number);
                            break;
                        default :
                            break;
                    }
                }
            }

            //  判定商品是否可以勾选  购物车数量小于起售数量的在 Cart::check()中修正为起售数量
            if (!$cart->goods->is_on_sale || $cart->goods->is_delete) {
                $canSelect = false;
                $errorMsg = '已下架';
            }
//            elseif (!empty($gift)) {
//                $canSelect = true;
//            }
            elseif ($cart->goods->start_num > $cart->goods->goods_number) {
                $canSelect = false;
                $errorMsg = '库存不足，当前可购买最大数量为：'.$cart->goods->goods_number;
            } else {
                $canSelect = true;
                $errorMsg = '';
            }

            //  团采商品要考虑购买上限
            if ($isGroupBuyGoods) {
                $goodsMaxCanBuy = min($aliveGroupBuyList[$cart->goods_id]['limit_num'], $cart->goods->goods_number);
                if ($cart->selected) {
                    //  如果购物车的购买数量 大于 团采活动的限购数量，商品不可选中
                    if ($cart->goods_number > $aliveGroupBuyList[$cart->goods_id]['limit_num']) {
                        Cart::updateAll(['selected' => 0], ['rec_id' => $cart->rec_id]);
                        $cart->selected = 0;
                    }
                }

            } else {
                $goodsMaxCanBuy = $cart->goods->goods_number;
            }

            //  按o_order_goods表的数据拼装
            $cartGoodsItem = [
                'rec_id' => (int)$cart->rec_id,
                'goods_id' => (int)$cart->goods_id,
                'goods_name' => (string)$cart->goods->goods_name,
                'goods_sn' => (string)$cart->goods->goods_sn,
                'product_id' => 0,
                'goods_number' => (int)$cart->goods_number,
                'market_price' => (string)$cart->goods->market_price,
                'goods_price' => NumberHelper::price_format($cart->goods_price),   //  计算优惠活动之前的结算价格
                'goods_attr' => '',
                'is_real' => (int)$cart->goods->is_real,
                'extension_code' => (string)$cart->goods->extension_code,
                'parent_id' => 0,
                'is_gift' => OrderGoods::IS_GIFT_NO,
                'event_id' => 0,
                'pay_price' => NumberHelper::price_format($cart->goods_price),
                'is_on_sale' => (int)$cart->goods->is_on_sale,
                'is_delete' => (int)$cart->goods->is_delete,

                'selected' => $cart->selected,
                'goodsSubTotal' => NumberHelper::price_format($cart->goods_price * $cart->goods_number),  //小计
                'gift' => $gift,  //  赠品(数组， 支持多个赠品)
                'wuliaoList' => $wuliaoList,  //  赠品(数组， 支持多个赠品)
                'brand_name' => (string)$cart->goods->brand['brand_name'],  //  品牌名称
                'brand_id' => (int)$cart->goods->brand_id,  //  品牌ID
                'supplier_user_id' => (int)$cart->goods->supplier_user_id,  //  供应商ID
                'start_num' => (int)$cart->goods->start_num,  //  供应商ID

                'goods_weight' => (string)$cart->goods->goods_weight,
                'goods_thumb' => ImageHelper::get_image_path($cart->goods->goods_thumb),  //  缩略图
                'goods_img' => ImageHelper::get_image_path($cart->goods->goods_img),
                'sample' => '',   //  物料配比 改用 满赠的形式配置，同步减库存
                'canSelect' => (int)$canSelect,   //  商品是否可勾选
                'goodsMaxCanBuy' => (int)$goodsMaxCanBuy,
                'buy_by_box' => (int)$cart->goods->buy_by_box,
                'number_per_box' => (int)$cart->goods->number_per_box,
                'errorMsg' => (string)$errorMsg,

                'fullCut' => false,
                'measure_unit' => $cart->goods->measure_unit ? (string)$cart->goods->measure_unit : '件',
            ];

            if ($cart->selected) {
                $selectedGoodsIdList[] = $cart->goods_id;   //  已勾选的商品，判断是否参与了团采活动，如果是，判定最大可购买数量
            }

            //  按 供应商/品牌 品牌分组
            if ($cartGoodsItem['supplier_user_id'] > 0) {
                $brandOrSupplierId = (int)$cartGoodsItem['supplier_user_id'];
            } else {
                $brandOrSupplierId = (int)$cartGoodsItem['brand_id'];
            }
            $cartGoods[$brandOrSupplierId]['goodsList'][] = $cartGoodsItem;
            if ($cartGoodsItem['selected']) {
                $cartGoods[$brandOrSupplierId]['hasValidGoods'] = 1;    //  判定子单是否用于结算
            }

            //  修正支付商品的品牌名称
            if ($brandOrSupplierId == 1257) {
                $cartGoods[$brandOrSupplierId]['brand_name'] = '小美直发';
                $cartGoods[$brandOrSupplierId]['brand_id'] = 0;
                $cartGoods[$brandOrSupplierId]['supplier_user_id'] = 1257;
                if ($cartGoodsItem['selected']) {
                    $total['hasDirectGoods'] = 1;    //  判定是否有 结算的直发商品
                }
            } else {
                $cartGoods[$brandOrSupplierId]['brand_name'] = (string)$cartGoodsItem['brand_name'];
                $cartGoods[$brandOrSupplierId]['brand_id'] = (int)$brandOrSupplierId;
                $cartGoods[$brandOrSupplierId]['supplier_user_id'] = 0;
            }

            //  判定商品分组是否全部是勾选状态
            if (!isset($cartGoods[$brandOrSupplierId]['allSelected'])) {
                $cartGoods[$brandOrSupplierId]['allSelected'] = (int)$cartGoodsItem['selected'];
            } else {
                $cartGoods[$brandOrSupplierId]['allSelected'] *= $cartGoodsItem['selected'];
            }

            if (!isset($cartGoods[$brandOrSupplierId]['goodsWeight'] )) {
                $cartGoods[$brandOrSupplierId]['goodsWeight'] = $cartGoodsItem['goods_weight'];
            } else {
                $cartGoods[$brandOrSupplierId]['goodsWeight'] += $cartGoodsItem['goods_weight'];
            }
            
            //  子单商品件数
            if (!isset($cartGoods[$brandOrSupplierId]['brandTotalNum'])) {
                $cartGoods[$brandOrSupplierId]['brandTotalNum'] = (int)$cartGoodsItem['goods_number'];
                $cartGoods[$brandOrSupplierId]['brandGoodsAmount'] = (string)$cartGoodsItem['goodsSubTotal'];
                $cartGoods[$brandOrSupplierId]['brandTotalAmount'] = NumberHelper::price_format($cartGoodsItem['goodsSubTotal']);
            } else {
                $cartGoods[$brandOrSupplierId]['brandTotalNum'] += $cartGoodsItem['goods_number'];
                $cartGoods[$brandOrSupplierId]['brandGoodsAmount'] += $cartGoodsItem['goodsSubTotal'];
                $cartGoods[$brandOrSupplierId]['brandTotalAmount'] = NumberHelper::price_format($cartGoods[$brandOrSupplierId]['brandTotalAmount'] + $cartGoodsItem['goodsSubTotal']);
            }

            //  分组的配送方式
            $cartGoods[$brandOrSupplierId]['shipping_id'] = (int)$orderShippingId; //商品信息shipping_code为空
            $cartGoods[$brandOrSupplierId]['shipping_name'] = (string)Yii::$app->params['shippingIdShortDesc'][$orderShippingId];
            $cartGoods[$brandOrSupplierId]['discount'] = '0.00'; //  预设折扣信息为0  考虑多个优惠活动共存时 按顺序累加

            if ($cart->selected == Cart::IS_NOT_SELECTED) {
                $total['cartAllSelected'] = false;
            } else {
                if (empty($total['goods_amount'])) {
                    $total['goods_amount'] = $cartGoodsItem['goodsSubTotal'];
                } else {
                    $total['goods_amount'] += $cartGoodsItem['goodsSubTotal'];
                }

                if (empty($total['goods_weight'])) {
                    $total['goods_weight'] = $cartGoodsItem['goods_weight'];
                } else {
                    $total['goods_weight'] += $cartGoodsItem['goods_weight'];
                }

                if (empty($total['total_number'])) {
                    $total['total_number'] = $cartGoodsItem['goods_number'];
                } else {
                    $total['total_number'] += $cartGoodsItem['goods_number'];
                }
            }
        }

        $total['userRankSavePrice'] = NumberHelper::price_format($total['userRankSavePrice']);

        //  团采的购买数量超出上限，则不能勾选，在购物车中加减的地方做修正

        return [
            'cartGoods' => $cartGoods,
            'total' => $total,
        ];
    }

    /**
     * 格式化 立即批量购买的 结算商品
     * @param $goodsList 对象数组
     * @param $goodsMap [$goodsId => $goodsNum, ...]
     * @param $userRankDiscount
     * @return array
     */
    public static function formatCartGoodsForGeneralBatch($goodsList, $goodsMap, $userRankDiscount)
    {
        $cartGoods = [];
        $total = [
            'fullCulEventList' => [],
            'couponEventList' => [],
            'fullCulGoodsIdList' => [],
            'couponGoodsIdList' => [],
            'userRankSavePrice' => 0.00,
        ];

        $now = date('Y-m-d H:i:s');
        $allGoodsEvent = Event::find()
            ->where([
                Event::tableName().'.is_active' => Event::IS_ACTIVE,
                'event_type' => [Event::EVENT_TYPE_FULL_CUT, Event::EVENT_TYPE_COUPON],
                'effective_scope_type' => EVENT::EFFECTIVE_SCOPE_TYPE_ALL,
            ])->andWhere([
                'and',
                ['<=', 'start_time', $now],
                ['>=', 'end_time', $now],
            ])->all();

        $zhiFaEvent = Event::find()
            ->where([
                Event::tableName().'.is_active' => Event::IS_ACTIVE,
                'event_type' => [Event::EVENT_TYPE_FULL_CUT, Event::EVENT_TYPE_COUPON],
                'effective_scope_type' => EVENT::EFFECTIVE_SCOPE_TYPE_ZHIFA,
            ])->andWhere([
                'and',
                ['<=', 'start_time', $now],
                ['>=', 'end_time', $now],
            ])->all();
        foreach ($goodsList as $goods) {
            if ($goodsMap[$goods->goods_id] < 1) {
                continue;
            }

            //  修正配送方式  改用品牌的配送方式，如果是团采秒杀的购买 则使用 团采秒杀活动的配送方式,直发商品使用小美诚品品牌的配送方式
            if (!empty($goods->supplier_user_id) && $goods->supplier_user_id == 1257) {
                $orderShippingId = Shipping::getShippingIdByCode(Yii::$app->params['zhiFaDefaultShippingCode']);
            }
            //  普通商品使用品牌的配送方式，默认到付
            else {
                $orderShippingId = !empty($goods->brand['shipping_id'])
                    ? $goods->brand['shipping_id'] :
                    Shipping::getDefaultShippingId();
            }
            $orderShippingId = intval($orderShippingId);

            //  修正价格
            $currentPrice = $goods->getCurrentPrice($goodsMap[$goods->goods_id], $userRankDiscount);
            if (!empty($currentPrice['userRankSavePrice'])) {
                $total['userRankSavePrice'] += $currentPrice['userRankSavePrice'] * $goodsMap[$goods->goods_id];
            }

            //  处理商品参与的活动
            $gift = []; //  满赠有明确的event_rule，
            //  满减/优惠券 对应 全局、直发、eventToBrand、eventToGoods 四种模式
            $list = [
                $goods->eventList,    //  eventToGoods
                $goods->brand->eventList,      //  eventToBrand
                $allGoodsEvent,             //  全局
            ];
            //  非直发商品不参与直发活动
            if ($goods->supplier_user_id == 1257) {
                $list[] = $zhiFaEvent;      //  直发
            }

            $wuliaoList = [];   //  物料要区分所属的商品，避免覆盖
            if (!empty($list)) {
                $eventList = self::uniqueEventList($list);
                foreach ($eventList as $event) {
                    switch ($event->event_type) {
                        case Event::EVENT_TYPE_FULL_GIFT:
                            //  如果赠品数量不足，返回gift为空
                            $gift = self::getGiftInfo($goods, $event, $goodsMap[$goods->goods_id]);
                            break;
                        case Event::EVENT_TYPE_FULL_CUT:
                            //  有活动规则的活动才是有效的
                            if (!empty($event->fullCutRule)) {
                                $total['fullCulGoodsIdList'][] = $goods->goods_id;

                                self::assignEventListForGoodsBatch(
                                    $event,
                                    $goods,
                                    $goodsMap[$goods->goods_id],
                                    $currentPrice['goods_price'],
                                    $total['fullCulEventList']
                                );
                            }
                            break;
                        case Event::EVENT_TYPE_COUPON:
                            //  有活动规则的活动才是有效的
                            if (!empty($event->fullCutRule)) {
                                $total['couponGoodsIdList'][] = $goods->goods_id;

                                self::assignEventListForGoodsBatch(
                                    $event,
                                    $goods,
                                    $goodsMap[$goods->goods_id],
                                    $currentPrice['goods_price'],
                                    $total['couponEventList']
                                );
                            }
                            break;
                        case Event::EVENT_TYPE_WULIAO:
                            //  如果赠品数量不足，返回gift为空
                            $wuliaoList = self::getGiftInfo($goods, $event, $goodsMap[$goods->goods_id]);
                            break;
                        default :
                            break;
                    }
                }
            }

            //  判定商品是否可以勾选  购物车数量小于起售数量的在 Cart::check()中修正为起售数量
            if (!$goods->is_on_sale || $goods->is_delete) {
                $canSelect = false;
                $errorMsg = '已下架';
            }

            elseif ($goods->start_num > $goods->goods_number) {
                $canSelect = false;
                $errorMsg = '库存不足，当前可购买最大数量为：'.$goods->goods_number;
            } else {
                $canSelect = true;
                $errorMsg = '';
            }

            //  按o_order_goods表的数据拼装
            $cartGoodsItem = [
                'goods_id' => (int)$goods->goods_id,
                'goods_name' => (string)$goods->goods_name,
                'goods_sn' => (string)$goods->goods_sn,
                'product_id' => 0,
                'goods_number' => (int)$goodsMap[$goods->goods_id],
                'market_price' => (string)$goods->market_price,
                'goods_price' => NumberHelper::price_format($currentPrice['goods_price']),   //  计算优惠活动之前的结算价格
                'goods_attr' => '',
                'is_real' => (int)$goods->is_real,
                'extension_code' => OrderInfo::EXTENSION_CODE_GENERAL_BATCH,
                'parent_id' => 0,
                'is_gift' => OrderGoods::IS_GIFT_NO,
                'event_id' => 0,
                'pay_price' => NumberHelper::price_format($currentPrice['goods_price']),
                'is_on_sale' => (int)$goods->is_on_sale,
                'is_delete' => (int)$goods->is_delete,

                'selected' => 1,
                'goodsSubTotal' => NumberHelper::price_format($currentPrice['goods_price'] * $goodsMap[$goods->goods_id]),  //小计
                'gift' => $gift,  //  赠品(数组， 支持多个赠品)
                'wuliaoList' => $wuliaoList,  //  赠品(数组， 支持多个赠品)
                'brand_name' => (string)$goods->brand['brand_name'],  //  品牌名称
                'brand_id' => (int)$goods->brand_id,  //  品牌ID
                'supplier_user_id' => (int)$goods->supplier_user_id,  //  供应商ID
                'start_num' => (int)$goods->start_num,  //  供应商ID

                'goods_weight' => (string)$goods->goods_weight,
                'goods_thumb' => ImageHelper::get_image_path($goods->goods_thumb),  //  缩略图
                'goods_img' => ImageHelper::get_image_path($goods->goods_img),
                'sample' => '',   //  物料配比 改用 满赠的形式配置，同步减库存
                'canSelect' => (int)$canSelect,   //  商品是否可勾选
                'goodsMaxCanBuy' => (int)$goods->goods_number,
                'buy_by_box' => (int)$goods->buy_by_box,
                'number_per_box' => (int)$goods->number_per_box,
                'errorMsg' => (string)$errorMsg,

                'fullCut' => false,
                'measure_unit' => $goods->measure_unit ? (string)$goods->measure_unit : '件',
            ];

            //  按 供应商/品牌 品牌分组
            if ($cartGoodsItem['supplier_user_id'] > 0) {
                $brandOrSupplierId = (int)$cartGoodsItem['supplier_user_id'];
            } else {
                $brandOrSupplierId = (int)$cartGoodsItem['brand_id'];
            }
            $cartGoods[$brandOrSupplierId]['goodsList'][] = $cartGoodsItem;
            if ($cartGoodsItem['selected']) {
                $cartGoods[$brandOrSupplierId]['hasValidGoods'] = 1;    //  判定子单是否用于结算
            }

            //  修正支付商品的品牌名称
            if ($brandOrSupplierId == 1257) {
                $cartGoods[$brandOrSupplierId]['brand_name'] = '小美直发';
                $cartGoods[$brandOrSupplierId]['brand_id'] = 0;
                $cartGoods[$brandOrSupplierId]['supplier_user_id'] = 1257;
                if ($cartGoodsItem['selected']) {
                    $total['hasDirectGoods'] = 1;    //  判定是否有 结算的直发商品
                }
            } else {
                $cartGoods[$brandOrSupplierId]['brand_name'] = (string)$cartGoodsItem['brand_name'];
                $cartGoods[$brandOrSupplierId]['brand_id'] = (int)$brandOrSupplierId;
                $cartGoods[$brandOrSupplierId]['supplier_user_id'] = 0;
            }

            //  判定商品分组是否全部是勾选状态
            if (!isset($cartGoods[$brandOrSupplierId]['allSelected'])) {
                $cartGoods[$brandOrSupplierId]['allSelected'] = (int)$cartGoodsItem['selected'];
            } else {
                $cartGoods[$brandOrSupplierId]['allSelected'] *= $cartGoodsItem['selected'];
            }

            if (!isset($cartGoods[$brandOrSupplierId]['goodsWeight'] )) {
                $cartGoods[$brandOrSupplierId]['goodsWeight'] = $cartGoodsItem['goods_weight'];
            } else {
                $cartGoods[$brandOrSupplierId]['goodsWeight'] += $cartGoodsItem['goods_weight'];
            }

            //  子单商品件数
            if (!isset($cartGoods[$brandOrSupplierId]['brandTotalNum'])) {
                $cartGoods[$brandOrSupplierId]['brandTotalNum'] = (int)$cartGoodsItem['goods_number'];
                $cartGoods[$brandOrSupplierId]['brandGoodsAmount'] = (string)$cartGoodsItem['goodsSubTotal'];
                $cartGoods[$brandOrSupplierId]['brandTotalAmount'] = NumberHelper::price_format($cartGoodsItem['goodsSubTotal']);
            } else {
                $cartGoods[$brandOrSupplierId]['brandTotalNum'] += $cartGoodsItem['goods_number'];
                $cartGoods[$brandOrSupplierId]['brandGoodsAmount'] += $cartGoodsItem['goodsSubTotal'];
                $cartGoods[$brandOrSupplierId]['brandTotalAmount'] = NumberHelper::price_format($cartGoods[$brandOrSupplierId]['brandTotalAmount'] + $cartGoodsItem['goodsSubTotal']);
            }

            //  分组的配送方式
            $cartGoods[$brandOrSupplierId]['shipping_id'] = (int)$orderShippingId; //商品信息shipping_code为空
            $cartGoods[$brandOrSupplierId]['shipping_name'] = (string)Yii::$app->params['shippingIdShortDesc'][$orderShippingId];
            $cartGoods[$brandOrSupplierId]['discount'] = '0.00'; //  预设折扣信息为0  考虑多个优惠活动共存时 按顺序累加

            if (empty($total['goods_amount'])) {
                $total['goods_amount'] = $cartGoodsItem['goodsSubTotal'];
            } else {
                $total['goods_amount'] += $cartGoodsItem['goodsSubTotal'];
            }

            if (empty($total['goods_weight'])) {
                $total['goods_weight'] = $cartGoodsItem['goods_weight'];
            } else {
                $total['goods_weight'] += $cartGoodsItem['goods_weight'];
            }

            if (empty($total['total_number'])) {
                $total['total_number'] = $cartGoodsItem['goods_number'];
            } else {
                $total['total_number'] += $cartGoodsItem['goods_number'];
            }
        }

        $total['userRankSavePrice'] = NumberHelper::price_format($total['userRankSavePrice']);

        return [
            'cartGoods' => $cartGoods,
            'total' => $total,
        ];
    }

    /**
     * 计算订单的各项价格
     * 【1】一次获取结算商品 分组 用到的所有配送方式
     * 【2】遍历结算商品，修正配送信息、运费、优惠
     *  【2.1】修正配送信息
     *  【2.2】计算运费
     *  【2.3】均摊折扣
     * 【3】优惠数据修正
     * @param string $extensionCode 购买方式
     * @param array $cartGoods      结算商品列表
     * @param array $address        收货地址
     * @param array $prepay         直发商品是否选择了预付运费
     * @param array $selectedCut    结算 参与的优惠活动
     * @return mixed
     */
    public static function getOrderFee($extensionCode, $cartGoods, $address, $prepay, &$total, $selectedCut = [])
    {
        //  【1】拼装用户的地址数组
        $regionIdList = [
            $address['country'],
            $address['province'],
            $address['city'],
            $address['district'],
        ];

        //  【2】遍历结算商品，修正配送信息、运费、优惠
        $discount = 0;  //  如果有优惠活动，计算均摊后的金额 累计 与总金额是否有出入，如果有，修正到最后一个有优惠的子单上
        $lastKey = 0;   //  最后一个有优惠的子单 brandOrSupplierId
        $total['preShippingFee'] = 0.00;
        $total['shippingFee'] = 0.00;

        foreach ($cartGoods as $key => $goodsGroup) {

            Yii::warning('goodsGroup = '. VarDumper::dumpAsString($goodsGroup), __METHOD__);

            //  【2.1】如果没有获取到有效的配送方式信息（配送方式不存在或无效），改用默认的配送方式(到付)
            if (empty($goodsGroup['shipping_id'])) {
                $cartGoods[$key]['shipping_id'] = Yii::$app->params['default_shipping_id'];
            }

            $shippingAreaInfo = ShippingArea::getShippingInfo($cartGoods[$key]['shipping_id'], $regionIdList);
            Yii::warning(' $shippingAreaInfo = '. json_encode($shippingAreaInfo), __METHOD__);
            $cartGoods[$key]['shipping_id'] = (int)$shippingAreaInfo->shipping_id;
            $cartGoods[$key]['shipping_code'] = (string)$shippingAreaInfo->shipping->shipping_code;

            //  【2.2】计算运费，处理运费说明
            if ($extensionCode != 'integral_exchange') {
                $goodsWeight = !empty($cartGoods[$key]['goodsWeight']) ? $cartGoods[$key]['goodsWeight'] : 0;

                //  礼包商品计算运费的价格应是实付价格
                if ($extensionCode == 'gift_pkg') {
                    $brandGoodsAmount = $total['totalAmount'];
                } else {
                    $brandGoodsAmount = !empty($cartGoods[$key]['brandGoodsAmount']) ? $cartGoods[$key]['brandGoodsAmount'] : 0;
                }
                $brandTotalNum = !empty($cartGoods[$key]['brandTotalNum']) ? $cartGoods[$key]['brandTotalNum'] : 0;

                $shippingResult = self::shippingFee(
                    $shippingAreaInfo->shipping->shipping_code,
                    $shippingAreaInfo->configure,
                    $goodsWeight,
                    $brandGoodsAmount,
                    $brandTotalNum,
                    $prepay
                );
                $cartGoods[$key]['shipping_fee'] = NumberHelper::price_format($shippingResult['shipping_fee']);
                $cartGoods[$key]['shipping_code'] = (string)$shippingResult['shipping_code'];
                $cartGoods[$key]['shipping_id'] = (int)$shippingResult['shipping_id'];

                //  运费说明 显示\入库 都使用最终对应的 包邮（2）、到付（3）、现付（4）
                switch ($cartGoods[$key]['shipping_code']) {
                    /**
                     * 小美直发(满额包邮) 不满足条件时，由用户选择是否【现付运费】, 不选择现付则为【运费到付】
                     * 小美直发满额包邮 需要判断用户是否选择了现付运费，
                     * 用户没有选择现付也需要计算现付的运费金额供用户选择，
                     * 用户最终没有选择现付，则入库的运费金额要修正 为 0
                     */
                    case 'fgaf':
                        if ($shippingResult['shipping_fee'] > 0) {
                            $total['preShippingFee'] += $shippingResult['shipping_fee'];
                            if (!empty($prepay)) {
                                $cartGoods[$key]['shipping_name'] = '现付';   //  小美直发(运费现付)
                                $cartGoods[$key]['shipping_id'] = 4;
                                $cartGoods[$key]['shipping_fee'] = NumberHelper::price_format($shippingResult['shipping_fee']);
                                $cartGoods[$key]['brandTotalAmount'] = NumberHelper::price_format($cartGoods[$key]['brandTotalAmount'] + $shippingResult['shipping_fee']);
                                $total['shippingFee'] += $shippingResult['shipping_fee'];
                            }
                            //  不满足 小美直发(满额包邮) 条件 且 不选择现付运费  即为 到付
                            else {
                                $cartGoods[$key]['shipping_id'] = 3;
                                $cartGoods[$key]['shipping_fee'] = 0;
                                $cartGoods[$key]['shipping_name'] = '到付';   //  小美直发(运费到付)
                            }
                        } else {
                            $cartGoods[$key]['shipping_name'] = '包邮'; //  小美直发(包邮)
                        }
                        break;
                    case 'fpbs':
                        $total['preShippingFee'] += $shippingResult['shipping_fee'];
                        break;
                    case 'dfgf':    //  全国包邮
                    case 'free':    //  品牌方包邮
                        $cartGoods[$key]['shipping_id'] = 2;
                        $cartGoods[$key]['shipping_name'] = '包邮';
                        break;
                    case 'fpd':
                        $cartGoods[$key]['shipping_id'] = 3;
                        $cartGoods[$key]['shipping_name'] = '到付';
                    default :
                        break;
                }
            }
            //  积分兑换商品 属于小美直发商品， 特殊处理
            else {
                $cartGoods[$key]['shipping_id'] = 3;
                $cartGoods[$key]['shipping_code'] = 'fpd';
                $cartGoods[$key]['shipping_name'] = '到付'; //  运费到付
                $cartGoods[$key]['shipping_fee'] = '0.00';
                Yii::warning('$cartGoods[$key] = '. VarDumper::dumpAsString($cartGoods[$key]), __METHOD__);
            }

            //  【2.3】优惠金额>0  均摊折扣
            if (!empty($selectedCut['cut'])) {
                $cartGoods[$key] = self::shareCut($cartGoods[$key], $selectedCut);

                if ($cartGoods[$key]['discount'] > 0) {
                    $lastKey = $key;
                    $discount += $cartGoods[$key]['discount'];
                }
            }

            //  容错，shipping_id 为空的时候 判断是包邮还是到付，默认到付
            if (empty($cartGoods[$key]['shipping_id'])) {
                if (empty($cartGoods[$key]['shipping_fee'])) {
                    $cartGoods[$key]['shipping_id'] = 3;
                    $cartGoods[$key]['shipping_name'] = '到付';
                } else {
                    $cartGoods[$key]['shipping_id'] = 2;
                    $cartGoods[$key]['shipping_name'] = '现付';
                }
                Yii::warning('$cartGoods[$key] = '. VarDumper::dumpAsString($cartGoods[$key]), __METHOD__);
            }
        }

        //  【3】如果有优惠活动并且 均摊结果累计 与 优惠总金额有出入，修正到最后一个有优惠的子单上
        if (!empty($selectedCut['cut']) && $selectedCut['cut'] != $discount) {
            $lastDiscount = $cartGoods[$lastKey]['discount'] + $selectedCut['cut'] - $discount;
            $cartGoods[$lastKey]['discount'] = NumberHelper::price_format($lastDiscount);
        }

        $total['preShippingFee'] = NumberHelper::price_format($total['preShippingFee']);
        $total['shippingFee'] = NumberHelper::price_format($total['shippingFee']);

        return $cartGoods;
    }

    /**
     * 计算运费
     *
     * fgaf小美直发(满额包邮) 不满额的情况下 fpbs(预付运费) 用户可能选择到付(prepay = 0) fpd  需要判定修正
     * free品牌方包邮(偏远地区到付) 需要判定 是不是偏远地区， 对偏远地区做出修正
     *
     * @param $shippingCode     子单的配送方式code
     * @param $shippingConfig   shippingObj->shippingArea->configure 或 反序列化过的数组
     * @param $goodsWeight      子单的总重量
     * @param $goodsAmount      子单的总金额
     * @param int $goodsNumber  子单的商品总重量
     * @param int $prepay       是否现付运费，默认到付
     * @return int
     */
    public static function shippingFee(
        $shippingCode,
        $shippingConfig,
        $goodsWeight,
        $goodsAmount,
        $goodsNumber = 0,
        $prepay = 0
    ) {
        Yii::warning(' 计算运费开始 $shippingCode = '. $shippingCode.' $shippingConfig = '.json_encode($shippingConfig).
            ' $goodsWeight = '.$goodsWeight.' $goodsAmount = '.$goodsAmount.' $goodsNumber = '.$goodsNumber);
        if (!is_array($shippingConfig)) {
            $shippingConfig = unserialize($shippingConfig);
        }

        $filename =  Yii::getAlias('@plugins/shipping').'/' . $shippingCode . '.php';
        if (file_exists($filename)) {
            include_once($filename);

            $obj = new $shippingCode($shippingConfig);

            //  小美直发 满额包邮 有特殊的 现付场景，只在用户选择现付的时候 增加传参
            if ($shippingCode == 'fgaf' && !empty($prepay)) {
                $shippingResult = $obj->calculateAndModify($goodsWeight, $goodsAmount, $goodsNumber, $prepay); //
            } else {
                $shippingResult = $obj->calculateAndModify($goodsWeight, $goodsAmount, $goodsNumber); //
            }
            //  calculateAndModify
        }
        //  读取不到配置文件则默认为到付
        else {
            $shippingResult = [
                'shipping_fee' => 0,
                'shipping_code' => 'fpd',
                'shipping_id' => Shipping::getShippingIdByCode('fpd'),
            ];
        }

        Yii::warning(' 计算运费结果：$shippingResult = '.json_encode($shippingResult));
        return $shippingResult;
    }

    /**
     * 均摊 优惠活动 到子单的每个商品
     * @param $goodsGroup   子单信息 ['GoodsList' => 商品列表, 统计信息, 品牌名称, 运费信息]
     * @param $selectedCut  满减/优惠券 信息
     * @return mixed
     */
    public static function shareCut($goodsGroup, $selectedCut)
    {
        //  优惠金额不能大于商品总价
        if ($selectedCut['cut'] > $selectedCut['sumPrice']) {
            $selectedCut['cut'] = $selectedCut['sumPrice'];
        }

        $rate = $selectedCut['cut'] / $selectedCut['sumPrice'];
        foreach ($goodsGroup['goodsList'] as $k => $goods) {
            //  判定 商品在 优惠活动的生效范围里 才计算优惠
            if (in_array($goods['goods_id'], $selectedCut['eventToGoodsSelected'])) {
                //  计算每个商品的折扣，均摊到SKU的单价,设置商品参与的活动ID，累计到子单的 优惠金额
                $discount = $goods['goods_price'] * $goods['goods_number'] / $selectedCut['sumPrice'] * $selectedCut['cut'];
                $goodsGroup['discount'] += $discount;
                $goodsGroup['goodsList'][$k]['pay_price'] = $goods['goods_price'] * (1 - $rate);
                $goodsGroup['goodsList'][$k]['pay_price'] = NumberHelper::price_format($goodsGroup['goodsList'][$k]['pay_price']);
                $goodsGroup['goodsList'][$k]['event_id'] = $selectedCut['event']['event_id'];
            } else {
                $goodsGroup['goodsList'][$k]['pay_price'] = NumberHelper::price_format($goodsGroup['goodsList'][$k]['goods_price']);
            }
        }

        return $goodsGroup;
    }

    /**
     * 获取总单的配送信息 —— 已废弃，多个品牌商品的结算，无法总结整体的订单配送信息
     * @param $total
     * @param $orderCount
     * @param $prepay
     * @return mixed
     */
    public static function formatTotalShippingDesc($total, $orderCount, $prepay = 0)
    {
        $total['isDirectOnly'] = false;
        $total['shippingDesc'] = '运费到付';
        $total['shippingFeeDesc'] = '到付';

        if (!empty($total['hasDirectGoods'])) {
            if ($orderCount == 1) {
                $total['isDirectOnly'] = true;

                if (!empty($prepay)) {
                    if (!empty($total['shippingFee'])) {
                        $total['shippingDesc'] = '现付小美直发 ¥ '.$total['shippingFee'].'运费';
                        $total['shippingFeeDesc'] = '¥ '.$total['shippingFee'].'(现付)';
                    }  elseif (empty($total['shippingFee'])) {
                        //  prepay参数只在预付款有值的情况下出现
                        $total['shippingDesc'] = '现付小美直发 ¥ '.$total['shippingFee'].'运费';
                        $total['shippingFeeDesc'] = '¥ '.$total['shippingFee'].'(现付)';
                    }
                } elseif (empty($prepay)) {
                    //  用户选择了prepay 预付运费才会统计到  $total['shippingFee']
                    if (empty($total['preShippingFee'])) {
                        $total['shippingDesc'] = '小美直发包邮';
                        $total['shippingFeeDesc'] = '包邮';
                    } else {
                        $total['shippingDesc'] = '小美直发(到付运费)';
                        $total['shippingFeeDesc'] = '到付';
                    }

                }
            } elseif ($orderCount > 1) {
                if (!empty($prepay)) {
                    if (!empty($total['shippingFee'])) {
                        $total['shippingDesc'] = '现付小美直发 ¥ '.$total['shippingFee'].'运费';
                        $total['shippingFeeDesc'] = '¥ '.$total['shippingFee'];
                    } else {
                        //  $prepay 有值 则一定有运费
                    }
                } else {
                    if (empty($total['preShippingFee'])) {
                        $total['shippingDesc'] = '小美直发包邮';
                        $total['shippingFeeDesc'] = '直发包邮';
                    } else {
                        //  默认到付
                    }

                }
            }
        }

        return $total;
    }

    public static function formatActivityGoodsForBuyNow($activityGoods)
    {
        $shippingId = !empty($activityGoods->shipping->shipping_id)
            ? (int)$activityGoods->shipping->shipping_id
            : 3;

        $wuliaoList = [];   //  活动不影响物料配比
        if (!empty($activityGoods->extInfo['wuliaoEventList'])) {
            foreach ($activityGoods->extInfo['wuliaoEventList'] as $wuliaoEventList) {
                if (!empty($wuliaoEventList['wuliaoList'])) {
                    foreach ($wuliaoEventList['wuliaoList'] as $wuliao) {
                        if ($wuliao['goods_number'] >0) {
                            $wuliaoList[] = $wuliao;
                        }
                    }
                }
            }
        }

        $cartGoodsItem = [
            'goods_id' => (int)$activityGoods->goods_id,
            'goods_name' => (string)$activityGoods->goods->goods_name,
            'act_name' => (string)$activityGoods->act_name,
            'goods_sn' => (string)$activityGoods->goods->goods_sn,
            'product_id' => 0,
            'goods_number' => (int)$activityGoods->extInfo['buyNum'],
            'market_price' => NumberHelper::price_format($activityGoods->goods->market_price),
            'old_price' => NumberHelper::price_format($activityGoods->old_price),
            'goods_price' => NumberHelper::price_format($activityGoods->act_price),   //  计算优惠活动之前的结算价格
            'goods_attr' => '',
            'is_real' =>(int) $activityGoods->goods->is_real,
            'extension_code' => (string)GoodsActivity::$actTypeExtensionCodeMap[$activityGoods->act_type],
            'parent_id' => 0,
            'is_gift' => OrderGoods::IS_GIFT_NO,
            'event_id' => 0,
            'pay_price' => NumberHelper::price_format($activityGoods->act_price),    //  计算优惠活动之后的实际购买价格

            'selected' => (int)Cart::IS_SELECTED,
            'goodsSubTotal' => NumberHelper::price_format($activityGoods->act_price * $activityGoods->extInfo['buyNum']),  //小计
            'gift' => '',  //  团采不支持配置赠品，可在团采的 物料配比字段中 填写文本，物料配比会填写到订单备注中
            'wuliaoList' => $wuliaoList,
            'brand_name' => (string)$activityGoods->goods->brand['brand_name'],  //  品牌名称
            'brand_id' => (int)$activityGoods->goods->brand_id,  //  品牌ID
            'supplier_user_id' => (int)$activityGoods->goods->supplier_user_id,  //  供应商ID

            'goods_weight' => $activityGoods->goods->goods_weight,
            'goods_thumb' => ImageHelper::get_image_path($activityGoods->goods->goods_thumb),  //  缩略图
            'goods_img' => ImageHelper::get_image_path($activityGoods->goods->goods_img),

            'sample' => (string)$activityGoods->sample,   //  物料配比
            'canSelect' => 1,   //  商品是否可勾选
            'goodsMaxCanBuy' => (int)$activityGoods->extInfo['goodsMaxCanBuy'],
            'buy_by_box' => (int)$activityGoods->buy_by_box,
            'number_per_box' => (int)$activityGoods->number_per_box,
            'errorMsg' => '',

            'fullCut' => false,
            'measure_unit' => $activityGoods->goods->measure_unit
                ? (string)$activityGoods->goods->measure_unit
                : '件',

            //  'shipping_id' => (int)$shippingId,
        ];

        if ($cartGoodsItem['supplier_user_id'] > 0) {
            $brandOrSupplierId = (int)$cartGoodsItem['supplier_user_id'];
        } else {
            $brandOrSupplierId = (int)$cartGoodsItem['brand_id'];
        }

        //  标记是否参与满减
        if (
            !empty($activityGoods->extInfo['selectedCut']) &&
            $activityGoods->extInfo['selectedCut']['event']['event_type'] == (string)Event::EVENT_TYPE_FULL_CUT
        ) {
            $cartGoodsItem['fullCut'] = true;
        }

        $cartGoods[$brandOrSupplierId] = $activityGoods['extInfo'];
        $cartGoods[$brandOrSupplierId]['goodsList'][] = $cartGoodsItem;
        $cartGoods[$brandOrSupplierId]['hasValidGoods'] = 1;

        //  修正支付商品的品牌名称
        if ($brandOrSupplierId == '1257') {
            $cartGoods[$brandOrSupplierId]['brand_name'] = '小美直发';
            $cartGoods[$brandOrSupplierId]['brand_id'] = 0;
            $cartGoods[$brandOrSupplierId]['supplier_user_id'] = 1257;
        } else {
            $cartGoods[$brandOrSupplierId]['brand_name'] = (string)$activityGoods->goods->brand['brand_name'];
            $cartGoods[$brandOrSupplierId]['brand_id'] = (int)$brandOrSupplierId;
            $cartGoods[$brandOrSupplierId]['supplier_user_id'] = 0;
        }
        $cartGoods[$brandOrSupplierId]['allSelected'] = Cart::IS_SELECTED;
        $cartGoods[$brandOrSupplierId]['goodsWeight'] = (string)$activityGoods->goods['goods_weight'];
        $cartGoods[$brandOrSupplierId]['shipping_id'] = (int)$shippingId;
        $cartGoods[$brandOrSupplierId]['shipping_name'] = (string)Yii::$app->params['shippingIdShortDesc'][$shippingId];
        $cartGoods[$brandOrSupplierId]['discount'] = !empty($activityGoods->extInfo['selectedCut']['cut'])
            ? (string)$activityGoods->extInfo['selectedCut']['cut']
            : 0.00;
        $cartGoods[$brandOrSupplierId]['brandTotalNum'] = (int)$cartGoodsItem['goods_number'];
        $cartGoods[$brandOrSupplierId]['brandGoodsAmount'] = NumberHelper::price_format($cartGoodsItem['goodsSubTotal']);
        $cartGoods[$brandOrSupplierId]['brandTotalAmount'] = NumberHelper::price_format($cartGoodsItem['goodsSubTotal']);

        $total = [
            'cartAllSelected' => true,   //  默认购物车商品全部选中
            'goods_amount' => $cartGoodsItem['goodsSubTotal'],   //  默认购物车商品全部选中
            'remarks' => '',   //  默认购物车商品全部选中
            'discount' => NumberHelper::price_format($cartGoods[$brandOrSupplierId]['discount']),   //  默认没有优惠

            'fullCulEventList' => !empty($activityGoods->extInfo['fullCulEventList'])
                ? $activityGoods->extInfo['fullCulEventList']
                : [],
            'fullCulEventListFormat' => !empty($activityGoods->extInfo['fullCulEventListFormat'])
                ? $activityGoods->extInfo['fullCulEventListFormat']
                : [],
            'bestFullCut' => !empty($activityGoods->extInfo['bestFullCut'])
                ? $activityGoods->extInfo['bestFullCut']
                : [],

            'couponEventList' => !empty($activityGoods->extInfo['couponEventList'])
                ? $activityGoods->extInfo['couponEventList']
                : [],
            'couponEventListFormat' => !empty($activityGoods->extInfo['couponEventListFormat']) ?
                $activityGoods->extInfo['couponEventListFormat']
                : [],
            'bestCoupon' => !empty($activityGoods->extInfo['bestCoupon'])
                ? $activityGoods->extInfo['bestCoupon']
                : [],

            'selectedCut' => !empty($activityGoods->extInfo['selectedCut'])
                ? $activityGoods->extInfo['selectedCut']
                : [],
            'hasDirectGoods' => $activityGoods->goods['supplier_user_id'] == 1257 ? 1 : 0,    //  判定是否有 结算的直发商品

            'shippingFee' => 0.00,   //  默认没有运费
            'totalAmount' => 0.00,   //  默认总金额，每次用之前重新计算 = goods_amount + shippingFee - discount
        ];

        return [
            'cartGoods' => $cartGoods,
            'total' => $total,
        ];
    }

    /**
     * 统一 pc、m两站的下单 方法
     * @param $params
     * @param $extParams
     * @return array
     * @throws BadRequestHttpException
     */
    public static function createOrder($params, $extParams)
    {
        Yii::warning(' 入参: $params = '.json_encode($params), __METHOD__);
        $return = [
            'validAddress' => [],
            'order_uniq_id' => '',
            'orderList' => [],
            'error' => '',
            'redirect' => '',
        ];

        //  session没有extensionCode 表示 已结算过或通过非法途径提交订单
        if (
            empty($params['user_id']) ||
            empty($params['extensionCode']) ||
            (
                !in_array($params['extensionCode'], ['general', 'general_batch']) &&
                empty($extParams['buy_goods_num'])
                && empty($extParams['pkg_id'])
            )
        ) {
            switch ($params['platform']) {
                case 'm':
                    $return['redirect'] = '/default/flow/index.html';
                    break;
                case 'pc':
                    $return['redirect'] = '/flow.php';
                    break;
                default :
                    break;
            }
            $return['error'] = '非法访问';
            Yii::warning(' 返回参数 $return = '.json_encode($return), __METHOD__);
            return $return;
        }

        $prepay = $params['prepay']; //  用户是否选择了预付运费
        $useCouponId = $params['couponId'];  //  用户选择的优惠券

        //  【2】验证地址
        $validAddress = OrderGroupHelper::checkAddress($params['user_id'], $params['addressId']);
        $return['validAddress'] = $validAddress;
        if (empty($validAddress)) {
            //  跳错误页面   ——  要有提示信息
            switch ($params['platform']) {
                case 'm':
                    $return['redirect'] = '/default/user/address_list.html';
                    break;
                case 'pc':
                    $return['redirect'] = '/user.php?act=address_list';
                    break;
                default :
                    break;
            }
            $return['error'] = '请完善收货人信息';
            Yii::warning(' 返回参数 $return = '.json_encode($return), __METHOD__);
            return $return;
        }

        //  【3】验证订单 已处理过库存校验
        $rs = OrderGroupHelper::checkoutGoods(
            $params['user_id'],
            $params['extensionCode'],
            $validAddress,
            $prepay,
            $useCouponId,
            $extParams
        );
        $cartGoods  = $rs['cartGoods'];
        $total      = $rs['total'];

        //  如果结算商品信息为空，跳转到购物车
        if (empty($cartGoods)) {
            switch ($params['platform']) {
                case 'm':
                    $return['redirect'] = '/default/flow/index.html';
                    break;
                case 'pc':
                    $return['redirect'] = '/flow.php';
                    break;
                default :
                    break;
            }
            $return['error'] = '没有有效的结算商品';
            Yii::warning(' 返回参数 $return = '.json_encode($return), __METHOD__);
            return $return;
        }
        else {
            //  不同平台使用不同的默认支付方式
            switch ($params['platform']) {
                case 'm':
                    $defaultPayId = '3';    //  微信站里当前只支持微信支付
                    $payment = TouchPayment::find()->select(['pay_name'])->where(['pay_id' => $defaultPayId])->one();
                    $payName = htmlspecialchars($payment->pay_name);
                    break;
                case 'pc':
                    $defaultPayId = '5';    //  易宝支付
                    $payment = Payment::find()->select(['pay_name'])->where(['pay_id' => $defaultPayId])->one();
                    $payName =htmlspecialchars($payment->pay_name);
                    break;
                default :
                    break;
            }
        }

        //  如果使用库存，且下订单时减库存，则减少库存
        if (C('use_storage') == '1' && C('stock_dec_time') == 1) {
            $goods_stock = [];
            foreach ($cartGoods as $brandGoodsList) {
                foreach ($brandGoodsList['goodsList'] as $goods) {
                    if ($goods['selected'] == 1) {
                        $goods_stock[$goods['goods_id']] = $goods['goods_number'];
                    }
                }
            }
            Yii::warning(' 下单减库存 $goods_stock = '.json_encode($goods_stock), __METHOD__);
            $goodsIdList = array_keys($goods_stock);
            $goodsInfo = Goods::find()
                ->select(['goods_id', 'goods_name', 'goods_number'])
                ->where(['goods_id' => $goodsIdList])
                ->indexBy('goods_id')
                ->all();

            foreach ($goods_stock as $goodsId => $goodsNumber) {
                if (!empty($goodsInfo[$goodsId])) {
                    if ($goodsInfo[$goodsId]->goods_number < $goodsNumber) {
                        throw new BadRequestHttpException($goodsInfo[$goodsId]->goods_name.
                            ' 库存不足，当前最大可购买数量为 '.$goodsInfo[$goodsId]->goods_number, 200);
                    } else {
                        $goodsInfo[$goodsId]->goods_number = $goodsInfo[$goodsId]->goods_number - $goodsNumber;
                        $goodsInfo[$goodsId]->save();
                    }
                } else {
                    throw new BadRequestHttpException('非法请求', 500);
                }
            }
        }

        //  生成总单
        if (!empty($total['selectedCut']['cut'])) {
            $orderGroupEventId = $total['selectedCut']['event_id'];
            $orderGroupRuleId = $total['selectedCut']['rule_id'];
        } else {
            $orderGroupEventId = 0;
            $orderGroupRuleId = 0;
        }
        $order_uniq_id = OrderGroupHelper::getUniqidGroupId($params['user_id']);
        $return['order_uniq_id'] = $order_uniq_id;
        //  遍历生成子单
        if (!empty($cartGoods) && !empty($total)) {
            //  先生成总单， 总单的主键ID有写入到 子单中
            $transaction = ActiveRecord::getDb()->beginTransaction();
            try {
                $totalAmount = NumberHelper::price_format($total['totalAmount']);
                $gmtime = DateTimeHelper::gmtime();

                $orderGroup = new OrderGroup();
                $orderGroup['group_id'] = $order_uniq_id;
                $orderGroup['user_id'] = (int)$params['user_id'];
                $orderGroup['create_time'] = gmtime();
                $orderGroup['group_status'] = OrderGroup::ORDER_GROUP_STATUS_UNPAY;
                $orderGroup['event_id'] = (int)$orderGroupEventId;
                $orderGroup['rule_id'] = (int)$orderGroupRuleId;

                //  收货人信息
                foreach ($validAddress as $key => $value) {
                    if (in_array($key, [
                        'consignee',
                        'mobile',
                        'country',
                        'province',
                        'city',
                        'district',
                        'address',
                        'mobile',
                    ])) {
                        $orderGroup[$key] = addslashes($value);
                    }
                }

                $orderGroup['pay_id'] = 0;
                $orderGroup['pay_name'] = '未支付';
                $orderGroup['goods_amount'] = $total['goods_amount'];
                $orderGroup['shipping_fee'] = $total['shippingFee'];
                $orderGroup['money_paid'] = 0.00;
                $orderGroup['order_amount'] = $totalAmount;

                $orderGroup['pay_time'] = 0;
                $orderGroup['shipping_time'] = 0;
                $orderGroup['recv_time'] = 0;
                $orderGroup['discount'] = $total['discount'];
                $orderGroup['event_id'] = (int)$orderGroupEventId;
                $orderGroup['rule_id'] = (int)$orderGroupRuleId;

                Yii::warning('$orderGroup = '.VarDumper::dumpAsString($orderGroup), __METHOD__);
                if (!$orderGroup->save()) {
                    Yii::warning(
                        '总单入库失败: $orderGroup->errors = '.VarDumper::dumpAsString($orderGroup->errors),
                        __METHOD__
                    );
                    throw new ServerErrorHttpException('总单入库失败', 1);
                }

                //  如果使用了优惠券，修改优惠券状态
                if (
                    !empty($useCouponId) &&
                    !empty($orderGroupEventId) &&
                    !empty($total['selectedCut']['coupon_id']) &&
                    $total['selectedCut']['coupon_id'] == $useCouponId
                ) {
                    $updateRs = CouponRecord::updateAll(
                        [
                            'used_at' => $orderGroup['create_time'],
                            'group_id' => $orderGroup['group_id'],
                            'status' => CouponRecord::COUPON_STATUS_USED,
                        ],
                        [
                            'coupon_id' => $useCouponId,
                            'user_id' => $params['user_id'],
                        ]
                    );
                    if ($updateRs) {
                        Yii::warning('优惠券使用状态修改成功 group_id：' . $orderGroup['group_id'] . ' ; coupon_id = ' . $useCouponId, __METHOD__);
                    } else {
                        Yii::warning('优惠券使用状态修改失败 group_id：' . $orderGroup['group_id'] . ' ; coupon_id = ' . $useCouponId, __METHOD__);
                    }
                }

                //  设置订单的基础数据
                $orderBase = [
                    'group_id'          => $orderGroup->group_id,
                    'group_identity'    => $orderGroup->id,
                    'user_id'           => $orderGroup->user_id,

                    'order_status'      => OrderInfo::ORDER_STATUS_UNCONFIRMED,
                    'shipping_status'   => OrderInfo::SHIPPING_STATUS_UNSHIPPED,
                    'pay_status'        => OrderInfo::PAY_STATUS_UNPAYED,

                    'consignee'         => $orderGroup->consignee,
                    'country'           => $orderGroup->country,
                    'province'          => $orderGroup->province,
                    'city'              => $orderGroup->city,
                    'district'          => $orderGroup->district,
                    'address'           => $orderGroup->address,
                    'zipcode'           => $validAddress->zipcode,
                    'tel'               => $validAddress->tel,
                    'mobile'            => $validAddress->mobile,
                    'email'             => $validAddress->email,
                    'best_time'         => $validAddress->best_time,
                    'sign_building'     => $validAddress->sign_building,

                    'pay_id'            => $defaultPayId,
                    'pay_name'          => $payName,
                    'how_oos'           => '',
                    'how_surplus'       => '',
                    'pack_name'         => '',
                    'card_name'         => '',
                    'card_message'      => '',
                    'insure_fee'        => 0.00,  //  保价
                    'pay_fee'           => 0.00,  //  支付手续费
                    'pack_fee'          => 0.00,
                    'card_fee'          => 0.00,
                    'money_paid'        => 0.00,    //  默认未支付（未启用红包），积分兑换没走这个流程
                    'surplus'           => 0.00,    //  未启用余额支付
                    'integral'          => 0,       //  未启用积分支付
                    'integral_money'    => 0.00,    //  未启用积分支付
                    'bonus'             => 0.00,    //  未启用红包

                    'inv_payee'         => '',  //  发票费用
                    'inv_content'       => '',  //  发票明细
                    'inv_type'          => '',  //  发票类型
                    'tax'               => 0.00,  //  税额

                    'from_ad'           => $params['from_ad'],    //  站内广告引流
                    'referer'           => ''.$params['referer'], //  外部来源，未启用
                    'add_time'          => $gmtime,     //  与总单保持一致
                    'confirm_time'      => 0,           //  支付时修正确认时间
                    'recv_time'         => 0,

                    'pack_id'           => 0,
                    'card_id'           => 0,
                    'bonus_id'          => 0,
                    'invoice_no'        => '',  //  发票编号 未启用
                    'to_buyer'          => $total['remarks'] ? (string)$total['remarks'] : '',
                    'pay_note'          => '',
                    'agency_id'         => 0,   //  当前未启用代理机构
                    'is_separate'       => 0,   //  是否已分成
                    'parent_id'         => 0,   //  推荐人 ID  未启用分销

                    'discount'          => 0,   //  优惠金额，默认0
                    'mobile_pay'        => 0,   //  是否移动端支付 支付时修正
                    'mobile_order'      => 0,   //  是否移动端下单
                ];

                //  如果使用 办事处(分区域设置订单管理员)，则判定；   当前的订单分区域是在o_user_region 表中配置的
                if (1 == 0) {
                    $orderBase['agency_id'] = get_agency_by_regions(
                        [
                            $validAddress->country,
                            $validAddress->province,
                            $validAddress->city,
                            $validAddress->district,
                        ]
                    );
                }

                //  获取当前有效的团采商品列表
                $aliveGroupBuyList = GoodsActivity::aliveGroupBuyList();
                $groupBuyGoodsList = array_keys($aliveGroupBuyList);

                //  子单入库
                foreach ($cartGoods as $item) {
                    $order = $orderBase;

                    $order['order_sn'] = OrderGroupHelper::getUniqueOrderSn();
                    $order['postscript']    = $_POST['postscript'];    //  区分具体订单的备注，并插入订单商品的物料配比
                    $order['shipping_id']   = !empty($item['shipping_id']) ? (int)$item['shipping_id'] : 3;
                    $order['shipping_name'] =  !empty($item['shipping_name']) ? (string)$item['shipping_name'] : '到付';
                    $order['shipping_fee']  = !empty($prepay) ? $item['shipping_fee'] : 0.00;   // 用户选择现付运费 才入库

                    //  修正 小美支付未选中现付运费 的入库参数
                    if ($order['shipping_id'] == 4 && empty($order['shipping_fee'])) {
                        $order['shipping_id'] = 3;
                        $order['shipping_name'] = '到付';   //  小美直发(运费到付)
                    }

                    $order['goods_amount']  = $item['brandGoodsAmount'];

                    if ($params['extensionCode'] == 'gift_pkg') {
                        $order['discount'] = $orderGroup['discount'];
                        $order['order_amount'] = NumberHelper::price_format($orderGroup['order_amount']);
                    } else {
                        $order['discount']      = $item['discount'] ? NumberHelper::price_format($item['discount']) : 0;
                        $orderAmount = $order['goods_amount'] + $order['shipping_fee'] - $order['discount'];
                        $order['order_amount']  = NumberHelper::price_format($orderAmount);
                    }

                    $order['extension_code']    = !empty($params['extensionCode']) ? (string)$params['extensionCode'] : 'general';
                    $order['extension_id']      = $extParams['act_id'] ? (int)$extParams['act_id'] : 0;

                    $order['brand_id']      = $item['brand_id'];
                    $order['supplier_user_id']  = $item['supplier_user_id'];
                    $order['pay_id'] = 0;
                    $order['pay_name'] = '';

                    $return['orderList'][] = $order;

                    $orderModel = new OrderInfo();
                    $orderModel->setAttributes($order);

                    Yii::warning(' 子单入库前 $orderModel = '.VarDumper::dumpAsString($orderModel), __METHOD__);
                    if ($orderModel->save()) {
                        $order_id = $orderModel->order_id;
                        //  子单入库获取到 order_id， 遍历订单商品入库
                        foreach ($item['goodsList'] as $goods) {
                            $goods['order_id'] = $order_id;
                            $orderGoods = new OrderGoods();
                            $orderGoods->setAttributes($goods);

                            //  如果是团采商品，修正商品的 extension_code =  'group_buy'
                            if (in_array($orderGoods->goods_id, $groupBuyGoodsList)) {
                                $orderGoods->extension_code = 'group_buy';
                            }

                            if (!$orderGoods->save()) {
                                Yii::warning(__LINE__.'订单商品入库失败: $goods = '.json_encode($goods).
                                    '; $orderGoods->errors = '.json_encode($orderGoods->errors), __METHOD__);
                                throw new ServerErrorHttpException('订单商品入库失败', 3);
                            }

                            //  赠品入库
                            if (!empty($goods['gift'])) {
                                foreach ($goods['gift'] as $gift) {
                                    if ($gift['goods_number'] > 0) {
                                        $gift['order_id'] = $order_id;

                                        $orderGoodsGift = new OrderGoods();
                                        $orderGoodsGift->setAttributes($gift);

                                        Yii::warning(' 赠品入库前 $orderGoodsGift = '.VarDumper::dumpAsString($orderGoodsGift), __METHOD__);
                                        if (!$orderGoodsGift->save()) {
                                            Yii::warning(__LINE__.'订单商品入库失败: $gift = '.json_encode($gift).
                                                '; $orderGoodsGift->errors = '.json_encode($orderGoodsGift->errors), __METHOD__);
                                            throw new ServerErrorHttpException('订单赠品入库失败', 4);
                                        }
                                    }
                                }
                            }

                            //  物料入库
                            if (!empty($goods['wuliaoList'])) {
                                foreach ($goods['wuliaoList'] as $wuliao) {
                                    if ($wuliao['goods_number'] > 0) {
                                        $wuliao['order_id'] = $order_id;

                                        $orderGoodsGift = new OrderGoods();
                                        $orderGoodsGift->setAttributes($wuliao);

                                        Yii::warning(' 物料入库前 $orderGoodsGift = '.VarDumper::dumpAsString($orderGoodsGift), __METHOD__);
                                        if (!$orderGoodsGift->save()) {
                                            Yii::warning(__LINE__.'订单物料入库失败: $wuliao = '.json_encode($wuliao).
                                                '; $orderGoodsGift->errors = '.json_encode($orderGoodsGift->errors), __METHOD__);
                                            throw new ServerErrorHttpException('订单物料入库失败', 7);
                                        }
                                    }
                                }
                            }
                        }

                        //  支付记录入库
                        $payLog = new PayLog();
                        $payLog->order_id = $orderModel->order_id;
                        $payLog->order_amount = $orderModel->order_amount;
                        $payLog->order_type = 0;
                        $payLog->is_paid = 0;

                        Yii::warning(' 支付记录入库 $payLog = '.VarDumper::dumpAsString($payLog), __METHOD__);
                        if (!$payLog->save()) {
                            Yii::warning(__LINE__ . ' pay_log 支付记录入库失败 $payLog = ' . json_encode($payLog) .
                                '; $payLog->errors = ' . json_encode($orderModel->errors));
                            throw new ServerErrorHttpException('支付记录入库失败', 5);
                        }
                    } else {
                        Yii::warning(__LINE__ . ' 订单入库失败 $order = ' . json_encode($order) .
                            '; $orderModel = ' . json_encode($orderModel) .
                            '; $orderModel->errors = ' . json_encode($orderModel->errors), __METHOD__);
                        throw new ServerErrorHttpException('子单入库失败', 2);
                    }
                }

                $orderGroup->syncFeeInfo();
                if (!$orderGroup->save()) {
                    Yii::warning(__LINE__ . ' pay_log 支付记录入库失败 $payLog = ' . json_encode($payLog) .
                        '; $payLog->errors = ' . json_encode($orderModel->errors));
                    throw new ServerErrorHttpException('总单信息同步失败', 6);
                }

                //  通过购物车购买 成单后 清空采购车
                if ($params['extensionCode'] == 'general') {
                    OrderGroupHelper::clearCart($params['user_id']);
                }

                $transaction->commit();
            } catch (\Exception $exception) {
                $transaction->rollBack();
                Yii::warning('创建订单失败 $exception = '.VarDumper::export($exception).PHP_EOL.' json_encode $exception = '
                    .json_encode($exception->getTrace()), __METHOD__);
                switch ($params['platform']) {
                    case 'm':
                        $return['redirect'] = '/default/flow/index.html';
                        break;
                    case 'pc':
                        $return['redirect'] = '/flow.php';
                        break;
                    default :
                        break;
                }
                $return['error'] = '创建总单失败';
            } catch (\Throwable $throwable) {
                Yii::warning('创建订单失败 $throwable = '.VarDumper::export($throwable), __METHOD__);
                $transaction->rollBack();
            }
        }

        Yii::warning(' 返回参数 $return = '.json_encode($return), __METHOD__);
        return $return;
    }

    /**
     * 删除指定用户 购物车中已勾选的商品，
     * @param int $userId
     */
    public static function clearCart($userId)
    {
        Yii::warning('清空购物车: userId = '. $userId, __METHOD__);

        Cart::deleteAll([
            'user_id' => $userId,
            'selected' => Cart::IS_SELECTED,
        ]);
    }

    /**
     * 获取 商品(或商品列表)参与的 满减/优惠券 活动（去重）
     * 全局活动、直发活动、品牌活动、指定商品互动
     * @param $list = [$goodsEventList, $brandEventList, $allGoodsEvent, $zhiFaEvent]
     * @return array
     */
    public static function uniqueEventList($list)
    {
        $eventList = [];

        foreach ($list as $itemGroup) {
            if (!empty($itemGroup)) {
                foreach ($itemGroup as $event) {
                    $eventList[$event['event_id']] = $event;
                }
            }
        }

        return $eventList;
    }

    /**
     * 分配活动数据
     * 统计每个event 对应的 商品、价格
     * @param $event
     * @param $cart
     * @param $eventList
     */
    public static function assignEventList($event, $cart, &$eventList)
    {
        $eventList[$event->event_id]['event'] = $event;
        $eventList[$event->event_id]['eventToGoods'][] = $cart->goods_id;
        if ($cart->selected == Cart::IS_SELECTED) {
            $eventList[$event->event_id]['eventToGoodsSelected'][] = $cart->goods_id;

            $goodsAmount = $cart->goods_price * $cart->goods_number;
            if (!isset($eventList[$event->event_id]['sumPrice'])) {
                $eventList[$event->event_id]['sumPrice'] = $goodsAmount;
            } else {
                $eventList[$event->event_id]['sumPrice'] += $goodsAmount;
            }
        }
    }

    /**
     * 分配活动数据
     * 统计每个event 对应的 商品、价格
     * @param $event
     * @param $goods
     * @param $buyNumber    购买数量
     * @param $curPrice     当前购买数量对应的会员价格
     * @param $eventList
     */
    public static function assignEventListForGoodsBatch($event, $goods, $buyNumber, $curPrice, &$eventList)
    {
        $eventList[$event->event_id]['event'] = $event;
        $eventList[$event->event_id]['eventToGoods'][] = $goods->goods_id;

        $eventList[$event->event_id]['eventToGoodsSelected'][] = $goods->goods_id;

        $goodsAmount = $curPrice * $buyNumber;
        if (!isset($eventList[$event->event_id]['sumPrice'])) {
            $eventList[$event->event_id]['sumPrice'] = $goodsAmount;
        } else {
            $eventList[$event->event_id]['sumPrice'] += $goodsAmount;
        }
    }

    public static function sendCouponAfterPaid($outTradeNo) {
        Yii::warning('入参 outTradeNo '.$outTradeNo, __METHOD__);
        //派券
        if (strstr($outTradeNo, 'O')) {
            $expTradeNo = explode('O', $outTradeNo);
        }
        elseif (strstr($outTradeNo, 'A')) {
            $expTradeNo = explode('A', $outTradeNo);
        }
        elseif (strstr($outTradeNo, 'E')) {
            $expTradeNo = explode('E', $outTradeNo);
        }
        elseif (strstr($outTradeNo, 'Y')) {
            $expTradeNo = explode('Y', $outTradeNo);
        }
        else {
            return false;
        }

        if (count($expTradeNo) > 0) {
            $groupId = $expTradeNo[0];
            $orderGroup = OrderGroup::find()->joinWith([
                'orders orders',
            ])->where([
                OrderGroup::tableName(). '.group_id' => $groupId,
            ])->one();

            if (empty($orderGroup)) {
                Yii::error('总单未查到或未支付', __METHOD__);
                return false;
            }

            if ($orderGroup['group_status'] != OrderGroup::ORDER_GROUP_STATUS_PAID) {
                Yii::error('订单还未支付', __METHOD__);
                return false;
            }

            if (!empty($orderGroup['orders'])) {
                $order = $orderGroup['orders'][0];
                if ($order['extension_code'] == \common\models\OrderInfo::EXTENSION_CODE_GENERAL
                    || $order['extension_code'] == \common\models\OrderInfo::EXTENSION_CODE_GENERAL_BUY_NOW) {
                    //  送券
                    $totalPaid = $orderGroup->money_paid;
                    $paidCouponList = \common\models\PaidCoupon::find()->orderBy([
                        'amount' => SORT_DESC,
                    ])->all();

                    Yii::warning('符合派券条件 totalPaid = '.$totalPaid, __METHOD__);

                    foreach ($paidCouponList as $paidCoupon) {
                        $amount = $paidCoupon['amount'];
                        Yii::warning('amount = '. $amount. ', totalPaid = '. $totalPaid, __METHOD__);
                        if ($totalPaid >= $amount) {
                            if ($paidCoupon['event_id'] > 0 && $paidCoupon['rule_id'] > 0) {

                                $event = Event::find()->joinWith([
                                    'couponPkg couponPkg',
                                    'fullCutRule fullCutRule',
                                ])->where([
                                    'event_type' => Event::EVENT_TYPE_COUPON,
                                    'is_active' => 1,
                                    Event::tableName().'.event_id' => $paidCoupon['event_id'],
                                ])->one();

                                if (empty($event)) {
                                    Yii::error('缺少活动', __METHOD__);
                                    continue;
                                }

                                $event->takeCoupon($orderGroup['user_id'], $paidCoupon['rule_id']);
                            }
                            break;
                        }
                    }
                }
            }
            else {
                Yii::error('订单无子单', __METHOD__);
            }
        }
        return false;
    }

    /**
     * @param $userId
     * @param $extensionCode
     * @param $validAddress
     * @param $extParams
     * @param int $prepay
     * @return array
     */
    public static function checkoutGiftPkg($userId, $extensionCode, $validAddress, $extParams, $prepay = 0)
    {
        $formatGiftPkg = self::formartGiftPkg($extParams['pkg_id'], $extParams['pkg_num']);

        $cartGoods = $formatGiftPkg['cartGoods'];
        $total = $formatGiftPkg['total'];

        //  计算配送方式和运费
        $cartGoods = self::getOrderFee($extensionCode, $cartGoods, $validAddress, $prepay, $total, []);
        //  修正总单总价
        $total['totalAmount'] = NumberHelper::price_format($total['totalAmount'] + $total['shippingFee']);

        return [
            'cartGoods' => $cartGoods,
            'total'     => $total,
        ];
    }

    public static function formartGiftPkg($pkgId, $pkgNum)
    {
        $cartGoods = [];
        $total = [];
        if (!empty($pkgId) && !empty($pkgNum)) {
            $giftPkgModel = GiftPkg::find()
                ->joinWith([
                    'giftPkgGoods',
                    'giftPkgGoods.goods'
                ])->where([
                    GiftPkg::tableName().'.id' => $pkgId,
                    GiftPkg::tableName().'.is_on_sale' => GiftPkg::IS_ON_SALE,
                ])->one();
            if (empty($giftPkgModel->shipping_code)) {
                $giftPkgModel->shipping_code = Yii::$app->params['default_shipping_code'];
            }
            $orderShippingId = Shipping::getShippingIdByCode($giftPkgModel->shipping_code);

            if (!empty($giftPkgModel->giftPkgGoods)) {
                $total = [
                    'goods_amount' => 0,
                    'goods_weight' => 0,
                    'total_number' => 0,
                ];
                foreach ($giftPkgModel->giftPkgGoods as $item) {
                    //  物料配比
                    $sample = '';
                    if (!empty($item->goods->goodsAttr)) {
                        $goodsAttrFormat = Goods::assignGoodsAttr($item->goods->goodsAttr);

                        if (!empty($goodsAttrFormat['sample'])) {
                            $sample = $goodsAttrFormat['sample'];
                        }
                    }

                    if ($item->goods_num > 0) {
                        $cartGoodsItem = [
                            'goods_id' => (int)$item->goods_id,
                            'goods_number' => (int)$item->goods_num * $pkgNum,  //  整套购买
                            'goods_name' => (string)$item->goods->goods_name,
                            'goods_sn' => (string)$item->goods->goods_sn,
                            'product_id' => 0,
                            'market_price' => NumberHelper::price_format($item->goods->market_price),
                            'goods_price' => NumberHelper::price_format($item->goods->shop_price),   //  计算优惠活动之前的结算价格
                            'goods_attr' => '',
                            'is_real' => (int)$item->goods->is_real,
                            'extension_code' => (string)$item->goods->extension_code,
                            'parent_id' => 0,
                            'is_gift' => OrderGoods::IS_GIFT_NO,
                            'event_id' => 0,
                            'pay_price' => NumberHelper::price_format($item->goods->shop_price),
                            'is_on_sale' => (int)GiftPkg::IS_ON_SALE,
                            'is_delete' => 0,

                            'selected' => 1,
                            'goodsSubTotal' => NumberHelper::price_format($item->goods->shop_price * $item->goods_num * $pkgNum),  //小计
                            'gift' => [],  //  赠品
                            'brand_name' => (string)$item->goods->brand['brand_name'],  //  品牌名称    ——礼包互动结算页显示礼包的名称
                            'brand_id' => (int)$item->goods->brand_id,  //  品牌ID
                            'supplier_user_id' => (int)$item->goods->supplier_user_id,  //  供应商ID
                            'start_num' => (int)$item->goods->start_num,  //  供应商ID ——礼包不做判断
                            'goods_weight' => (string)$item->goods->goods_weight,
                            'goods_thumb' => ImageHelper::get_image_path($item->goods->goods_thumb),  //  缩略图
                            'goods_img' => ImageHelper::get_image_path($item->goods->goods_img),

                            'sample' => (string)$sample,   //  物料配比

                            'goodsMaxCanBuy' => (int)$item->goods->goods_number,
                            'buy_by_box' => (int)$item->goods->buy_by_box,
                            'number_per_box' => (int)$item->goods->number_per_box,
                            'errorMsg' => '',

                            'fullCut' => false,
                            'measure_unit' => $item->goods->measure_unit ? (string)$item->goods->measure_unit : '件',
                        ];

                        if ($cartGoodsItem['supplier_user_id'] > 0) {
                            $brandOrSupplierId = (int)$cartGoodsItem['supplier_user_id'];
                        } else {
                            $brandOrSupplierId = (int)$cartGoodsItem['brand_id'];
                        }

                        $cartGoods[$brandOrSupplierId]['goodsList'][] = $cartGoodsItem;

                        //  子单商品件数
                        if (!isset($cartGoods[$brandOrSupplierId]['brandTotalNum'])) {
                            $cartGoods[$brandOrSupplierId]['brandTotalNum'] = $cartGoodsItem['goods_number'];
                            $cartGoods[$brandOrSupplierId]['brandGoodsAmount'] = NumberHelper::price_format($cartGoodsItem['goodsSubTotal']);
                            $cartGoods[$brandOrSupplierId]['brandTotalAmount'] = NumberHelper::price_format($cartGoodsItem['goodsSubTotal']);
                        } else {
                            $cartGoods[$brandOrSupplierId]['brandTotalNum'] += $cartGoodsItem['goods_number'];
                            $cartGoods[$brandOrSupplierId]['brandGoodsAmount'] = NumberHelper::price_format($cartGoods[$brandOrSupplierId]['brandGoodsAmount'] + $cartGoodsItem['goodsSubTotal']);
                            $cartGoods[$brandOrSupplierId]['brandTotalAmount'] = NumberHelper::price_format($cartGoods[$brandOrSupplierId]['brandTotalAmount'] + $cartGoodsItem['goodsSubTotal']);
                        }

                        //  修正支付商品的品牌名称
                        if ($brandOrSupplierId == 1257) {
                            $cartGoods[$brandOrSupplierId]['brand_id'] = 0;
                            $cartGoods[$brandOrSupplierId]['supplier_user_id'] = 1257;
                        } else {
                            $cartGoods[$brandOrSupplierId]['brand_id'] = $brandOrSupplierId;
                            $cartGoods[$brandOrSupplierId]['supplier_user_id'] = 0;
                        }
                        $cartGoods[$brandOrSupplierId]['brand_name'] = (string)$giftPkgModel->name; //  礼包活动的结算页显示活动名称
                        $cartGoods[$brandOrSupplierId]['shipping_id'] = (int)$orderShippingId; //  礼包活动的结算页显示活动名称
                        $cartGoods[$brandOrSupplierId]['shipping_name'] = (string)Yii::$app->params['shippingIdShortDesc'][$orderShippingId]; //

                        //  礼包活动在结算的是要当成一个商品展示
                        $cartGoods[$brandOrSupplierId]['img'] = (string)$giftPkgModel->getUploadUrl('img');
                        $cartGoods[$brandOrSupplierId]['name'] = (string)$giftPkgModel->name;
                        $cartGoods[$brandOrSupplierId]['price'] = NumberHelper::price_format($giftPkgModel->price);
                        $cartGoods[$brandOrSupplierId]['pkgNum'] = (int)$pkgNum;

                        $total['goods_amount'] += $cartGoodsItem['goodsSubTotal'];
                        $total['goods_weight'] += $cartGoodsItem['goods_weight'];
                        $total['total_number'] += $cartGoodsItem['goods_number'];
                    }
                }

                $giftPkgAmount = $giftPkgModel->price * $pkgNum;
                $total['giftPkgSavePrice'] = NumberHelper::price_format($total['goods_amount'] - $giftPkgAmount);
                $total['discount'] = $total['giftPkgSavePrice'];
                $total['totalAmount'] = NumberHelper::price_format($giftPkgAmount);
                $total['goods_amount'] = NumberHelper::price_format($total['goods_amount']);
                $total['goods_weight'] = NumberHelper::price_format($total['goods_weight']);
                $total['total_number'] = (int)$total['total_number'];

                //  均摊 pay_price    礼包$cartGoods 只有一个元素
                $rate = $giftPkgAmount / $total['goods_amount'];
                foreach ($cartGoods as $brandOrSupplierId => $goodsGroup) {
                    foreach ($cartGoods[$brandOrSupplierId]['goodsList'] as  $key => $goodsItem) {
                        $payPrice = NumberHelper::price_format($goodsItem['pay_price'] * $rate);
                        $cartGoods[$brandOrSupplierId]['goodsList'][$key]['pay_price'] = $payPrice;
                    }
                }
            }
        }

        return [
            'cartGoods' => $cartGoods,
            'total'     => $total,
        ];
    }

    /**
     * 校验商品列表是否都可以购买
     * @param $goodsArr [['goods_id' => $goods_id, 'goods_number' => $goods_number],...]
     * @param $userId
     * @return array|Goods[]
     */
    public static function checkGoodsListCanBuy($goodsArr, $userId)
    {
        $result = [];

        //  获取 尚未到货的 提醒
        $goodsList = Goods::find()
            ->joinWith([
                'arrivalReminder' => function ($query) use ($userId) {
                    return $query->andOnCondition([
                        'user_id' => $userId,
                        'status' => ArrivalReminder::NOT_ARRIVAL,
                    ]);
                }
            ])
            ->where(['goods_id' => array_keys($goodsArr)])
            ->all();

        //  已下架的 | 库存不足的 | 其他（有购买数量的，没有购买数量的）
        foreach ($goodsList as $goods) {
            $reminder = 0;      //  不考虑到货通知

            if (!$goods->is_on_sale || $goods->is_delete) {
                $result[$goods->goods_id] = [
                    'msg' => '商品已下架',
                    'canSelect' => false,
                    'resetGoodsNumber' => 0,
                    'reminder' => $reminder,
                ];
            }
            //  库存不足  区分是否 已设置到货提醒
            elseif ($goods->start_num > $goods->goods_number) {
                if (!empty($goods->arrivalReminder)) {
                    $reminder = 1;  //  已设置 到货通知
                } else {
                    $reminder = 2;  //  提示用户 可设置到货通知
                }
                $result[$goods->goods_id]  = [
                    'msg' => '库存不足',
                    'canSelect' => false,
                    'resetGoodsNumber' => 0,
                    'reminder' => $reminder,
                ];
            } else {
                if ($goodsArr[$goods->goods_id] > 0) {
                    $canBuyMax = $goods->goods_number;
                    $resetGoodsNumber = $goodsArr[$goods->goods_id];
                    if ($goods->buy_by_box) {
                        $canBuyMax = floor($goods->goods_number / $goods->number_per_box) * $goods->number_per_box;

                        if ($goodsArr[$goods->goods_id] <= $canBuyMax && $goodsArr[$goods->goods_id] >= $goods->start_num) {
                            $canBuyMax = floor($goods->goods_number / $goods->number_per_box) * $goods->number_per_box;
                            $resetGoodsNumber = floor($goodsArr[$goods->goods_id] / $goods->number_per_box) * $goods->number_per_box;

                            $msg = '可以购买';

                        } else {
                            $resetGoodsNumber = $canBuyMax;
                            $msg = '购买量超出库存';
                        }
                    }

                    $result[$goods->goods_id] = [
                        'msg' => $msg,
                        'canSelect' => true,
                        'resetGoodsNumber' => $resetGoodsNumber,    //  修正购买数量
                        'reminder' => $reminder,

                        'canBuyMax' => $canBuyMax,  //  最大可购买数量
                    ];

                }
                //  未选择数量的商品
                else {
                    if (!empty($goods->arrivalReminder)) {
                        $reminder = 1;  //  已设置 到货通知
                    } elseif ($goods->start_num > $goods->goods_number) {
                        $reminder = 2;  //  提示用户 可设置到货通知
                    }

                    $result[$goods->goods_id]  = [
                        'msg' => '可以购买',
                        'canSelect' => true,
                        'resetGoodsNumber' => 0,
                        'reminder' => $reminder,
                    ];
                }
            }

        }

        return $result;
    }
}