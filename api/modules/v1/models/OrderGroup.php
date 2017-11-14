<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/5 0005
 * Time: 15:31
 */

namespace api\modules\v1\models;

use common\helper\DateTimeHelper;
use common\helper\NumberHelper;
use yii\helpers\ArrayHelper;

class OrderGroup extends \common\models\OrderGroup
{
    public $shippingDesc;

    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            ['shippingDesc', 'safe']
        ]);
    }

    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'shippingDesc' => '配送信息'
        ]);
    }

    public function fields()
    {
        return [
            'id' => function ($model) {
                return (int)$model->id;
            },
            'user_id',
            'group_id',
            'create_time' => function ($model) {
                return DateTimeHelper::getFormatCNDateTime($model['create_time']);
            },

            'group_status',

            'consignee',
            'country' => function ($model) {
                return (int)$model->country;
            },  //  '国家',
            'province' => function ($model) {
                return (int)$model->province;
            },  //  '省份',
            'city' => function ($model) {
                return (int)$model->city;
            },  //  '城市',
            'district' => function ($model) {
                return (int)$model->district;
            },  //  '区域',
            'address',
            'mobile',
            'pay_id' => function ($model) {
                return (int)$model->pay_id;
            },
            'pay_name',

            //下面是费用相关
            'goods_amount' => function ($model) {
                return NumberHelper::price_format($model->goods_amount);
            },  //  商品总金额
            'shipping_fee' => function ($model) {
                return NumberHelper::price_format($model->shipping_fee);
            },  //  运费
            'discount' => function ($model) {
                return NumberHelper::price_format($model->discount);
            },  //  '折扣金额',
            'money_paid' => function ($model) {
                return NumberHelper::price_format($model->money_paid);
            },  //  '已付款金额',
            'order_amount' => function ($model) {
                return NumberHelper::price_format($model->order_amount);
            },  //  '应付款金额',

            //下面是时间相关
            'pay_time',
            'shipping_time',
            'recv_time',

            'shippingDesc' => function ($model) {
                return (string)$model->shippingDesc;
            },

            'orders',
        ];
    }

    public function getOrders()
    {
        return $this->hasMany(OrderInfo::className(), [
            'group_id' => 'group_id',
        ]);
    }

    public function getDeliveryOrders()
    {
        return $this->hasMany(DeliveryOrder::className(), [
            'group_id' => 'group_id',
        ]);
    }

    public function getBackOrders() {
        return $this->hasMany(BackOrder::className(), [
            'group_id' => 'group_id',
        ]);
    }
}