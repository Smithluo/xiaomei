<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 8/23/16
 * Time: 4:05 PM
 */

namespace api\modules\v1\models;

use \Yii;
use common\helper\EventHelper;
use common\helper\ImageHelper;
use common\helper\NumberHelper;
use common\helper\DateTimeHelper;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;

class Event extends \common\models\Event
{

    public function fields()
    {
        return [
            'event_id' => function ($model) {
                return (int)$model->event_id;
            },
            'event_type' => function ($model) {
                return (int)$model->event_type;
            },
            'event_name',
            'event_desc',
            'pkg_id' => function ($model) {
                return (int)$model->pkg_id;
            },
            'rule_id' => function ($model) {
                return (int)$model->rule_id;
            },
            'start_time' => function ($model) {
                return (string)$model->start_time;
            },
            'end_time' => function ($model) {
                return (string)$model->end_time;
            },
            'updated_at' => function ($model) {
                return (int)$model->updated_at;
            },
            'updated_by' => function ($model) {
                return (int)$model->updated_by;
            },
            'is_active' => function ($model) {
                return (int)$model->is_active;
            },
            'banner' => function ($model) {
                return ImageHelper::get_image_path($model->banner);
            },
            'url',
            'bgcolor',
            'sub_type',
        ];
    }

    /**
     * 根据有效的活动 获取活动详情
     * @param $event_list
     * @return array
     */
    public static function getEventDetail($event_list)
    {
        Yii::warning(' 入参 $event_list = '.json_encode($event_list), __METHOD__);
        $event_detail = [];
        $event_id_list = array_keys($event_list);

        $event_tb = Event::tableName();
        $event_rule_tb = EventRule::tableName();
        $event_detail_list = Event::find()
            ->select([
                $event_tb.'.event_id', $event_tb.'.event_name', $event_tb.'.event_desc', $event_tb.'.event_type',
                $event_rule_tb.'.gift_id', $event_rule_tb.'.gift_num',
                $event_rule_tb.'.match_type', $event_rule_tb.'.match_value', $event_rule_tb.'.match_effect',
                $event_rule_tb.'.gift_show_peice', $event_rule_tb.'.gift_need_pay'
            ])->leftJoin($event_rule_tb, $event_rule_tb.'.rule_id = '.$event_tb.'.rule_id')
            ->where(['event_id' => $event_id_list])
            ->asArray()
            ->all();
        Yii::warning(' $event_detail_list = '.json_encode($event_detail_list), __METHOD__);

        //  获取参与活动的商品和赠品的 库存， 便于计算当前最大可购买数量
        $goods_id_list = array_column($event_detail_list, 'gift_id');
        foreach ($event_list as $goods_list) {
            if (!empty($goods_id_list)) {
                $goods_id_list = array_merge($goods_id_list, $goods_list);
            } else {
                $goods_id_list = $goods_list;
            }
        }
        $goods_id_list = array_unique($goods_id_list);
        $goods_id_list = array_filter($goods_id_list);  //  过滤为空的数据
        Yii::warning(' $goods_id_list = '.json_encode($goods_id_list), __METHOD__);
        //  过滤
        $goodsResult = Goods::find()->select(['goods_id', 'goods_number', 'buy_by_box', 'number_per_box'])
            ->where(['goods_id' => $goods_id_list])
            ->indexBy('goods_id')
            ->all();
        Yii::warning(' $goodsResult = '.VarDumper::export($event_detail_list), __METHOD__);

        foreach ($event_detail_list as $event) {

            //  一个SKU不同时参与 满减、满赠——做告警
            if (
                $event['event_type'] == self::EVENT_TYPE_FULL_GIFT
                && $event['event_id']
                && $event['gift_id'] > 0
            ) {
                $gift = Goods::find()->select(['goods_name', 'goods_number', 'goods_thumb'])
                    ->where(['goods_id' => $event['gift_id']])
                    ->one();

                //  库存不足即活动失效
                if ($gift != null && $gift->goods_number >= $event['gift_num']) {
                    foreach ($event_list[$event['event_id']] as $goods_id) {

                    //  计算参与活动的商品的最大可购买数量、最大满赠倍数
                    if ($goods_id == $event['gift_id']) {
                        $goodsMatchTimesMax = floor($goodsResult[$goods_id]['goods_number'] / ($event['match_value'] + $event['gift_num']));
                        $matchTimesMax = $goodsMatchTimesMax;
                    } else {
                        $goodsMatchTimesMax = floor($goodsResult[$goods_id]['goods_number'] / $event['match_value']);
                        $giftsMatchTimesMax = floor($goodsResult[$event['gift_id']]['goods_number'] / $event['gift_num']);
                        $matchTimesMax = min($giftsMatchTimesMax, $goodsMatchTimesMax);
                    }
                    //  修正商品的最大可购买数量,考虑按箱购买
                    if (!empty($goodsResult[$goods_id]['buy_by_box'])) {
                        $goods_max_num = $event['match_value'] * ($matchTimesMax + 1) - $goodsResult[$goods_id]['number_per_box'];
                    } else {
                        $goods_max_num = $event['match_value'] * ($matchTimesMax + 1) - 1;
                    }

                    if ($goods_max_num > $goodsResult[$goods_id]['goods_number']) {
                        $goods_max_num = $goodsResult[$goods_id]['goods_number'];
                    }

                        $event_detail[$goods_id][] = [
                            'goods_id' => $goods_id,
                            'event_id' => intval($event['event_id']),
                            'event_type' => intval($event['event_type']),
                            'event_name' => $event['event_name'],
                            'event_desc' => $event['event_desc'],
                            'gift_show_peice' => $event['gift_show_peice'],
                            'gift_need_pay' => $event['gift_need_pay'],
                            'gift_num' => $event['gift_num'],
                            'gift_id' => $event['gift_id'],
                            'match_type' => $event['match_type'],
                            'match_value' => $event['match_value'],
                            'match_effect' => $event['match_effect'],
                            'goods_name' => $gift->goods_name,
                            'goods_thumb' => ImageHelper::get_image_path($gift->goods_thumb),
                            'gift_amount' => NumberHelper::price_format($event['gift_num'] * $event['gift_show_peice']),
                            'gift_need_pay' => $event['gift_num'] * $event['gift_need_pay'],
                            'goods_max_num' => $goods_max_num,  //  主商品的最大购买数量
                            'gift_number_max' => $gift['goods_number'],  //  赠品的最大库存
                        ];
                    }
                }
            }
            elseif ($event['event_type'] == self::EVENT_TYPE_FULL_CUT) {
                if (count($goods_id_list) == 1) {
                    foreach ($goods_id_list as $gods_id) {
                        $event_detail[$gods_id][] = [
                            'goods_id' => $gods_id,
                            'event_id' => intval($event['event_id']),
                            'event_type' => intval($event['event_type']),
                            'event_name' => $event['event_name'],
                            'event_desc' => $event['event_desc'],
                        ];
                    }
                }
            }
        }
        Yii::warning(' $event_detail = '.json_encode($event_detail), __METHOD__);

        return $event_detail;
    }

    /**
     * 获取单个商品的赠品信息
     *
     * @param $goodsId      商品ID
     * @param $goodsNumber  购买数量
     * @return array
     */
    public static function getGiftForSingleGoods($goodsId, $goodsNumber)
    {
        $gift = [];
        //  获取商品的赠品信息
        $time = DateTimeHelper::getFormatGMTTimesTimestamp(time());
        $is_active = true;
        $event_list = EventToGoods::getEventByGoodsForGift($goodsId, $time, $is_active);
        $event_detail = Event::getEventDetail($event_list);
        if (isset($event_detail[$goodsId]) && $event_detail[$goodsId]) {
            $event = current($event_detail[$goodsId]);

            //  单件商品满X件赠送B商品Y件
            if (
                $event['match_effect'] == EventRule::MATCH_EFFECT_ONE &&
                $event['match_type'] == EventRule::MATCH_TYPE_GOODS_NUM)
            {
                $gifts_goods_num = floor($goodsNumber / $event['match_value']) * $event['gift_num'];
                $gift = EventHelper::setGifts($event, $gifts_goods_num);
                $gift = array_merge($gift, [
                    'gift_number_max' => (int)$event['gift_number_max'],    //  主商品的最大购买数量
                    'goods_thumb' => $event['goods_thumb'],
                    'parent_id' => $goodsId,
                ]);
            }
        }

        return $gift;
    }
}