<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/14 0014
 * Time: 11:37
 */

namespace backend\models;

use common\helper\DateTimeHelper;
use common\helper\NumberHelper;
use common\models\ServicerSpecStrategy;
use common\models\ShopConfig;
use Yii;
use yii\helpers\VarDumper;

class DeliveryOrder extends \common\models\DeliveryOrder
{

    public function getDeliveryGoods()
    {
        return $this->hasMany(DeliveryGoods::className(), [
            'delivery_id' => 'delivery_id'
        ]);
    }

    public function getServicerDivideRecord() {
        return $this->hasOne(ServicerDivideRecord::className(), [
            'delivery_id' => 'delivery_id'
        ]);
    }

    /**
     * 获取订单
     * @return \yii\db\ActiveQuery
     */
    public function getOrderInfo() {
        return $this->hasOne(OrderInfo::className(), [
            'order_id' => 'order_id',
        ]);
    }

}