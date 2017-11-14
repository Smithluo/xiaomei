<?php
/**
 * Created by PhpStorm.
 * User: clark
 * Date: 2017/3/23
 * Time: 15:52
 */

namespace common\helper;

use \Yii;
use common\models\Goods;
use common\models\GoodsActivity;
use common\models\OrderInfo;
use yii\helpers\ArrayHelper;

class GoodsActivityHelper
{
    /**
     * 获取团采活动列表，按后台设置的sort_order逆序排序
     * @param $num  获取数量
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getGroupBuyList($num)
    {
        $list = [];
        $now = DateTimeHelper::getFormatGMTTimesTimestamp();
        $list = GoodsActivity::find()
            ->where(['act_type' => GoodsActivity::ACT_TYPE_GROUP_BUYT])
            ->andWhere(['<', 'is_finished', GoodsActivity::STATUS_SETTLED])
            ->andWhere(['>', 'end_time', $now])
            ->orderBy([
                'sort_order' => SORT_DESC])
            ->limit($num)
            ->all();

        return $list;
    }

    /**
     * 获取秒杀活动列表，按后台设置的sort_order逆序排序
     *
     * 秒杀活动 设置独立库存限制 match_num，支付数量满足则 显示售罄
     *
     * @param int $num  获取数量
     * @param string $platform  访问来源 ['pc', 'm', 'ios', 'android', 'wap']
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getFlashSaleGoodsList($num, $platform)
    {
        Yii::warning(' 入参 $num = '.json_encode($num).', $platform = '.json_encode($platform), __METHOD__);
        $flashGoodsList = [];
        $now = DateTimeHelper::getFormatGMTTimesTimestamp();
        $gaTb = GoodsActivity::tableName();

        $list = GoodsActivity::find()
            ->joinWith('goods')
            ->where([
                'and',
                ['act_type' => GoodsActivity::ACT_TYPE_FLASH_SALE],
                ['<', 'is_finished', GoodsActivity::STATUS_SETTLED],
                ['>', 'end_time', $now]
            ])->orderBy([
                $gaTb.'.sort_order' => SORT_DESC,
                'start_time' => SORT_ASC,
                'end_time' => SORT_ASC,
            ])->limit($num)
            ->all();
        Yii::warning(__LINE__.' $list = '.json_encode($list), __METHOD__);

        if (!empty($list)) {
            foreach ($list as $flashGoodsModel) {
                $flashGoods = ArrayHelper::toArray($flashGoodsModel);
                $flashGoods['goods'] = ArrayHelper::toArray($flashGoodsModel->goods);
                $flashGoods['orderInfo'] = ArrayHelper::toArray($flashGoodsModel->orderInfo);
                $flashGoods['orderGoods'] = ArrayHelper::toArray($flashGoodsModel->orderGoods);
                $startDateTime = DateTimeHelper::getFormatCNDateTime($flashGoods['start_time']);
                $flashGoods['startTimeFormat'] = substr($startDateTime, 5, 11); //  只取时间的 mm-dd hh-ii

                //  修正秒杀的banner图
                if (!empty($flashGoods['show_banner'])) {
                    $flashGoods['showBannerFormat'] = ImageHelper::getGoodsActivityImgPath($flashGoods['show_banner']);
                } else {
                    $flashGoods['showBannerFormat'] = ImageHelper::get_image_path($flashGoods['goods']['goods_thumb']);
                }

                //  计算活动的销量
                $countRs = self::getCount($flashGoods['orderInfo'], $flashGoods['orderGoods']);
                $flashGoods['sales_count'] = $countRs['saleCount'];
                $flashGoods['payCount'] = $countRs['payCount'];

                //  修正库存——活动上的最大可购买数量，
                if ($flashGoods['match_num'] > 0) {
//                    $flashGoods['canSaleMaxNum'] = $flashGoods['match_num'] - $flashGoods['payCount'];
                    $flashGoods['canSaleMaxNum'] = $flashGoods['match_num'];
                    $flashGoods['stock'] = min($flashGoods['goods']['goods_number'], $flashGoods['canSaleMaxNum']);

                    if ($flashGoods['stock'] < 0) {
                        $flashGoods['stock'] = 0;
                    }
                } elseif ($flashGoods['match_num'] == 0) {
                    $flashGoods['stock'] = 0;
                } else {
                    $flashGoods['stock'] = $flashGoods['goods']['goods_number'];
                }

//                $flashGoods['limitNumPersonal'] = 10;

                /**
                 * 判定活动状态
                 * a)即将开始  ——当前时间【不在】活动时段内
                 * b)进行中    ——当前时间【在】活动时段内 && 库存 > 0
                 * c)已售罄    ——当前时间【在】活动时段内 && 库存 <= 0
                 */
                if ($flashGoods['start_time'] > $now) {
                    $flashGoods['status'] = 'startEve';  //  即将开始
                    $flashGoods['statusNum'] = 3;
                }
                else {
                    if ($flashGoods['stock'] > 0) {
                        $flashGoods['status'] = 'onGoing';  //  进行中
                        $flashGoods['statusNum'] = 2;
                    }else {
                        $flashGoods['status'] = 'sellOut';  //  已售罄
                        $flashGoods['statusNum'] = 1;
                    }
                }

                //  拼链接
                switch ($platform) {
                    case 'm':
                        $flashGoods['url'] = '/default/groupbuy/info/id/'.$flashGoods['act_id'].'.html';
                        break;
                    case 'pc':
                        $flashGoods['url'] = '/group_buy.php?id='.$flashGoods['act_id'];
                        break;

                    default:
                        $flashGoods['url'] = '#';
                        break;
                }

                $flashGoodsList[] = $flashGoods;
            }
        }

        Yii::warning(__LINE__.' 返回 $flashGoodsList = '.json_encode($flashGoodsList), __METHOD__);
        return $flashGoodsList;
    }

    /**
     * 获取 团采/秒杀 活动的销量、付款数量
     *
     * 团采的销量下单就算，秒杀的销量付款才算
     * @param $orderInfoList    array
     * @param $orderGoodsList   array
     * @param int $userId       int
     * @return array
     */
    public static function getCount($orderInfoList, $orderGoodsList, $userId = 0)
    {
        Yii::warning(' 入参 $orderInfoList = '.json_encode($orderInfoList).
            ', $orderGoodsList = '.json_encode($orderGoodsList).' $userId = '.$userId, __METHOD__);
        $payCount = 0;
        $saleCount = 0;
        $userPayCount = 0;

        if (!empty($orderGoodsList)) {
            $validOrderIdList = [];
            $userOrderIdList = [];
            //  秒杀的销量，付款才算
            foreach ($orderInfoList as $orderInfo) {
                //  以付款订单作为销量计算的基础，不考虑退换
                if ($orderInfo['pay_status'] == OrderInfo::PAY_STATUS_PAYED) {
                    $validOrderIdList[] = $orderInfo['order_id'];

                    if (!empty($userId) && $orderInfo['user_id'] == $userId) {
                        $userOrderIdList[] = $orderInfo['order_id'];
                    }
                }
            }

            Yii::warning(__LINE__.' $validOrderIdList = '.json_encode($validOrderIdList).
                ', $userOrderIdList = '.json_encode($userOrderIdList),
                __METHOD__);

            if (!empty($validOrderIdList)) {
                foreach ($orderGoodsList as $orderGoods) {
                    if (in_array($orderGoods['order_id'], $validOrderIdList)) {
                        $payCount += $orderGoods['goods_number'];

                        if (!empty($userOrderIdList) && in_array($orderGoods['order_id'], $userOrderIdList)) {
                            $userPayCount += $orderGoods['goods_number'];
                        }
                    }
                }
            }

            $saleMap = array_column($orderGoodsList, 'goods_number');
            $saleCount = array_sum($saleMap);
        }

        Yii::warning(__LINE__.' $saleMap = '.json_encode($saleMap).', $saleCount = '.$saleCount.
            ', $payCount = '.$payCount.', $userPayCount = '.$userPayCount, __METHOD__);
        return [
            'payCount'      => $payCount,
            'saleCount'     => $saleCount,
            'userPayCount'  => $userPayCount,
        ];
    }

    /**
     * 获取立即购买商品的 当前有效活动的 类型
     *
     * 没有对应活动则认为是普通商品，返回0
     * @param $goodsId      int
     * @return int|mixed    int
     */
    public static function getActType($goodsId)
    {
        $now = DateTimeHelper::getFormatGMTTimesTimestamp();
        $rs = GoodsActivity::find()
            ->select(['act_type'])
            ->where([
                'and',
                ['goods_id' => $goodsId],
                ['>', 'end_time', $now],
                ['<', 'start_time', $now],
            ])->one();

        if (!empty($rs) && !empty($rs->act_type)) {
            $actType = $rs->act_type;
        } else {
            $actType = 0;   //  默认普通商品
        }

        return $actType;
    }
}