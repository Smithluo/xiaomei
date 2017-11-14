<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/28
 * Time: 17:20
 */

namespace backend\models;

/**
 * Class OrderInfo
 * @package backend\models
 * @property array $ordergoods This property is read-only.
 * @property ServicerDivideRecord $servicerDivideRecord This property is read-only.
 */
class OrderInfo extends \common\models\OrderInfo
{
    /**
     * 获取分成记录
     * @return \yii\db\ActiveQuery
     */
    public function getServicerDivideRecord() {
        return $this->hasOne(ServicerDivideRecord::className(), ['order_id' => 'order_id']);
    }

    public function getOrdergoods()
    {
        return $this->hasMany(OrderGoods::className(), ['order_id' => 'order_id']);
    }

    public function getDeliveryOrder()
    {
        return $this->hasMany(DeliveryOrder::className(), ['order_id' => 'order_id']);
    }

}