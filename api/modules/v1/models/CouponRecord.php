<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/19 0019
 * Time: 11:03
 */

namespace api\modules\v1\models;

use common\helper\DateTimeHelper;

class CouponRecord extends \common\models\CouponRecord
{
    public function fields()
    {
        return [
            'coupon_id' => function ($model) {
                return (int)$model->event_id;
            },
            'status' => function ($model) {
                return (int)$model->status;
            },
            'start_time' => function ($model) {
                return DateTimeHelper::getFormatDate($model['start_time']);
            },
            'end_time' => function ($model) {
                return DateTimeHelper::getFormatDate($model['end_time']);
            },
        ];
    }

    public function getEvent()
    {
        return $this->hasOne(Event::className(), [
            'event_id' => 'event_id',
        ]);
    }

    public function getFullCutRule()
    {
        return $this->hasOne(FullCutRule::className(), [
            'rule_id' => 'rule_id',
        ]);
    }
}