<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 8/18/16
 * Time: 4:15 PM
 */

namespace backend\models;

use yii\helpers\ArrayHelper;

class Event extends \common\models\Event
{
    public $eventToGoodsList;       //  参与活动的商品
    public $eventToBrandList;      //  参与活动的品牌
    public $eventFilterGoodsList;   //  不参与活动的商品(过滤)
    public $pkgEnable;

    public function rules()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                [['eventToGoodsList', 'eventToBrandList', 'eventFilterGoodsList', 'pkgEnable'], 'safe'],
//                ['eventToGoodsList', 'required', 'on' => 'insert'],
            ]
        );
    }

    public function attributeLabels()
    {
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
                'eventToGoodsList'      => '参与活动的商品列表',
                'eventToBrandList'     => '参与活动的品牌列表',
                'eventFilterGoodsList'  => '参与活动的品牌列表',
                'pkgEnable'             => '是否作为券包供用户领取',
            ]
        );
    }

    /**
     * 获取活动名称列表
     * @param string $type  空值则获取全部，非空则获取对应类型的活动列表
     * @return array
     */
    public static function getEventNameMap($type = '')
    {
        $query = self::find()
            ->select(['event_id', 'event_name']);

        if ($type) {
            $query->where(['event_type' => $type]);
        }

        $rs = $query->asArray()->all();
        return array_column($rs, 'event_name', 'event_id');
    }

    /**
     * 获取活动描述列表
     * @return array
     */
    public static function getEventDescMap()
    {
        $rs = self::find()
            ->select(['event_id', 'event_desc'])
            ->asArray()
            ->all();
        return array_column($rs, 'event_desc', 'event_id');
    }

    /**
     * 获取 满赠、物料配比的 活动映射
     * @param array $type   活动类型
     * @return array
     */
    public static function giftEventMap($type = [])
    {
        $giftEventMap = [];
        $eventQuery = self::find()
            ->select(['event_id', 'event_type', 'event_name', 'is_active', 'effective_scope_type']);

        if (!empty($type)) {
            $eventQuery->where(['event_type' => $type]);
        }

        $event = $eventQuery->all();

        if (!empty($event)) {
            foreach ($event as $item) {
                $str = Event::$eventTypeMap[$item->event_type];
                $str .= '['.$item->event_id.']';
                $str .= $item->event_name;
                $str .= '['.Event::$is_active_map[$item->is_active].']';
                $str .= Event::$effectiveScopeTypeMap[$item->effective_scope_type];


                $giftEventMap[$item->event_id] = $str;
            }
        }

        return $giftEventMap;
    }
}