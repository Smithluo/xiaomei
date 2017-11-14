<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_order_action".
 *
 * @property string $action_id
 * @property string $order_id
 * @property string $action_user
 * @property integer $order_status
 * @property integer $shipping_status
 * @property integer $pay_status
 * @property integer $action_place
 * @property string $action_note
 * @property string $log_time
 */
class OrderAction extends \yii\db\ActiveRecord
{
    const ORDER_STATUS_NOT_CONFIRMED    = 0;
    const ORDER_STATUS_CONFIRMED        = 1;
    const ORDER_STATUS_CANCELED         = 2;
    const ORDER_STATUS_INVALID          = 3;
    const ORDER_STATUS_REJECTED         = 4;

    const SHIPPING_STATUS_NOT_SHIPPED   = 0;
    const SHIPPING_STATUS_SHIPPED       = 1;
    const SHIPPING_STATUS_CANCELED      = 2;
    const SHIPPING_STATUS_STOCKING      = 3;

    const PAY_STATUS_NOT_PAID           = 0;
    const PAY_STATUS_HALF_PAID          = 1;
    const PAY_STATUS_PAID               = 2;

    //  订单状态
    public static $order_status_map = [
        self::ORDER_STATUS_NOT_CONFIRMED    => '未确认',
        self::ORDER_STATUS_CONFIRMED        => '已确认',
        self::ORDER_STATUS_CANCELED         => '已取消',
        self::ORDER_STATUS_INVALID          => '无效',
        self::ORDER_STATUS_REJECTED         => '退货'
    ];

    //  配送状态
    public static $shipping_status_map = [
        self::SHIPPING_STATUS_NOT_SHIPPED   => '未发货',
        self::SHIPPING_STATUS_SHIPPED       => '已发货',
        self::SHIPPING_STATUS_CANCELED      => '已取消',   //  可能用于 快递单填错时 取消发货，然后再次发货（填写新的快递单号）
        self::SHIPPING_STATUS_STOCKING      => '备货中',
    ];

    //  支付状态
    public static $pay_status_map = [
        self::PAY_STATUS_NOT_PAID => '未付款',
        self::PAY_STATUS_HALF_PAID => '付款中',
        self::PAY_STATUS_PAID => '已付款',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_order_action';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'order_status', 'shipping_status', 'pay_status', 'action_place', 'log_time'], 'integer'],
            [['action_user'], 'string', 'max' => 30],
            [['action_note'], 'string', 'max' => 255],
            [['action_note'], 'default', 'value' => ''],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'action_id'         => '流水号',
            'order_id'          => '订单ID',
            'action_user'       => '操作人',
            'order_status'      => '订单状态',      //  0未确认, 1已确认; 2已取消; 3无效; 4退货
            'shipping_status'   => '发货状态',      //  0未发货; 1已发货  2已取消  3备货中
            'pay_status'        => '支付状态',      //  0未付款; 1已付款中;  2已付款
            'action_place'      => '操作地点',      //  当前没用到
            'action_note'       => '操作备注',
            'log_time'          => '操作时间',
        ];
    }

    /**
     * @inheritdoc
     * @return OrderActionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new OrderActionQuery(get_called_class());
    }
}
