<?php

namespace common\models\payment;

use common\models\OrderInfo;
use Yii;

/**
 * This is the model class for table "o_pay_log".
 *
 * @property string $log_id
 * @property string $order_id
 * @property string $order_amount
 * @property integer $order_type
 * @property integer $is_paid
 */
class PayLog extends \yii\db\ActiveRecord
{
    const ORDER_TYPE_GENERAL    = 0;
    const ORDER_TYPE_PREPAY     = 1;
    const ORDER_TYPE_INTEGRAL   = 2;

    public static $orderTypeMap = [
        self::ORDER_TYPE_GENERAL    => '商品支付',
        self::ORDER_TYPE_PREPAY     => '商品预付',
        self::ORDER_TYPE_INTEGRAL   => '积分兑换',
    ];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_pay_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'order_type', 'is_paid'], 'integer'],
            [['order_amount'], 'required'],
            [['order_amount'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'log_id' => 'Log ID',
            'order_id' => 'Order ID',
            'order_amount' => 'Order Amount',
            'order_type' => 'Order Type',
            'is_paid' => 'Is Paid',
        ];
    }

    /**
     * @inheritdoc
     * @return PayLogQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PayLogQuery(get_called_class());
    }

    /**
     * 关联订单
     * @return \yii\db\ActiveQuery
     */
    public function getOrderInfo()
    {
        return $this->hasOne(OrderInfo::className(), ['order_id' => 'order_id']);
    }
}
