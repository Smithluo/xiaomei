<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 8/18/16
 * Time: 4:16 PM
 */

namespace backend\models;


use yii\helpers\ArrayHelper;

class EventRule extends \common\models\EventRule
{
    /**
     * 获取满赠活动的规则列表
     * 兼容
     *      过去o_event.rule_id 指定的规则
     *  和
     *      改版后 通过 o_event_rule.event_id 关联的多条规则
     * @param int $eventId
     * @return array
     */
    public static function getRuleMap($eventId)
    {
        $rs = [];
        if (!empty($eventId)) {
            $event = Event::findOne($eventId);
            if (!empty($event)) {
                $rules = self::find()
                    ->select(['rule_name', 'rule_id'])
                    ->where(['event_id' => $eventId])
                    ->indexBy('rule_id')
                    ->all();

                $rs = ArrayHelper::getColumn($rules, 'rule_name', true);
            }
        }

        return $rs;
    }
}