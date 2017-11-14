<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/5 0005
 * Time: 11:30
 */

namespace api\modules\v1\models;


class WechatPayInfo extends \common\models\WechatPayInfo
{
    public function getPaylog() {
        return $this->hasOne(PayLog::className(), ['log_id' => 'pay_log_id']);
    }

    public function getOrderInfo() {
        return $this->hasOne(OrderInfo::className(), ['order_sn' => 'order_sn']);
    }
}