<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/8 0008
 * Time: 11:49
 */

namespace backend\models;

use Yii;
use common\helper\DateTimeHelper;

class BackOrder extends \common\models\BackOrder
{
    public static function createByOrderInfo($orderInfo) {
        $backOrder = new BackOrder();
        $backOrder->delivery_sn = $orderInfo->order_sn;
        $backOrder->order_sn = $orderInfo->order_sn;
        $backOrder->order_id = $orderInfo->order_id;
        $backOrder->add_time = DateTimeHelper::gmtime();
        $backOrder->shipping_id = $orderInfo->shipping_id;
        $backOrder->shipping_name = $orderInfo->shipping_name;
        $backOrder->user_id = $orderInfo->user_id;
        $backOrder->action_user = Yii::$app->user->identity['user_name'];
        $backOrder->consignee = $orderInfo->consignee;
        $backOrder->address = $orderInfo->address;
        $backOrder->country = $orderInfo->country;
        $backOrder->province = $orderInfo->province;
        $backOrder->city = $orderInfo->city;
        $backOrder->district = $orderInfo->district;
        $backOrder->sign_building = null;
        $backOrder->mobile = $orderInfo->mobile;
        $backOrder->how_oos = '等待所有商品备齐后再发';
        $backOrder->insure_fee = 0;
        $backOrder->shipping_fee = 0;
        $backOrder->update_time = DateTimeHelper::gmtime();
        $backOrder->suppliers_id = 0;
        $backOrder->status = 0;
        $backOrder->express_id = 0;
        $backOrder->invoice_no = '';
        $backOrder->group_id = $orderInfo->group_id;
        return $backOrder;
    }

    public function getBackGoods()
    {
        return $this->hasMany(BackGoods::className(), [
            'back_id' => 'back_id',
        ]);
    }
}