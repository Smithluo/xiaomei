<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 8/23/16
 * Time: 3:44 PM
 */

namespace api\modules\v1\models;


class EventToGoods extends \common\models\EventToGoods
{
    /**
     * 根据商品ID列表 获取对应的活动列表，
     * @param $goodsList
     * @return array    [ [event_id] => [goods_id, goods_id] ]
     */
    public static function getEventList($goodsList)
    {
        $map = self::find()->select()->where(['goods_id' => $goodsList])->asArray()->all();

        if ($map) {
            foreach ($map as $item) {
                $rs[$item->event_id][] = $item->goods_id;
            }
        } else {
            $rs = [];
        }

        return $rs;
    }
}