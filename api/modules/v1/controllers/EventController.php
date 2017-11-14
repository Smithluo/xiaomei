<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 8/25/16
 * Time: 3:40 PM
 */

namespace api\modules\v1\controllers;

use api\modules\v1\models\EventUserCount;
use common\helper\EventHelper;
use common\models\CouponRecord;
use common\models\PaidCoupon;
use \Yii;
use api\modules\v1\models\EventRule;
use api\modules\v1\models\Event;
use api\modules\v1\models\EventToGoods;
use common\helper\DateTimeHelper;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\ServerErrorHttpException;

/**
 * 满减活动需要考虑用户实际支付的价格，启用权限校验
 * Class EventController
 * @package api\modules\v1\controllers
 */
class EventController extends BaseAuthActiveController
{
    public $modelClass = 'api\modules\v1\models\Event';

    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];

    /**
     * POST gifts 匹配策略, 获取赠品列表
     *  $goods_map[goods_id] = [
     *      'goods_id' => $goods_id,
     *      'buy_number' => $goods_number,
     *  ];
     */
    public function actionGifts()
    {
        $data = Yii::$app->request->post();
        $goods_map = $data['goods_map'];

        $goods_list = array_keys($goods_map);
        //  获取当前商品参与的所有有效的活动
        $time = (isset($data['time']) && $data['time'])
            ? $data['time']
            : DateTimeHelper::getFormatGMTTimesTimestamp(time());
        //  只有显示订单详情的时候回传递时间参数
        $is_active = empty($data['time']) ? true : false;
        $event_list = EventToGoods::getEventByGoodsForGift($goods_list, $time, $is_active);
        return $event_list;
        $event_detail = Event::getEventDetail($event_list);
        $gifts = [];
        foreach ($goods_map as $goods) {
            $goods_id = $goods['goods_id'];
            if (isset($event_detail[$goods_id]) && $event_detail[$goods_id]) {
                $event = current($event_detail[$goods_id]);

                //  单件商品满X件赠送B商品Y件
                if ($event['match_effect'] == EventRule::MATCH_EFFECT_ONE &&
                    $event['match_type'] == EventRule::MATCH_TYPE_GOODS_NUM)
                {
                    $gifts_goods_num = floor($goods['buy_number'] / $event['match_value']) * $event['gift_num'];
                    $gifts[$goods_id] = EventHelper::setGifts($event, $gifts_goods_num);
                }
            }
        }

        /*foreach ($event_detail as $in_event_goods_id => $events) {
//            foreach ($events as $event) {}
            //  当前只考虑一件商品只参与一个活动的场景
            $event = current($events);
            //  单件商品满X件赠送B商品Y件
            if ($event['match_effect'] == EventRule::MATCH_EFFECT_ONE &&
                $event['match_type'] == EventRule::MATCH_TYPE_GOODS_NUM)
            {
                foreach ($goods_map as $goods_id => $goods) {
                    if ($goods_id == $in_event_goods_id && $goods['goods_num'] >= $event['match_value']) {
                        $gifts_goods_num = floor($goods['goods_num'] / $event['match_value']) * $event['gift_num'];
                        $gifts[$goods_id] = EventHelper::setGifts($event, $gifts_goods_num);
                    }
                }
            }
            //  多件商品总件数满X件赠送B商品Y件
            elseif ($event['match_effect'] == EventRule::MATCH_EFFECT_ALL &&
                $event['match_type'] == EventRule::MATCH_TYPE_GOODS_NUM
            ) {
                $goods_num = 0;
                foreach ($goods_map as $goods_id => $goods) {
                    if ($goods_id == $in_event_goods_id)
                    $goods_num += $goods['goods_num'];
                }

                if ($goods_num >= $event['match_value']) {
                    $gifts_goods_num = floor($goods_num / $event['match_value']) * $event['gift_num'];
                    $gifts[$goods_id] = EventHelper::setGifts($event, $gifts_goods_num);
                }
            }
            //  单件商品总价满X元赠送B商品Y件
            elseif ($event['match_effect'] == EventRule::MATCH_EFFECT_ONE &&
                $event['match_type'] == EventRule::MATCH_TYPE_GOODS_AMOUNT
            ) {
                foreach ($goods_map as $goods_id => $goods) {
                    $goods_amount = $goods['goods_num'] * $goods['goods_price'];
                    if ($goods_amount >= $event['match_value']) {
                        $gifts_goods_num = floor($goods_amount / $event['match_value']) * $event['gift_num'];
                        $gifts[$goods_id] = EventHelper::setGifts($event, $gifts_goods_num);
                    }
                }
            }
            //  多件商品总价满X元赠送B商品Y件
            elseif ($event['match_effect'] == EventRule::MATCH_EFFECT_ALL &&
                $event['match_type'] == EventRule::MATCH_TYPE_GOODS_AMOUNT
            ) {
                $goods_amount = 0;
                foreach ($goods_map as $goods_id => $goods) {
                    $goods_amount += $goods['goods_num'] * $goods['goods_price'];
                }

                if ($goods_amount >= $event['match_value']) {
                    $gifts_goods_num = floor($goods_amount / $event['match_value']) * $event['gift_num'];
                    $gifts[$goods_id] = EventHelper::setGifts($event, $gifts_goods_num);
                }
            }

        }*/

        return $gifts;
    }

    /**
     * POST event/valid-events 获取有效的活动信息
     *
     * [
     *      'goodsMap' => [
     *          [
     *              'goods_id'      => $goods_id,
     *              'goods_number'  => $goods_number,
     *          ],
     *          [
     *              'goods_id'      => $goods_id,
     *              'goods_number'  => $goods_number,
     *          ]
     *      ],
     *      'time' => 下单时间
     *      'from_type' => 'm|pc|ios|andriod'
     * ]
     *
     * return array    [
     *      'gifts'     => [[赠品信息], [...]],
     *      'fullCut'   => [满减活动的所有信息],
     * ]
     */
    public function actionValid_events()
    {
        //  【1】接收参数
        $userModel = Yii::$app->user->identity;
        $data = Yii::$app->request->post();

        if (empty($data['goodsMap'])) {
            throw new BadRequestHttpException('缺少必要参数', 1);
        }

        if (empty($data['time'])) {
            $data['time'] = DateTimeHelper::getFormatGMTTimesTimestamp();
        }

        //  【2】获取当前有效的活动
        $validEvents = EventHelper::getValidEventList($data['goodsMap'], $userModel->user_rank, $data['time']);
        return $validEvents;
    }

    public function actionTake_coupon() {
        $userModel = Yii::$app->user->identity;
        $eventId = Yii::$app->request->get('event_id');

        if (empty($eventId)) {
            throw new BadRequestHttpException('缺少活动ID', 1);
        }

        //查找这个优惠券活动
        $event = Event::find()->joinWith([
            'fullCutRule fullCutRule',
            'couponPkg couponPkg',
        ])->where([
            Event::tableName(). '.event_id' => $eventId
        ])->andWhere([
            'is_active' => 1,
        ])->andWhere([
            'event_type' => Event::EVENT_TYPE_COUPON,
        ])->andWhere([
            'receive_type' => Event::RECEIVE_TYPE_DRAW
        ])->andWhere([
            '<',
            'pre_time',
            DateTimeHelper::getFormatCNDateTime(DateTimeHelper::gmtime()),
        ])->andWhere([
            '>',
            'end_time',
            DateTimeHelper::getFormatCNDateTime(DateTimeHelper::gmtime()),
        ])->one();
        if (empty($event)) {
            throw new BadRequestHttpException('缺少这个活动', 2);
        }
        else {
            Yii::trace('event = '. VarDumper::export($event), __METHOD__);
        }

        $existsPaidCoupon = PaidCoupon::find()->where([
            'event_id' => $eventId
        ])->exists();
        if ($existsPaidCoupon) {
            Yii::error('满额才能领取券 eventId = '. $eventId, __METHOD__);
            throw new BadRequestHttpException('满额才能领券', 5);
        }

        foreach($event->fullCutRule as $rule) {
            $event->takeCoupon($userModel->user_id, $rule['rule_id']);
        }

        return [
            'msg' => '已领取优惠券',
        ];
    }

    public function actionTake_coupon_by_rule() {

        $eventId = Yii::$app->request->get('event_id');
        if (empty($eventId)) {
            throw new BadRequestHttpException('缺少活动ID', 9);
        }

        $ruleId = Yii::$app->request->get('rule_id');
        if (empty($ruleId)) {
            throw new BadRequestHttpException('缺少规则ID', 10);
        }

        $event = \common\models\Event::find()->joinWith([
            'couponPkg couponPkg',
            'fullCutRule fullCutRule',
        ])->where([
            'event_type' => \common\models\Event::EVENT_TYPE_COUPON,
            'is_active' => 1,
            \common\models\Event::tableName().'.event_id' => $eventId,
        ])->one();

        if (empty($event)) {
            throw new BadRequestHttpException('缺少活动', 11);
        }

        $result = $event->takeCoupon(Yii::$app->user->identity['user_id'], $ruleId, true);

        //临时这样处理，防止一个用户领多次券
        if ($result['code'] == 0) {
            return [
                'msg' => $result['msg'],
            ];
        }
        throw new ServerErrorHttpException($result['msg'], $result['code']);
    }
}