<?php
/**
 * Created by PhpStorm.
 * User: clark
 * Date: 2016/11/15
 * Time: 14:58
 */

namespace backend\modules\dashboard\models;

use yii\base\Model;
use common\models\dashboard\Mark as ComMark;

class MarkCountForm extends Model
{
    public $start_time;
    public $end_time;
    public $user_id;
    public $login_days;
    public $login_times;
    public $click_times;
    public $order_count;
    public $pay_count;
    public $user_name;
    public $mobile_phone;
    public $plat_form;

    public function rules()
    {
        return [
            [['start_time', 'end_time', 'user_name', 'plat_form'], 'string'],
            [['user_id', 'login_times', 'click_times', 'order_count', 'pay_count', 'login_days'], 'integer'],
            [['start_time', 'end_time', 'user_id', 'login_times', 'click_times', 'login_days',
                'order_count', 'pay_count', 'mobile_phone'], 'safe'],
        ];

    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'start_time'    => '查询时段开始时间',
            'end_time'      => '查询时段结束时间',
            'user_id' => '用户ID',
            'plat_form' => '平台',
            'user_name' => '用户名',
            'mobile_phone' => '手机号',
            'login_days' => '登录天数',
            'login_times' => '登录次数',
            'click_times' => '浏览页面数量',
            'order_count' => '下单数量',
            'order_times' => '下单次数',
            'pay_count' => '支付订单数量',
            'pay_times' => '支付订单次数',
        ]);
    }

    /**
     * 统计用户行为
     * @param $params
     * @return array|\common\models\dashboard\Mark[]
     */
    public function count($params) {
        $this->load($params);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return [];
        }

        $query = ComMark::find();

        if ($this->user_id) {
            $query->andWhere(['user_id' => $this->user_id]);
        }


        if (!empty($this->start_time) && !empty($this->end_time)) {
            $query->andWhere([
                'between', 'date', $this->start_time, $this->end_time
            ]);
        }

        if ($this->login_times) {
            $query->andWhere(['>=', 'login_times', $this->login_times]);
        }

        if ($this->click_times) {
            $query->andWhere(['>=', 'click_times', $this->click_times]);
        }

        if ($this->order_count) {
            $query->andWhere(['>=', 'order_count', $this->order_count]);
        }

        if ($this->pay_count) {
            $query->andWhere(['>=', 'pay_count', $this->pay_count]);
        }

        return $query->all();
    }
}