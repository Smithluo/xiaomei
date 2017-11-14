<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/3 0003
 * Time: 16:33
 */

namespace api\modules\v1\models;


class YeePayInfo extends \common\models\YeePayinfo
{
    public function getPaylog() {
        return $this->hasOne(PayLog::className(), ['log_id' => 'pay_log_id']);
    }

    public function getOrderInfo() {
        return $this->hasOne(OrderInfo::className(), ['order_sn' => 'order_sn']);
    }
}