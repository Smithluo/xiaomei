<?php
/**
 * Created by PhpStorm.
 * User: HongXunPan
 * Date: 2017/11/2
 * Time: 11:56
 */

namespace api\modules\v1\controllers;

use common\models\CouponRecord;
use \Yii;
use api\modules\v1\models\Event;
use common\helper\DateTimeHelper;
use common\models\FullCutRule;
use yii\helpers\ArrayHelper;
use common\helper\CouponHelper;


class CouponController extends BaseAuthActiveController
{
    public $modelClass = 'api\modules\v1\models\Event';

    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];

    public function actionCoupon_center() {

        $now = date('Y-m-d H:i:s', time());
        $userId = Yii::$app->user->getId();
        $couponEvent = Event::find()
            ->joinWith([
                'fullCutRule',
            ])->where([
                'is_active' => Event::IS_ACTIVE,
                'receive_type' => Event::RECEIVE_TYPE_DRAW,
            ])->andWhere(['<', Event::tableName().'.start_time', $now])
            ->andWhere(['>', Event::tableName().'.end_time', $now])
            ->all();

        //  当前有效的优惠券活动
        $eventIdList = ArrayHelper::getColumn($couponEvent, 'event_id');

        //  已有(未使用未过期的)优惠券
        CouponRecord::setExpiredCoupon($userId);

        //  获取用户已有的优惠券
        $couponRecord = CouponRecord::find()
            ->where([
                'user_id' => $userId,
                'event_id' => $eventIdList,
            ])->all();
        $couponRecordCount = count($couponRecord);
        $data['coupon_record_count'] = $couponRecordCount;
        $couponMap = [];
        $couponRuleCanUseMap = [];
        if ($couponRecordCount > 0) {
            foreach ($couponRecord as $coupon) {
                $couponMap[$coupon->event_id][] = $coupon->rule_id;

                // 已有可用的优惠券
                if ($coupon->used_at == 0 && $coupon->status == CouponRecord::COUPON_STATUS_UNUSED) {
                    $couponRuleCanUseMap[] = $coupon->rule_id;
                }
            }
        }


        $couponList = [];
        $totalCouponList = [];
        if (!empty($couponEvent)) {
            foreach ($couponEvent as $event) {
                if (!empty($event->fullCutRule)) {
                    $eventItem = [
                        'bgcolor' => $event->bgcolor,
                        'sub_type' => $event->sub_type,
                        'event_id' => $event->event_id,
                        'event_name' => $event->event_name,
                        'event_desc' => $event->event_desc,
                    ];

                    foreach ($event->fullCutRule as $rule) {
                        //  如果当前优惠券规则生效
                        if ($rule->status = FullCutRule::STATUS_VALID) {
                            $ruleItem = $eventItem;
                            $ruleItem['rule_id'] = $rule->rule_id;
                            $ruleItem['rule_name'] = $rule->rule_name;
                            $ruleItem['cut'] = intval($rule->cut);
                            if ($rule->term_of_validity > 0) {
                                $ruleItem['valid_period'] = '自领券时间起'.DateTimeHelper::getTimePeriod($rule->term_of_validity).'以内';
                            } else {

                                $endTime = substr($event->end_time, 0, 10);
//                                $endTime = str_replace('-', '/', $endTime);

                                $ruleItem['valid_period'] = $endTime;
                            }

                            // 未领取
                            if (!isset($couponMap[$rule->event_id]) || !in_array($rule->rule_id, $couponMap[$rule->event_id]))  {
                                // 未领取 并且有券可领取
                                if (!empty($rule->couponCanTake) && !empty($rule->couponCanTake->coupon_id)) {
                                    $ruleItem['notHave'] = true;
                                    $ruleItem['coupon_id'] = $rule->couponCanTake->coupon_id;
                                    $couponList['notHave'][] = $ruleItem;   //  未领取的券集合
                                } else {
                                    continue;
                                }
                            }
                            //  已领取
                            else {
                                $map = array_count_values($couponMap[$rule->event_id]);

                                //  有限次数的优惠券
                                if ($event->times_limit >= 1) {
                                    //  已领满  已领券数量 >= 可参与次数的活动不显示
                                    if (isset($map[$rule->rule_id]) && $event->times_limit <= $map[$rule->rule_id]) {
                                        if (in_array($rule->rule_id, $couponRuleCanUseMap)) {
                                            //  有可用的优惠券，则显示
                                            $ruleItem['notHave'] = false;
                                        } else {
                                            //  没有可用的优惠券，则不显示
                                            continue;
                                        }
                                    }
                                    //  可领多张券 并且领了还未领满
                                    else {
                                        //  还有可领去的优惠券
                                        if (!empty($rule->couponCanTake) && !empty($rule->couponCanTake->coupon_id)) {
                                            $ruleItem['notHave'] = true;
                                            $ruleItem['coupon_id'] = $rule->couponCanTake->coupon_id;
                                            $couponList['notHave'][] = $ruleItem;   //  未领取的券集合
                                        }
                                        //  已发行的优惠券领光了
                                        else {
                                            continue;
                                        }
                                    }
                                }
                                //  无限次数的优惠券
                                else {
                                    $ruleItem['notHave'] = false;
                                }
                            }

                            $totalCouponList[] = $ruleItem;
                            if (!empty($event->effective_scope_type)) {
                                $couponList[$event->effective_scope_type][] = $ruleItem;
                            }
                            //  当前没有其他券，生效范围必填
                            else {
                                $couponList['other'][] = $ruleItem;
                            }
                        }
                    }

                }
            }
        }

        $showList = [];
        $type = Yii::$app->request->get('type');
        if (empty($type)) {
            $showList = $totalCouponList;
        } elseif (isset($couponList[$type])) {
            $showList = $couponList[$type];
        }

        $notHaveCount = count($couponList['notHave']);
        $data['not_have_count'] = $notHaveCount;
        $data['show_list'] = $showList;

        $typeList = array_keys($couponList);

        $typeMap = Event::$effectiveScopeTypeMap;
        $data['coupon_type'] = [];
        foreach ($typeList as $couponType) {
            if (isset($typeMap[$couponType])) {
                $data['coupon_type'][$couponType] = $typeMap[$couponType] . '券';
            }
        }
        if (!empty($couponList['other'])) {
            $data['coupon_type']['other'] = '其他券';
        }
        if (!empty($couponList['notHave'])) {
            $data['coupon_type']['notHave'] = '未领券';
        }

        return $data;
    }

    public function actionCoupon_receive($id) {
        $event = \common\models\Event::find()->alias('event')->joinWith([
            'fullCutRule fullCutRule' => function ($query) {
                $query->andOnCondition([
                    'status' => \common\models\FullCutRule::STATUS_VALID,
                ]);
            }
        ])->where([
            'event.event_id' => $id,
        ])->andWhere([
            'event.event_type' => \common\models\Event::EVENT_TYPE_COUPON,
        ])->andWhere([
            '<',
            'event.pre_time',
            \common\helper\DateTimeHelper::getFormatDateTimeNow()
        ])->andWhere([
            '>',
            'event.end_time',
            \common\helper\DateTimeHelper::getFormatDateTimeNow()
        ])->andWhere([
            'event.is_active' => \common\models\Event::IS_ACTIVE,
        ])->andWhere([
            'event.receive_type' => \common\models\Event::RECEIVE_TYPE_DRAW,
        ])->one();

        $coupons = [];
        foreach ($event['fullCutRule'] as $fullCutRule) {
            $coupons[] = [
                'rule_name' => $fullCutRule['rule_name'],
                'event_name' => $event['event_name'],
            ];
        }
        return [
            'event_id' => $event['event_id'],
            'sub_type' => $event['sub_type'],
            'rest_count' => $event['times_limit'] - $event['fullCutRule'][0]->getCouponCountTaken(Yii::$app->user->getId()),
            'coupons' => $coupons,
        ];
    }


    public function actionCoupon_list()
    {
        $userId = Yii::$app->user->getId();

        \common\models\RedDot::deleteAll([
            'user_id' => $userId,
        ]);

        $status = Yii::$app->request->get('status', CouponRecord::COUPON_STATUS_UNUSED);

        //  获取指定状态的优惠券
        $couponList = CouponHelper::getCouponList($userId, $status);
        $couponListFormat = CouponHelper::getCouponListFormat($couponList);

        //  获取优惠券的分类统计数量
        $couponCountMap = CouponHelper::getCouponCountMap($userId);

        $coupons = [];
        foreach ($couponListFormat as $coupon) {
            $coupons[] = [
                'bg_color' => $coupon['event']['bgcolor'],
                'amount' => $coupon['fullCutRule']['cutFormat'],
                'rule_name' => $coupon['fullCutRule']['rule_name'],
                'can_use_now' => $coupon['canUseNow'],
                'sub_type' => $coupon['event']['sub_type'],
                'event_id' => $coupon['event_id'],
                'event_name' => $coupon['event']['event_name'],
                'event_desc' => $coupon['event']['event_desc'],
                'start_date' => $coupon['start_date'],
                'end_date' => $coupon['end_date'],
            ];
        }
        return [
            'coupon_count_map' => [
                'un_used_num' => $couponCountMap['unusedNum'],
                'used_num' => $couponCountMap['usedNum'],
                'expired_num' => $couponCountMap['expiredNum'],
            ],
            'coupon_list' => $coupons,
            'status' => $status,
        ];
    }
}