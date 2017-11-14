<?php

namespace backend\modules\dashboard\models;

/**
 * Created by PhpStorm.
 * User: clark
 * Date: 2016/11/15
 * Time: 11:13
 */
class Mark extends \common\models\dashboard\Mark 
{
    public $start_time; //  查询时段开始时间
    public $end_time;   //  查询时段结束时间

    public function rules()
    {
        return [
            [['start_time', 'end_time', 'plat_form'], 'string'],
            [['user_id', 'login_times', 'click_times', 'order_count', 'pay_count'], 'integer'],
            [['start_time', 'end_time', 'user_id', 'login_times', 'click_times', 'order_count', 'pay_count'], 'safe'],
        ];

    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'start_time'    => '查询开始时间',
            'end_time'      => '查询结束时间',
        ]);
    }
}

//public $date;
//public $user_id;
//public $login_times;
//public $click_times;
//public $order_count;
//public $pay_count;