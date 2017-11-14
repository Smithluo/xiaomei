<?php
/**
 * Created by PhpStorm.
 * User: clark
 * Date: 2017/3/6
 * Time: 18:18
 */

namespace common\helper;

use common\models\Event;
use common\models\FullCutRule;
use common\models\CouponRecord;

class CouponHelper
{
    /**
     * 获取优惠券活动列表
     * @return array
     */
    public static function getCouponEventMap()
    {
        $return = [];
        $rs = Event::find()
            ->select(['event_id', 'event_name', 'is_active'])
            ->where(['event_type' => Event::EVENT_TYPE_COUPON])
            ->orderBy(['event_id' => SORT_DESC])
            ->all();

        if ($rs) {
            $is_active_map = Event::$is_active_map;
            foreach ($rs as $item) {
                $return[$item->event_id] = '['.$is_active_map[$item->is_active].']'.$item->event_name;
            }
        }
        return $return;
    }

    /**
     * 获取优惠券活动列表
     * @return array
     */
    public static function getCouponEventRuleMap()
    {
        $return = [];
        $rs = FullCutRule::find()
            ->joinWith('event')
            ->select([FullCutRule::tableName().'.rule_id', 'rule_name'])
            ->where([Event::tableName().'.event_type' => Event::EVENT_TYPE_COUPON])
            ->orderBy([FullCutRule::tableName().'.rule_id' => SORT_DESC])
            ->all();

        if ($rs) {
            foreach ($rs as $item) {
                $return[$item->rule_id] = '['.$item->rule_id.']'.$item->rule_name;
            }
        }
        return $return;
    }

    /**
     * 获取 指定用户和状态的 优惠券列表
     * @param $userId
     * @param $status
     * @return mixed
     */
    public static function getCouponList($userId, $status)
    {
        $couponList = [];

        CouponRecord::setExpiredCoupon($userId);

        if (!empty($userId)) {
            $couponList = CouponRecord::find()
                ->joinWith('event')
                ->joinWith('fullCutRule')
                ->where([
                    'user_id' => $userId,
                    CouponRecord::tableName().'.status' => $status,
                ])->orderBy([
                    Event::tableName().'.end_time' => SORT_DESC,
                    FullCutRule::tableName().'.cut' => SORT_DESC,
                ])->asArray()
                ->all();

            foreach ($couponList as $k => $coupon) {
                if (isset($coupon['coupon_id'])) {
                    $couponList[$k]['coupon_id'] = intval($coupon['coupon_id']);
                }

                if (isset($coupon['status'])) {
                    $couponList[$k]['status'] = intval($coupon['status']);
                }

                if (isset($coupon['fullCutRule']['cut'])) {
                    $couponList[$k]['fullCutRule']['cut'] = NumberHelper::price_format($coupon['fullCutRule']['cut']);
                }

                if (isset($coupon['fullCutRule']['above'])) {
                    $couponList[$k]['fullCutRule']['above'] = NumberHelper::price_format($coupon['fullCutRule']['above']);
                }
            }
        }

        return $couponList;
    }



    /**
     * 格式化优惠券列表
     *
     * @param $couponList
     * @return array
     */
    public static function getCouponListFormat($couponList)
    {
        $couponListFormat = [];
        if ($couponList) {
            $now = date('Y-m-d H:i:s', time());
            foreach ($couponList as $coupon) {
                $coupon['start_date'] = substr($coupon['start_time'], 0, 10);
                $coupon['end_date'] = substr($coupon['end_time'], 0, 10);
                $coupon['event']['bgcolor'] = $coupon['event']['bgcolor'] ?: '#fca2bb';
                $coupon['fullCutRule']['cutFormat'] = (int)$coupon['fullCutRule']['cut'];

                if (
                    ($now > $coupon['start_time'])
                    && ($now < $coupon['end_time'])
                    && $coupon['event']['is_active']
                ) {
                    $coupon['canUseNow'] = true;
                } else {
                    $coupon['canUseNow'] = false;
                }

                $couponListFormat[] = $coupon;
            }
        }

        return $couponListFormat;
    }

    /**
     * 获取优惠券的分类统计数量
     *
     * @param $userId
     * @return array
     */
    public static function getCouponCountMap($userId)
    {
        $couponCountMap = [
            'unusedNum'     => 0,
            'usedNum'       => 0,
            'expiredNum'    => 0,
        ];

        $countMap = CouponRecord::find()
            ->select([CouponRecord::tableName().'.status', 'COUNT(*) AS cnt'])
            ->where([
                'user_id' => $userId,
            ])->groupBy(CouponRecord::tableName().'.status')
            ->asArray()
            ->all();
        if ($countMap) {
            foreach ($countMap as $item) {
                switch ($item['status']) {
                    case 0:
                        $couponCountMap['unusedNum'] = $item['cnt'];
                        break;
                    case 1:
                        $couponCountMap['usedNum'] = $item['cnt'];
                        break;
                    case 2:
                        $couponCountMap['expiredNum'] = $item['cnt'];
                        break;
                    default:
                        break;
                }
            }
        }

        return $couponCountMap;
    }
}