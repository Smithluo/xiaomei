<?php
/**
 * Created by PhpStorm.
 * User: clark
 * Date: 2017/1/5
 * Time: 14:55
 */

namespace common\helper;

use common\models\CouponRecord;
use common\models\Event;
use common\models\EventRule;
use common\models\EventToGoods;
use common\models\FullCutRule;
use common\models\Goods;
use common\models\OrderGoods;
use common\models\Users;
use Yii;
use yii\web\BadRequestHttpException;

class EventHelper
{
    /**
     * 检查商品参与的活动
     * @param $cart_goods
     * @return array
     */
    public static function checkEvent($cart_goods, $userId) {
        $goodsMap = [];
        $event = [];
        $fullCut = [];
        $gifts = [];
        $coupon = [];
        $goodsTagCoupon = [];   //  要显示券标的商品

        foreach ($cart_goods as $goods) {
            $goodsMap[] = [
                'goods_id'      => $goods['goods_id'],
                'goods_number'  => $goods['goods_number'],
                'selected'      => $goods['selected'],
            ];
        }

        if (!empty($goodsMap)) {
            try {
                $validEvents = self::formatValidEvents($goodsMap, $userId);
            } catch (Exception $e) {
                ToLog(5, __FUNCTION__.' 获取商品的活动信息失败');
                $validEvents = [];
            }

            if (!empty($validEvents['gifts'])) {
                $gifts = $validEvents['gifts'];
            }

            if (!empty($validEvents['fullCut'])) {
                $event = current($validEvents['fullCut']);
                $fullCut = self::getFullCutMsg($event);
            }

            if (!empty($validEvents['couponList'])) {
                $coupon = $validEvents['couponList'];
                Yii::warning(__LINE__.'可用的优惠券列表：$coupon = '.json_encode($coupon));

                foreach ($coupon as $item) {
                    if (empty($goodsTagCoupon)) {
                        $goodsTagCoupon = array_values($item['eventToGoods']);
                    } else {
                        $goodsTagCoupon = array_merge($goodsTagCoupon, array_values($item['eventToGoods']));
                    }
                }
                $goodsTagCoupon = array_unique($goodsTagCoupon);
                Yii::warning(__LINE__.'参与优惠券活动的商品列表，用于显示券标：$goodsTagCoupon = '.json_encode($goodsTagCoupon));
            }
        }

        return [
            'event'             => $event,
            'fullCut'           => $fullCut,
            'gifts'             => $gifts,
            'coupon'            => $coupon,
            'goodsTagCoupon'    => $goodsTagCoupon,
        ];
    }

    /**
     * 获取满减活动的达成情况: 可减额度，提示语
     * @param array $event  满减活动的详情 | event/valid-events 结果集的 $validEvents['data']['fullCut'] 的子元素
     * @return string
     */
    public static function getFullCutMsg($event)
    {
        usort($event['eventRule'], function ($a, $b){
            if ($a['above'] == $b['above']) {
                return 0;
            } else {
                return (float)$a['above'] > (float)$b['above'] ? 1 : -1;
            }
        });
        $cut = 0;
        $cutMsg = '';
        $notMatchMsg = '';
        foreach ($event['eventRule'] as $rule) {
            if ((float)$event['sumPrice'] >= (float)$rule['above'] && $rule['cut'] > $cut) {
                $cut = NumberHelper::price_format($rule['cut']);
                $event['match'] = $rule;
                $cutMsg = '优惠 ¥'.$cut;
            } else {
                if (!isset($event['notMatch'])) {
                    $event['notMatch'] = $rule;
                } elseif ((float)$event['notMatch']['above'] > (float)$rule['above']) {
                    $event['notMatch'] = $rule;
                }
                $sub = $event['notMatch']['above'] - $event['sumPrice'];
                $notMatchMsg = ', 再购 ¥'.NumberHelper::price_format($sub).
                    ' 可优惠 <em>¥'.NumberHelper::price_format($event['notMatch']['cut']).' </em>';
            }
        }

        $event['cut'] = $cut;
        $event['sumPrice'] = NumberHelper::price_format($event['sumPrice']);
        $event['goodsAmount'] = NumberHelper::price_format($event['goodsAmount']);

        //  如果当前满减商品已满足最大数量，则不显示 去凑单的链接
//        $event['fullCutMsg'] = '已购满减专区产品 ¥'.$event['sumPrice'].' '.$cutMsg.' '.$notMatchMsg;
        if (!empty($event['notMatch'])) {
            $event['fullCutMsg'] = '已购满减专区产品 ¥'.$event['sumPrice'].$notMatchMsg;

            $event['mFullCutMsg'] = '<a href="default/activity/hot.html?activeIndex=4">'.
                $event['fullCutMsg'].
                '<span>去凑单></span></a>';
//            $event['pcFullCutMsg'] = $event['fullCutMsg'].'<a href="/topic_full_cut.php">去凑单></a>';
            $event['pcFullCutMsg'] = '<a href="/topic_full_cut.php">'.$event['fullCutMsg'].'<span>去凑单></span></a>';
        } else {
            $event['mFullCutMsg'] = '已购满减专区产品 ¥'.$event['sumPrice'].' '.$cutMsg;
            $event['pcFullCutMsg'] = '已购满减专区产品 ¥'.$event['sumPrice'].' '.$cutMsg;
        }

        return $event;
    }

    /**
     * 处理满赠、满减活动信息
     * @param $goodsMap [[goods_id,goods_number,selected]]
     * @param $brand_goods_list []  可以不传
     * @return mixed    $brand_goods_list
     */
    public static function processEvent($goodsMap, $brand_goods_list, $userId) {
        Yii::warning(__FILE__.' | '.__FUNCTION__.'处理满赠、满减、优惠券开始 参数列表：$goodsMap = '.json_encode($goodsMap).
            ' ; $brand_goods_list = '.json_encode($brand_goods_list).' ; $userId = '.$userId);

        //  满赠、满减、优惠券 处理——开始
        $gifts = [];
        $fullCut = [];
        $coupon = [];
        $goodsTagCoupon = [];   //  要显示券标的商品

        if (!empty($goodsMap)) {
            //  获取当前有效的活动
            $validEvents = EventHelper::formatValidEvents($goodsMap, $userId);
            Yii::warning(__LINE__.'获取当前有效的活动：'.json_encode($validEvents));
            if (!empty($validEvents['gifts'])) {
                $gifts = $validEvents['gifts'];
            }

            if (!empty($validEvents['fullCut'])) {
                $event = current($validEvents['fullCut']);
                $fullCut = EventHelper::getFullCutMsg($event);
            }

            if (!empty($validEvents['couponList'])) {
                $coupon = $validEvents['couponList'];
                Yii::warning(__LINE__.'可用的优惠券列表：$coupon = '.json_encode($coupon));

                foreach ($coupon as $item) {
                    if (empty($goodsTagCoupon)) {
                        $goodsTagCoupon = array_values($item['eventToGoods']);
                    } else {
                        $goodsTagCoupon = array_merge($goodsTagCoupon, array_values($item['eventToGoods']));
                    }
                }
                $goodsTagCoupon = array_unique($goodsTagCoupon);
                Yii::warning(__LINE__.'参与优惠券活动的商品列表，用于显示券标：$goodsTagCoupon = '.json_encode($goodsTagCoupon));
            }
        }

        //  满赠、满减、优惠券处理 数据分配
        if ((!empty($gifts) || !empty($fullCut) || !empty($coupon)) && !empty($brand_goods_list)) {
            foreach($brand_goods_list as $brand_id => $goods_list) {
                foreach($goods_list as $k => $goods) {
                    //  显示满减标签
                    if (!empty($fullCut) && in_array($goods['goods_id'], $fullCut['eventToGoods'])) {
                        $brand_goods_list[$brand_id][$k]['fullCut'] = true;
                    } else {
                        $brand_goods_list[$brand_id][$k]['fullCut'] = false;
                    }

                    //  分配赠品
                    if (!empty($gifts) && !empty($gifts[$goods['goods_id']])) {
                        $brand_goods_list[$brand_id][$k]['gifts'] = $gifts[$goods['goods_id']];
                    } else {
                        $brand_goods_list[$brand_id][$k]['gifts'] = [];
                    }

                    //  显示参与优惠券活动的标签
                    if (!empty($coupon) && in_array($goods['goods_id'], $goodsTagCoupon)) {
                        $brand_goods_list[$brand_id][$k]['coupon'] = true;
                    } else {
                        $brand_goods_list[$brand_id][$k]['coupon'] = false;
                    }
                }
            }
        }

        Yii::warning(__LINE__.'获取返回数据：brand_goods_list：'.json_encode($brand_goods_list).' fullCut：'.json_encode($fullCut));

        return [
            'brand_goods_list' => $brand_goods_list,
            'fullCut' => $fullCut,
            'coupon' => $coupon,
//            'gifts' => $gifts,
        ];;
    }


    /**
     * 依据 提交的 商品id\购买数量 获取商品参与活动的信息
     *
     * @param array $goodsMap [ ['goods_id' => $goods_id, 'goods_number' => 要购买的商品数量]]
     * @return array
     */
    public static function formatValidEvents($goodsMap, $userId, $time = 0) {
        Yii::warning(__FILE__.' | '.__FUNCTION__.'获取商品参与活动的信息开始 $goodsMap'.json_encode($goodsMap).
            ' ; $userId = '.$userId.' ; $time = '.$time);
        $gifts      = [];
        $fullCut    = [];
        $couponList = [];
        $giftMap    = [];
        $fullCutMap = [];

        $user = Users::findOne(['user_id' => $userId]);
        if ($user) {
            $userRank = $user->user_rank;
        }

        if (!empty($goodsMap) && !empty($userRank)) {
            try {
                if (empty($time)) {
                    $time = date('Y-m-d H:i:s', time());
                } elseif (is_numeric($time)) {
                    $time = date('Y-m-d H:i:s', $time);
                }
                $validEvents = self::getValidEventList($goodsMap, $userRank, $time, $userId);
                Yii::warning(__LINE__.'获取商品参与活动 $validEvents: '.json_encode($validEvents));
            } catch (Exception $e) {
                ToLog(5, __FUNCTION__.' 获取商品的活动信息失败');
                $validEvents = [];
            }
        }

        if (!empty($validEvents)) {
            $giftMap    = $validEvents['gifts'];
            $fullCutMap = $validEvents['fullCut'];
            $couponList = $validEvents['coupon'];

            if (!empty($giftMap)) {
                foreach ($giftMap as $gift) {
                    $gifts[$gift['parent_id']] = $gift;
                }
            }

            if (!empty($fullCutMap)) {
                foreach ($fullCutMap as $event) {
                    $event = self::getFullCutMsg($event);
                    if (!empty($event['eventToGoods'])) {
                        $fullCut[$event['event_id']] = $event;
                        //  如果一个商品同时参与多个满减活动，则只会在一个活动中参与满减的计算
//                    foreach ($event['eventToGoods'] as $goodsId) {
//                        $fullCutGoodsMap[$goodsId] = $event['event_id'];
//                    }
                    }
                }
            }
        }

        $data = [
            'giftMap'       => $giftMap,
            'fullCutMap'    => $fullCutMap,
            'couponList'    => $couponList,
            'gifts'         => $gifts,
            'fullCut'       => $fullCut,
        ];

        Yii::warning(__LINE__.'获取商品参与活动 返回结果: '.json_encode($data));

        return $data;
    }

    /**
     * 优惠活动【满减、优惠券】 按参与优惠活动的商品总价 占 参与优惠活动的所有商品总价的比例 拆分到订单中,每个参与优惠的SKU 折算单价
     *
     * //   优惠活动信息
     * @param array $fullCut = Event; ['cut' => '优惠总金额'， 'sumPrice' => '参与优惠活动的商品总金额', ...]
     * @param array $brand_list   按供应商、品牌 分组后的商品列表 [['brand_or_supplier_id' => [Goods1, Goods2], ...],
     * @return array $fullCutMap = [
     *  $brand_or_supplier_id => [
     *      'discount' => 子订单减免的金额,
     *      'fullCutGoodsList' => 参与满减的商品id列表
     *  ],
     *  ...
     * ]
     */
    public static function assignFullCut($fullCut, &$brand_list)
    {
        Yii::warning(__CLASS__.' | '.__FUNCTION__.'计算满减、优惠券金额，均摊到每个SKU 开始 $fullCut = '.json_encode($fullCut).
            ' ; $brand_list = '.json_encode($brand_list));
        $fullCutMap = [];
        $sumDiscount = 0.00;
        //  如果有优惠活动 累计到$total['discount']
        if (!empty($fullCut['cut'])) {
            //  修正优惠额度
            if ($fullCut['cut'] > $fullCut['sumPrice']) {
                $fullCut['cut'] = $fullCut['sumPrice'];
            }

            foreach ($brand_list as $brand_or_supplier_id => &$goods_list) {
                $fullCutGoodsAmount = 0;    //  每个订单中参与优惠活动的商品总价
                foreach ($goods_list as &$goods) {
                    if (!empty($fullCut) && !empty($fullCut['cut']) && in_array($goods['goods_id'], $fullCut['eventToGoods'])) {
                        $fullCutMap[$brand_or_supplier_id]['fullCutGoodsList'][] = $goods['goods_id'];
                        $goodsAmount = bcmul($goods['goods_price'], $goods['goods_number'], 2);
                        $fullCutGoodsAmount = bcadd($fullCutGoodsAmount, $goodsAmount, 2);

                        //  折算商品的单价 即 商品结算价 - 每个商品的单价 在 参与优惠的商品总金额内占的比例 乘以 优惠的金额
                        $goodsDiscountShare = $goods['goods_price'] / $fullCut['sumPrice'] * $fullCut['cut'];

                        $goods['event_id'] = $fullCut['event_id'];
                        $goods['pay_price'] = NumberHelper::price_format($goods['goods_price'] - $goodsDiscountShare);
//                        bcsub误差大于普通计算
//                        $goods['pay_price'] = bcsub($goods['goods_price'], $goodsDiscountShare, 2);
//                        Yii::trace(__FUNCTION__.'goodsDiscountShare: '.$goodsDiscountShare.
//                            ' | pay_price :'.$goods['pay_price'].
//                            ' | goods_price :'.$goods['goods_price'].
//                            ' | 普通方法计算：'.NumberHelper::price_format($goods['goods_price'] - $goodsDiscountShare)
//                        );
                    }
                }
                if (!empty($fullCutGoodsAmount)) {
                    //  每个订单中参与优惠活动的商品 均摊到的优惠额度
                    $fullCutMap[$brand_or_supplier_id]['discount'] = NumberHelper::price_format($fullCut['cut'] * $fullCutGoodsAmount / $fullCut['sumPrice']);

                    $last_brand_or_supplier_id = $brand_or_supplier_id;
                } else {
                    $fullCutMap[$brand_or_supplier_id]['discount'] = 0.00;
                }

                $sumDiscount = NumberHelper::price_format($sumDiscount + $fullCutMap[$brand_or_supplier_id]['discount']);
            }

            //  修正均摊的优惠额度，修正到最后一个参与优惠的订单上
            $fullCutDiff = NumberHelper::price_format($fullCut['cut'] - $sumDiscount);
            if ($fullCutDiff != 0) {
                $fullCutMap[$last_brand_or_supplier_id]['discount'] = NumberHelper::price_format($fullCutDiff +
                    $fullCutMap[$last_brand_or_supplier_id]['discount']);
            }
        }

        Yii::warning('计算满减、优惠券金额，均摊到每个SKU 结束 $fullCutMap = '.json_encode
            ($fullCutMap).' ; $brand_list = '.json_encode($brand_list), __METHOD__);
        return $fullCutMap;
    }


    /**
     * 如果有参与满减活动，修正结算价格
     * @param $fullCut
     * @param $order
     * @param $total
     * @return array
     */
    public static function correctPrice($fullCut, $order, $total) {
        if ($fullCut['cut'] > 0) {
            if (isset($order['discount'])) {
                $order['discount'] = bcadd($order ['discount'], $fullCut['cut'], 2);
            }

            if (!isset($total['discount'])) {
                $total['discount'] = 0.00;
            }
            $total['discount'] = bcadd($total['discount'], $fullCut['cut'], 2);

            if (isset($total['amount'])) {
                $total['amount'] = bcsub($total['amount'], $fullCut['cut'], 2);
            }

            if (isset($total['total_amount'])) {
                $total['total_amount'] = bcsub($total['total_amount'], $fullCut['cut'], 2);
            }
        }

        return [
            'order' => $order,
            'total' => $total,
        ];
    }

    /**
     * 通过 商品ID列表 获取 当前生效的活动列表 满赠、满减、优惠券
     *
     * @param $goodsMap =
     * [
     *      [
     *          'goods_id'      => $goods_id,
     *          'goods_number'  => $goods_number,
     *          'selected'      => 1,
     *      ],
     *      [
     *          'goods_id'      => $goods_id,
     *          'goods_number'  => $goods_number,
     *          'selected'      => 0,
     *      ]
     * [
     * @param $userRank 会员等级
     * @param int $time 当前时间 或 下单时间 判断是否在活动时段内
     * @param int $userId 用户ID
     * @return array    [
     *      'gifts'     => [[赠品信息], [...]],
     *      'fullCut'   => [满减活动的所有信息],
     * ]
     * @throws BadRequestHttpException
     */
    public static function getValidEventList($goodsMap, $userRank, $time, $userId = null)
    {
        Yii::warning(__CLASS__.' | '.__FUNCTION__.' params: $goodsMap = '.json_encode($goodsMap).
            ' ; $userRank = '.$userRank.' ; $time = '.$time.' ; $userId ='.$userId);

        $rs = [
            'gifts'     => [],
            'fullCut'   => [],
            'coupon'    => [],
        ];
        $couponEventList = [];

        if (!$time) {
            $time = date('Y-m-d H:i:s', time());
        } elseif (is_numeric($time)) {
            $time = date('Y-m-d H:i:s', $time);
        }
        $goodsIdList = [];  //  需要判断库存的商品ID列表
        $selectedGoodsList = [];    //  选中的商品列表
        foreach ($goodsMap as $goods) {
            $goodsList[] = $goods['goods_id'];
            $goodsIdNumMap[$goods['goods_id']] = $goods['goods_number'];
	    //  未选中的商品不参与活动计算
            if ($goods['selected']) {
                $selectedGoodsList[] = $goods['goods_id'];
            }
        }

        //  获取当前有效的活动
        $map = Event::find()
            ->joinWith('eventToGoods')
            ->where(['is_active' => Event::IS_ACTIVE])
            ->andWhere(['<=', 'start_time', $time])
            ->andWhere(['>=', 'end_time', $time])
            ->andWhere([EventToGoods::tableName().'.goods_id' => $goodsList])
            ->all();

        Yii::warning('获取当前有效的活动, $map = '.json_encode($map));
        //  区分满赠和满减活动，满正活动可能有多个，满减活动也可能有多个
        if (!empty($map)) {
            $priceMap = GoodsHelper::getGoodsPriceMapForBuy($goodsMap, $userRank);

            foreach ($map as $item) {
                if ($item->event_type == Event::EVENT_TYPE_FULL_GIFT) {
                    $giftsEvent = self::formatEvent($item, $goodsList, $selectedGoodsList);

                    $giftsEvent['eventRule'] = $item->fullGiftRule;
                    $goodsIdList[] = $giftsEvent['eventRule']['gift_id'];

                    if (!empty($giftsEvent['eventToGoods'])) {
                        $goodsIdList = array_merge($goodsIdList, $giftsEvent['eventToGoods']);
                    }

                    $rs['giftsEvent'][] = $giftsEvent;
                }
                elseif ($item->event_type == Event::EVENT_TYPE_FULL_CUT) {
                    $fullCut = self::formatEvent($item, $goodsList, $selectedGoodsList);
                    if (!empty($fullCut['eventToGoods'])) {
                        if (empty($goodsIdList)) {
                            $goodsIdList = $fullCut['eventToGoods'];
                        } else {
                            $goodsIdList = array_merge($goodsIdList, $fullCut['eventToGoods']);
                        }
                    }

                    $rs['fullCut'][] = $fullCut;
                }
                elseif ($item->event_type == Event::EVENT_TYPE_COUPON) {
                    $couponEventList[$item->event_id] = self::formatEvent($item, $goodsList, $selectedGoodsList);
                }
            }

            $goodsIdList = array_unique($goodsIdList);
            $goodsInfo = Goods::find()
                ->select(['goods_id', 'goods_number', 'goods_name', 'goods_thumb', 'is_real'])
                ->where(['goods_id' => $goodsIdList])
                ->all();
            foreach ($goodsInfo as $goods) {
                if (
                    isset($goodsIdNumMap[$goods->goods_id]) &&
                    in_array($goods->goods_id, $selectedGoodsList) &&
                    $goodsIdNumMap[$goods->goods_id] > $goods->goods_number
                ) {
                    //  要购买的商品库存不足（赠品不在这里判断）
                    $msg = '商品 '.$goods->goods_name.' 库存不足，实时库存为：'.$goods->goods_number.', 请修改够购买数量';
                    throw new BadRequestHttpException($msg, 11);
                } else {
                    $goodsInfoMap[$goods->goods_id] = [
                        'goods_number' => $goods->goods_number,
                        'goods_name' => $goods->goods_name,
                        'goods_thumb' => $goods->goods_thumb,
                        'is_real' => $goods->is_real,
                    ];
                }
            }

            //  如果有满赠活动，计算赠品库存是否充足，格式化 赠品信息
            $gifts = [];
            if (!empty($rs['giftsEvent'])) {
                //  获取满赠活动的赠品信息，校验库存
                foreach ($rs['giftsEvent'] as &$event) {
                    $event['gift'] = [];
                    $eventRule = $event['eventRule'];
                    //  当前只考虑 单件商品满X件赠送B商品Y件
                    if (
                        !empty($event['eventToGoods']) &&
                        $eventRule['match_effect'] == EventRule::MATCH_EFFECT_ONE &&
                        $eventRule['match_type'] == EventRule::MATCH_TYPE_GOODS_NUM
                    ) {
                        //  计算赠品库存是否充足，最大支持多少
                        foreach ($event['eventToGoods'] as $goodsId) {

                            //  计算参与活动的商品的最大可购买数量(最大满赠数+最小满赠数-1)
                            if ($goodsId == $eventRule['gift_id']) {
                                $goodsMaxNum = ceil($goodsInfoMap[$goodsId]['goods_number'] / ($eventRule['match_value'] +
                                        $eventRule['gift_num']) * $eventRule['match_value']);
                            } else {
                                $gift_support_max_num = ceil($goodsInfoMap[$eventRule['gift_id']]['goods_number'] / $eventRule['gift_num']) * $eventRule['match_value'];
                                $goodsMaxNum = min($gift_support_max_num, $goodsInfoMap[$goodsId]['goods_number']);
                            }

                            //  如果要购买的商品没有超出 赠品支持的范围，则商品的满赠活动正常进行
                            if ($goodsMaxNum < $goodsIdNumMap[$goodsId]) {
                                $goodsMaxNum = $goodsIdNumMap[$goodsId];

                                $msg = '商品 '.$goodsInfoMap[$goodsId]['goods_name'].
                                        '库存不能满足满赠活动：'.$event['event_name'].', 请修改够购买数量';
                                Yii::warning($msg);
                                //  throw new BadRequestHttpException($msg, 12);    前端处理异常，提示错误

                            }
                            if ($goodsMaxNum >= $goodsIdNumMap[$goodsId]) {
                                $giftsGoodsNum = floor($goodsIdNumMap[$goodsId] / $eventRule['match_value']) * $eventRule['gift_num'];
                                if ($giftsGoodsNum) {
                                    $gift = self::formatGifts(
                                        $event,
                                        $eventRule,
                                        $giftsGoodsNum,
                                        $goodsInfoMap[$eventRule['gift_id']],
                                        $goodsMaxNum
                                    );
                                    $gift['parent_id'] = $goodsId;
                                } else {
                                    $gift = [];
                                }

                                if (!empty($gift)) {
//                                    $event['gift'][] = $gift;
                                    $gifts[] = $gift;
                                }
                            }
                        }
                    }
                }

            }
            $rs['gifts'] = $gifts;


            //  如果有满减活动，计算库存是否充足，格式化 优惠信息
            if (!empty($rs['fullCut'])) {
                //  即：如果一个SKU参与多个满减专题(每个专题可以有多条规则，这个是支持的)，则每个专题都按商品购买总金额计算满减
                //  只考虑一个商品只参与一个满减活动的场景，不考虑一个商品同时参加多个活动，
                $event = [];
                if (!empty($rs['fullCut'])) {
                    $event = current($rs['fullCut']);
                }

                //  计算参与满减活动的商品总价格
                $sumPrice = 0.00;
                $goodsAmount = 0.00;
                foreach ($priceMap as $goods_id => $price) {
                    //  生效的满减活动有生效的活动规则并且 当前有参与活动的商品 才计算满减
                    if (
                        !empty($event) &&
                        !empty($event['eventRule']) &&
                        !empty($event['eventToGoods']) &&
                        !empty($event['eventToGoodsSelected']) &&
                        in_array($goods_id, $event['eventToGoodsSelected'])
                    ) {
                        $sumPrice += bcmul($goodsIdNumMap[$goods_id], $price, 4);
                    }

                    if (in_array($goods_id, $selectedGoodsList)) {
                        $goodsAmount += bcmul($goodsIdNumMap[$goods_id], $price, 4);
                    }
                }

                $event['sumPrice'] = $sumPrice;
                $event['goodsAmount'] = NumberHelper::price_format($goodsAmount);
                $rs['fullCut'] = [$event];
            }

            //  如果有优惠券活动，计算库存是否充足，格式化 优惠信息
            if (!empty($couponEventList) && $userId) {
                //  1个SKU参与多个优惠券活动(每个活动可以有多张优惠券)，则每个专题都按商品购买总金额计算，每次下单只能会用一张优惠券
                //  用户持有的优惠券遍历计算每张券是否可用，并推荐优惠幅度最高的优惠券
                $couponList = CouponRecord::find()
                    ->joinWith('fullCutRule')
                    ->joinWith('event')
                    ->where([
                        'user_id' => $userId,
                        CouponRecord::tableName().'.status' => CouponRecord::COUPON_STATUS_UNUSED,
                    ])->asArray()
                    ->all();
                Yii::warning('用户名下未使用的优惠券列表, $couponList = '.json_encode($couponList));

                $bestCouponId = ''; //  优惠幅度最大的优惠券ID
                $bestCut = 0;
                foreach ($couponList as $coupon) {
                    $coupon['useable'] = 0;
                    $coupon['cut'] = 0;
                    $coupon['eventToGoods'] = [];
                    $coupon['eventToGoodsSelected'] = [];
                    $coupon['fullCutRule']['cutFormat'] = (int)$coupon['fullCutRule']['cut'];

                    $_eventId = $coupon['event_id'];
                    if (!empty($couponEventList[$_eventId]) && !empty($couponEventList[$_eventId]['eventToGoodsSelected'])) {

                        //  计算参与优惠券活动的商品总价格
                        $sumPrice = 0.00;
                        foreach ($priceMap as $goods_id => $price) {
                            //  生效的满减活动有生效的活动规则并且 当前有参与活动的商品 才计算满减
                            if (in_array($goods_id, $couponEventList[$_eventId]['eventToGoodsSelected'])) {
                                $sumPrice += bcmul($goodsIdNumMap[$goods_id], $price, 4);
                            }
                        }

                        $coupon['sumPrice'] = $sumPrice;
                        $coupon['eventToGoods'] = array_values($couponEventList[$_eventId]['eventToGoods']);
                        $coupon['eventToGoodsSelected'] = array_values($couponEventList[$_eventId]['eventToGoodsSelected']);

                        if ($sumPrice - $coupon['fullCutRule']['above'] >= 0) {
                            $coupon['useable'] = 1;
                            $coupon['cut'] = $coupon['fullCutRule']['cutFormat'];
                            if ((int)$coupon['cut'] > (int)$bestCut) {
                                $bestCut = $coupon['cut'];
                                $bestCouponId = $coupon['coupon_id'];
                            }
                        }
                    }

                    $rs['coupon'][] = $coupon;
                }

                Yii::warning('优惠幅度最大的优惠券ID = '.$bestCouponId);
                //  如果有符合使用条件的优惠券，个出标记
                if (!empty($bestCouponId)) {
                    foreach ($rs['coupon'] as &$couponItem) {
                        if ($couponItem['coupon_id'] == $bestCouponId) {
                            $couponItem['bestCoupon'] = true;
                        } else {
                            $couponItem['bestCoupon'] = false;
                        }
                    }
                }

                //  按优惠幅度排序
                usort($rs['coupon'], function ($a, $b){
                    if ($a['cut'] == $b['cut']) {
                        return 0;
                    } else {
                        return $a['cut'] > $b['cut'] ? -1 : 1;
                    }
                });
            }
        }

        Yii::warning('满赠、满减、优惠券活动信息汇总, $rs = '.json_encode($rs));
        return [
            'gifts'     => $rs['gifts'],
            'fullCut'   => $rs['fullCut'],
            'coupon'    => $rs['coupon'],
        ];
    }

    /**
     * 格式化活动信息
     * @param $item eventModel
     * @return mixed
     */
    public static function formatEvent($item, $goodsList, $selectedGoodsList)
    {
        $event = [
            'event_id'      => (int)$item->event_id,
            'event_type'    => (int)$item->event_type,
            'rule_id'       => (int)$item->rule_id,
            'start_time'    => (int)DateTimeHelper::getFormatGMTTimesTimestamp($item->start_time),
            'start_time_str'=> (string)$item->start_time,
            'end_time'      => (int)DateTimeHelper::getFormatGMTTimesTimestamp($item->end_time),
            'end_time_str'  => (string)$item->end_time,
            'event_name'    => $item->event_name,
            'event_desc'    => $item->event_desc,
            'banner'        => ImageHelper::get_image_path($item->banner),
            'url'           => $item->url,
            'bgcolor'       => $item->bgcolor,
        ];

        if (!empty($item->eventToGoods)) {
            $event['global'] = false;
            foreach ($item->eventToGoods as $goods) {
                //  计算参与活动的商品 只考虑 当前结算范围内的商品
                if (in_array($goods->goods_id, $goodsList)) {
                    $event['eventToGoods'][] = $goods->goods_id;
                }
                if (in_array($goods->goods_id, $selectedGoodsList)) {
                    $event['eventToGoodsSelected'][] = $goods->goods_id;
                }
            }
        }
        //  暂时不支持全局的优惠券和满减——某些商品利润特别低，不适合参与，当前没有设置不参与的规则
//        elseif ($item->event_type == Event::EVENT_TYPE_FULL_CUT || $item->event_type = Event::EVENT_TYPE_COUPON) {
//            //  如果活动没有配置商品，相当于所有商品都参与活动
//            $event['global'] = true;
//            $event['eventToGoods'] = $goodsList;
//            $event['eventToGoodsSelected'] = $selectedGoodsList;
//        }

        if ($item->event_type == Event::EVENT_TYPE_FULL_CUT || $item->event_type == Event::EVENT_TYPE_COUPON) {
            $event['eventRule'] = $item->fullCutRule;
        }

        return $event;
    }

    /**
     * 格式化赠品信息  ——  把活动规则的信息也填写到gifts中，追加到商品信息的 $goods[gift]中
     * @param string    $event          满赠活动
     * @param array     $eventRule      满赠活动规则
     * @param int       $giftsGoodsNum  赠品数量
     * @param string    $goodsInfo      赠品信息
     * @param int       $goodsMaxNum    商品的最大可购买数量，用于更新页面的  max-numb
     * @return array
     */
    public static function formatGifts($event, $eventRule, $giftsGoodsNum, $goodsInfo, $goodsMaxNum) {
        $gift_show_peice    = NumberHelper::price_format($eventRule['gift_show_peice']);
        $gift_need_pay      = NumberHelper::price_format($eventRule['gift_need_pay']);
        return [
//            'event_id'          => (int)$event['event_id'],   //  is_gift 标记了是赠品，parent_id 标记了来源，event_id 暂时没有应用场景
            'event_name'        => $event['event_name'],
            'event_desc'        => $event['event_desc'],

            'goods_id'          => (int)$eventRule['gift_id'],
            'goods_num'         => (int)$giftsGoodsNum,
            'goods_number'      => (int)$giftsGoodsNum,
            'goods_max_num'     => (int)$goodsMaxNum,
            'gift_show_peice'   => $gift_show_peice,    //  可全局替换为 market_price ? 要兼容IOS
            'gift_need_pay'     => $gift_need_pay,      //  可全局替换为 goods_price  ? 要兼容IOS
            'goods_price'       => $gift_need_pay,
            'pay_price'         => $gift_need_pay,
            'goods_name'        => (string)$goodsInfo['goods_name'],
            'goods_thumb'       => ImageHelper::get_image_path($goodsInfo['goods_thumb']),

            'is_real'           => (int)$goodsInfo['is_real'],
            'is_gift'           => OrderGoods::IS_GIFT_GIFT,
        ];
    }

    /**
     * 设置要输出的 赠品数据
     *
     * @param $event    满赠活动
     * @param $gifts_goods_num  赠品数量
     * @param int $is_real      是否为真实商品
     * @return array
     */
    public static function setGifts($event, $gifts_goods_num, $is_real = 1)
    {
        return [
            'event_id'          => (int)$event['event_id'],
            'event_name'        => (string)$event['event_name'],
            'event_desc'        => (string)$event['event_desc'],

            'goods_id'          => (int)$event['gift_id'],
            'goods_num'         => (int)$gifts_goods_num,
            'goods_number'      => (int)$gifts_goods_num,
            'goods_max_num'     => (int)$event['goods_max_num'],
            'gift_show_peice'   => NumberHelper::price_format($event['gift_show_peice']),
            'gift_need_pay'     => NumberHelper::price_format($event['gift_need_pay']),
            'goods_name'        => (string)$event['goods_name'],
            'goods_thumb'       => ImageHelper::get_image_path($event['goods_thumb']),

            'is_real'           => (int)$is_real,   //  需要通过数据库获取
            'is_gift'           => OrderGoods::IS_GIFT_GIFT,
        ];
    }

    public static function getValidCouponEventMap() {
        $time = date('Y-m-d H:i:s', time());

        //  获取当前有效的活动
        $eventList = Event::find()
            ->joinWith('fullCutRule fullCutRule')
            ->where(['is_active' => Event::IS_ACTIVE])
            ->andWhere(['<=', 'start_time', $time])
            ->andWhere(['>=', 'end_time', $time])
            ->andWhere([
                'fullCutRule.status' => FullCutRule::STATUS_VALID,
            ])
            ->all();

        $result = [];
        foreach ($eventList as $event) {
            $result[$event['event_id']] = '('. $event['event_id']. ')'. $event['event_name'];
        }
        return $result;
    }

    public static function getValidCouponRuleMap() {
        $time = date('Y-m-d H:i:s', time());

        //  获取当前有效的活动
        $eventList = Event::find()
            ->joinWith('fullCutRule')
            ->where(['is_active' => Event::IS_ACTIVE])
            ->andWhere(['<=', 'start_time', $time])
            ->andWhere(['>=', 'end_time', $time])
            ->all();

        $result = [];
        foreach ($eventList as $event) {
            foreach ($event['fullCutRule'] as $rule) {
                if ($rule['status'] == FullCutRule::STATUS_VALID) {
                    $result[$rule['rule_id']] = '('. $event['event_id']. ')('. $rule['rule_id']. ')'. $rule['rule_name'];
                }
            }
        }
        return $result;
    }
}
