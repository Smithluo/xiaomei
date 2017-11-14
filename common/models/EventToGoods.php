<?php

namespace common\models;

use common\helper\DateTimeHelper;
use common\helper\NumberHelper;
use Yii;
use \yii\db\Query;

/**
 * This is the model class for table "o_event_to_goods".
 *
 * @property integer $id
 * @property integer $event_id
 * @property integer $goods_id
 */
class EventToGoods extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_event_to_goods';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['event_id', 'goods_id'], 'required'],
            [['event_id', 'goods_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '活动与商品的关联关系',
            'event_id' => '活动ID',
            'goods_id' => '商品ID',
        ];
    }

    /**
     * 根据商品列表获取活动ID
     *
     * @param $goods_list
     * @param $time
     * @param $is_active
     * @return array
     */
    public static function getEventByGoods($goods_list, $time, $is_active)
    {
        $result = [];

        $rs = self::find()->select(['event_id', 'goods_id'])
            ->where(['goods_id' => $goods_list])
            ->all();
        if ($rs) {
            foreach ($rs as $item) {
//                $event = $item->getEvent();
                $event = Event::find()->where(['event_id' => $item->event_id])->one();
                if ($event  && $event->start_time < $time && $event->end_time > $time) {
                    //  不需要校验活动当前有效(订单中的显示) 或（需要校验并且）当前活动有效
                    if (!$is_active || $event->is_active == Event::IS_ACTIVE) {
                        $result[$item->event_id][] = $item->goods_id;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * 根据商品列表获取活动ID
     *
     * @param $goods_list
     * @param $time
     * @param $is_active
     * @return array
     */
    public static function getEventByGoodsForGift($goods_list, $time, $is_active)
    {
        $result = [];

        $rs = self::find()->select(['event_id', 'goods_id'])
            ->where(['goods_id' => $goods_list])
            ->all();
        if ($rs) {
            foreach ($rs as $item) {
                $event = Event::find()
                    ->where([
                        'event_id' => $item->event_id,
                        'event_type' => Event::EVENT_TYPE_FULL_GIFT
                    ])->one();
                if ($event  && $event->start_time < $time && $event->end_time > $time) {
                    //  不需要校验活动当前有效(订单中的显示) 或（需要校验并且）当前活动有效
                    if (!$is_active || $event->is_active == Event::IS_ACTIVE) {
                        $result[$item->event_id][] = $item->goods_id;
                    }
                }
            }
        }

        return $result;
    }

    public function getEvent()
    {
        return $this->hasOne(Event::className(), ['event_id' => 'event_id']);
    }

    public function getGoods()
    {
        return $this->hasOne(Goods::className(), ['goods_id' => 'goods_id']);
    }

}