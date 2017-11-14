<?php
/**
 * Created by PhpStorm.
 * User: clark
 * Date: 2017/1/6
 * Time: 11:55
 */

namespace common\helper;

use common\models\Cart;
use common\models\Event;
use common\models\Goods;
use yii\helpers\ArrayHelper;

class CartHelper
{
    /**
     * 获取购物车的 商品和数量
     * @param $userId
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getSelectedMap($userId)
    {
        $rs = [];
        if (!empty($userId)) {
            $cartList = Cart::find()
                ->select(['goods_id', 'goods_number', 'selected', 'goods_price'])
                ->where(['user_id' => $userId])
                ->asArray()
                ->all();

            if ($cartList) {
                foreach ($cartList as $goods) {
                    $rs[] = [
                        'goods_id'      => $goods['goods_id'],
                        'goods_number'  => $goods['goods_number'],
                        'selected'      => $goods['selected'],
                        'goods_price'   => $goods['goods_price'],
                    ];
                }
            }
        }

        return $rs;
    }

    /**
     * 修正购物车中的商品的购买数量
     *
     * @param $recId    购物车记录ID o_cart.rec_id
     * @param $buyNum   新的购买数量  o_cart.goods_number 未入库
     * @param $userRank 用户等级
     * @return int
     */
    public static function checkBuyNum($recId, $buyNum, $userRank)
    {
        //  【1】 获取基础信息
        $cartGoods = Cart::find()
            ->joinWith('goods')
            ->joinWith('event')
            ->where(['rec_id' => $recId])
            ->one();
        $goods = $cartGoods->goods;
        $eventRule = [];

        //  初始化返回数据
        $data = [];
        $code = 0;
        $msg = '';

        //  【2】判定商品购买数量是否符合实际——是否小于起售数量，是否超出库存，是否有参与活动，是否按箱购买
        //  [2.1]是否在[起售数量，库存之间]
        if ($buyNum > $goods->goods_number) {
            $buyNum = $goods->goods_number;
        } elseif ($buyNum < $goods->start_num) {
            $buyNum = $goods->start_num;
        }

        //  [2.2]是否有参与活动，如满赠，需要修正最大可购买数量
        $goodsMaxNum = $goods->goods_number;    //  如果不考虑活动，商品的最大库存就是实际库存
       
        if (!empty($cartGoods->event)) {
            foreach ($cartGoods->event as $event) {
                //  满赠，需要修正最大可购买数量
                if ($event['event_type'] == Event::EVENT_TYPE_FULL_GIFT && $eventRule->gift_num) {
                    $eventRule = $event->fullGiftRule;

                    if ($goods->goods_id == $eventRule->gift_id) {
                        $goodsMatchTimesMax = floor($goods->goods_number / ($eventRule->match_value + $eventRule->gift_num));
                        $matchTimesMax = $goodsMatchTimesMax;
                    } else {
                        $gift = $eventRule->gift;
                        $goodsMatchTimesMax = floor($goods->goods_number / $eventRule->match_value);
                        $giftsMatchTimesMax = floor($gift->goods_number / $eventRule->gift_num);
                        $matchTimesMax = min($giftsMatchTimesMax, $goodsMatchTimesMax);
                    }
return $matchTimesMax;
                    //  商品的最大可购买数量 = 刚好符合最大满赠数的商品数量 + 符合一次满赠规则的数量 - 1
                    $goodsMaxNum = $eventRule->match_value * ($matchTimesMax + 1) - 1;
                    if ($goodsMaxNum > $goods->goods_number) {
                        $goodsMaxNum = $goods->goods_number;
                    }
                }
            }
        }

        //  [2.3]如果是按箱购买 且 有有效的装箱数，修正最大可购买数量、用户购买数量 为整箱的商品数量
        if ($goods->buy_by_box && $goods->number_per_box) {
            $goodsMaxNum = floor($goodsMaxNum / $goods->number_per_box) * $goods->number_per_box;

            //  按箱购买的商品 用户购买数量不是整箱时， 四舍五入到整箱，得到的结果如果超出了【最大可购买数量】，则
            $buyNum = round($buyNum / $goods->number_per_box) * $goods->number_per_box;
        }

        //  [2.4]
        if ($buyNum > $goodsMaxNum) {
            $code = 1;
            $msg = '超出商品的最大可购买数量'.$goodsMaxNum;
            $buyNum = $goodsMaxNum;
        }

        //  【3】修正商品价格，购买数量、价格入库
        $goodsPrice = GoodsHelper::getFinalPrice($goods, $buyNum, $userRank);

        //  【4】组织返回数据
        $data['goods_id'] = $goods->goods_id;
        $data['goods_number'] = $buyNum;
        $data['goods_price'] = $goodsPrice;
        if (!empty($eventRule)) {
            $data['gift_num'] = floor($buyNum / $eventRule->match_value) * $eventRule->gift_num;
        }

        return $response = [
            'code'  => $code,
            'msg'   => $msg,
            'data'  => $data
        ];

    }
}