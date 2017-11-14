<?php
/**
 * Created by PhpStorm.
 * User: clark
 * Date: 2016/12/29
 * Time: 11:27
 */

namespace backend\models;

use backend\models\CouponRecordIssueForm;

class FullCutRule extends \common\models\FullCutRule
{
    /**
     * 获取满赠活动的规则列表
     * @return array
     */
    public static function getRuleMap()
    {
        $rs = self::find()
            ->select(['rule_name', 'rule_id'])
            ->asArray()
            ->all();

        return array_column($rs, 'rule_name', 'rule_id');
    }

    /**
     * 获取满赠活动的规则列表
     * @return array
     */
    public static function getRuleMapByEventId($eventId)
    {
        $rs = [];

        $map = self::find()
            ->select(['rule_name', 'rule_id', 'status', 'above'])
            ->where(['event_id' => $eventId])
            ->asArray()
            ->all();

        usort($map, function ($a, $b){
            if ($a['above'] == $b['above']) {
                return 0;
            } else {
                return (float)$a['above'] > (float)$b['above'] ? 1 : -1;
            }
        });

        if ($map) {
            foreach ($map as $rule) {
                $status = $rule['status'] == Event::IS_ACTIVE ? '[ -生效- ]' : '[未生效]';
                $rs[$rule['rule_id']] = $status.$rule['rule_name'];
            }

        }

        return $rs;
    }

    /**
     * 获取优惠券活动规则对应的优惠券信息
     * @param $eventId
     * @param $ruleId
     * @param $eventName
     * @return array
     */
    public static function getCouponInfo($eventId, $ruleId, $eventName)
    {
        $couponRecordIssueForm = new CouponRecordIssueForm();
        $couponRecordIssueForm->event_id = $eventId;
        $couponRecordIssueForm->rule_id = $ruleId;

        $coupon['event_name'] = '['.$eventId.']'.$eventName;
        $coupon['circulation'] = CouponRecordIssueForm::getCirculation($eventId, $ruleId);
        $coupon['bindCount'] = CouponRecordIssueForm::getBindCouponCount($eventId, $ruleId);
        $coupon['usedCount'] = CouponRecordIssueForm::getUsedCouponCount($eventId, $ruleId);

        return [
            'couponRecordIssueForm' => $couponRecordIssueForm,
            'coupon' => $coupon,
        ];
    }
}