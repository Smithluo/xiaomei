<?php

namespace common\models;

use api\helper\OrderHelper;
use backend\models\BackGoods;
use backend\models\BackOrder;
use common\behaviors\RecordOrderActionBehavior;
use common\behaviors\RecordOrderModifyActionBehavior;
use common\helper\CacheHelper;
use common\helper\DateTimeHelper;
use common\helper\NumberHelper;
use common\helper\OrderGroupHelper;
use common\helper\PaymentHelper;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "{{%o_order_info}}".
 *
 * @property string $order_id
 * @property string $order_sn
 * @property string $user_id
 * @property integer $order_status
 * @property integer $shipping_status
 * @property integer $pay_status
 * @property string $consignee
 * @property integer $country
 * @property integer $province
 * @property integer $city
 * @property integer $district
 * @property string $address
 * @property string $zipcode
 * @property string $tel
 * @property string $mobile
 * @property string $email
 * @property string $best_time
 * @property string $sign_building
 * @property string $postscript
 * @property integer $shipping_id
 * @property string $shipping_name
 * @property integer $pay_id
 * @property string $pay_name
 * @property string $how_oos
 * @property string $how_surplus
 * @property string $pack_name
 * @property string $card_name
 * @property string $card_message
 * @property string $inv_payee
 * @property string $inv_content
 * @property string $goods_amount
 * @property string $shipping_fee
 * @property string $insure_fee
 * @property string $pay_fee
 * @property string $pack_fee
 * @property string $card_fee
 * @property string $money_paid
 * @property string $surplus
 * @property string $integral
 * @property string $integral_money
 * @property string $bonus
 * @property string $order_amount
 * @property integer $from_ad
 * @property string $referer
 * @property string $add_time
 * @property string $confirm_time
 * @property string $pay_time
 * @property string $shipping_time
 * @property integer $recv_time
 * @property integer $pack_id
 * @property integer $card_id
 * @property string $bonus_id
 * @property string $invoice_no
 * @property string $extension_code
 * @property string $extension_id
 * @property string $to_buyer
 * @property string $pay_note
 * @property integer $agency_id
 * @property string $inv_type
 * @property string $tax
 * @property integer $is_separate
 * @property string $parent_id
 * @property string $discount
 * @property string $mobile_pay
 * @property string $mobile_order
 * @property string $group_id
 * @property integer $supplier_user_id
 * @property integer $offline
 * @property string $group_identity
 * @property integer $brand_id
 */
class OrderInfo extends \yii\db\ActiveRecord
{
    const ORDER_STATUS_UNCONFIRMED = 0; // 未确认
    const ORDER_STATUS_CONFIRMED = 1; // 已确认
    const ORDER_STATUS_CANCELED = 2; // 已取消
    const ORDER_STATUS_INVALID = 3; // 无效
    const ORDER_STATUS_RETURNED = 4; // 退货中
    const ORDER_STATUS_SPLITED = 5; // 已分单
    const ORDER_STATUS_SPLITING_PART = 6; // 部分分单
    const ORDER_STATUS_ASK_4_REFUND = 7; // 申请退款
    const ORDER_STATUS_ASK_4_RETURN = 8; // 申请退货
    const ORDER_STATUS_RETURNED_DONE = 9; // 退货完成
    const ORDER_STATUS_REFUNDED_DONE = 10; // 退款完成
    const ORDER_STATUS_DONE = 11; // 已完成
    const ORDER_STATUS_REALLY_DONE = 12; // 真实已完成（不允许退货）
    const ORDER_STATUS_AGREE_RETURN = 13; // 同意退货

    const SHIPPING_STATUS_UNSHIPPED = 0; // 未发货
    const SHIPPING_STATUS_SHIPPED = 1; // 已发货
    const SHIPPING_STATUS_RECEIVED = 2; // 已收货
    const SHIPPING_STATUS_PREPARING = 3; // 备货中
    const SHIPPING_STATUS_SHIPPED_PART = 4; // 已发货(部分商品)
    const SHIPPING_STATUS_SHIPPED_ING = 5; // 发货中(处理分单)
    const ORDER_STATUS_SHIPPED_PART = 6; // 已发货(部分商品)

    const PAY_STATUS_UNPAYED = 0; // 未付款
    const PAY_STATUS_PAYING = 1; // 付款中
    const PAY_STATUS_PAYED = 2; // 已付款
    const PAY_STATUS_REFUND = 3; // 已退款

    //  订单综合状态
    const ORDER_CS_STATUS_CANCELED = 0;    //  已取消
    const ORDER_CS_STATUS_TO_BE_PAID = 1;    //  待支付
    const ORDER_CS_STATUS_TO_BE_SHIPPED = 2;    //  待发货
    const ORDER_CS_STATUS_TO_BE_REFUNDED = 3;    //  退款中
    const ORDER_CS_STATUS_REFUNDED_DONE = 4;    //  退款完成
    const ORDER_CS_STATUS_SHIPPED = 5;    //  已发货
    const ORDER_CS_STATUS_COMPLETED = 6;    //  已确认收货
    const ORDER_CS_STATUS_TO_BE_RETURNED = 7;    //  待退货
    const ORDER_CS_STATUS_RETURNED = 8;    //  退货中（用户已退货、显示快递信息）
    const ORDER_CS_STATUS_RETURNED_DONE = 9;    //  退货完成
    const ORDER_CS_STATUS_COMPLETED_OVER = 10;   //  已完结（可分成）
    const ORDER_CS_STATUS_ASK_4_RETURN = 11;    //  用户申请了退货
    const ORDER_CS_STATUS_SHIPPED_PART = 12;
//    const ORDER_CS_STATUS_ALL                   = 11;   //  所有订单

    const EXTENSION_CODE_GENERAL            = 'general';
    const EXTENSION_CODE_GENERAL_BUY_NOW    = 'general_buy_now';
    const EXTENSION_CODE_GENERAL_BATCH      = 'general_batch';
    const EXTENSION_CODE_GROUPBUY           = 'group_buy';
    const EXTENSION_CODE_FLASHSALE          = 'flash_sale';
    const EXTENSION_CODE_INTEGRAL           = 'integral_exchange';
    const EXTENSION_CODE_GIFT_PKG           = 'gift_pkg';

    //  显示给品牌商的订单状态
    public static $order_status_show = [
//        self::ORDER_STATUS_UNCONFIRMED,
//        self::ORDER_STATUS_CONFIRMED,
//        self::ORDER_STATUS_CANCELED,  //  已取消
//        self::ORDER_STATUS_INVALID,   //  无效
        self::ORDER_STATUS_RETURNED,
        self::ORDER_STATUS_SPLITED,
        self::ORDER_STATUS_SPLITING_PART,
//        self::ORDER_STATUS_ASK_4_REFUND,
        self::ORDER_STATUS_ASK_4_RETURN,
        self::ORDER_STATUS_RETURNED_DONE,
//        self::ORDER_STATUS_REFUNDED_DONE, //  退款完成
        self::ORDER_STATUS_DONE,
        self::ORDER_STATUS_REALLY_DONE,
        self::ORDER_STATUS_AGREE_RETURN,
    ];

    //  订单状态
    public static $order_status_map = [
        self::ORDER_STATUS_UNCONFIRMED => '未确认',
        self::ORDER_STATUS_CONFIRMED => '已确认',
        self::ORDER_STATUS_CANCELED => '已取消',
        self::ORDER_STATUS_INVALID => '无效',
        self::ORDER_STATUS_RETURNED => '退货中',
        self::ORDER_STATUS_SPLITED => '已分单',
        self::ORDER_STATUS_SPLITING_PART => '部分分单',
        self::ORDER_STATUS_ASK_4_REFUND => '申请退款',
        self::ORDER_STATUS_ASK_4_RETURN => '申请退货',
        self::ORDER_STATUS_RETURNED_DONE => '退货完成',
        self::ORDER_STATUS_REFUNDED_DONE => '退款完成',
        self::ORDER_STATUS_DONE => '已完成',
        self::ORDER_STATUS_REALLY_DONE => '真实已完成（不允许退货）',
        self::ORDER_STATUS_AGREE_RETURN => '同意退货',
    ];

    //  配送状态
    public static $shipping_status_map = [
        self::SHIPPING_STATUS_UNSHIPPED => '未发货',
        self::SHIPPING_STATUS_SHIPPED => '已发货',
        self::SHIPPING_STATUS_RECEIVED => '已收货',
        self::SHIPPING_STATUS_PREPARING => '备货中',
        self::SHIPPING_STATUS_SHIPPED_PART => '已发货(部分商品)',
        self::SHIPPING_STATUS_SHIPPED_ING => '发货中(处理发货单)',   //  发货后发现发货单填错，取消发货，进入发货中
        self::ORDER_STATUS_SHIPPED_PART => '已发货(部分商品)',
    ];

    //  支付状态
    public static $pay_status_map = [
        self::PAY_STATUS_UNPAYED => '未付款',
        self::PAY_STATUS_PAYING => '付款中',
        self::PAY_STATUS_PAYED => '已付款',
        self::PAY_STATUS_REFUND => '已退款',
    ];

    //  综合状态的名称(无样式)
    public static $order_cs_status_map_no_style = [
        self::ORDER_CS_STATUS_CANCELED => '已取消',
        self::ORDER_CS_STATUS_TO_BE_PAID => '待支付',
        self::ORDER_CS_STATUS_TO_BE_SHIPPED => '待发货',
        self::ORDER_CS_STATUS_TO_BE_REFUNDED => '退款中',
        self::ORDER_CS_STATUS_REFUNDED_DONE => '退款完成',
        self::ORDER_CS_STATUS_SHIPPED => '已发货',
        self::ORDER_CS_STATUS_COMPLETED => '已收货',
        self::ORDER_CS_STATUS_ASK_4_RETURN => '申请退货',
        self::ORDER_CS_STATUS_TO_BE_RETURNED => '待退货',
        self::ORDER_CS_STATUS_RETURNED => '退货中', //  用户已退货（显示快递信息）
        self::ORDER_CS_STATUS_RETURNED_DONE => '退货完成',
        self::ORDER_CS_STATUS_COMPLETED_OVER => '已完成',  //  已完结（可分成）
        self::ORDER_CS_STATUS_SHIPPED_PART => '部分发货',
    ];

    //  综合状态的名称
    public static $order_cs_status_map = [
        self::ORDER_CS_STATUS_CANCELED => '<span class="label">已取消</span>',
        self::ORDER_CS_STATUS_TO_BE_PAID => '<span class="label">待支付</span>',
        self::ORDER_CS_STATUS_TO_BE_SHIPPED => '<span class="label label-danger">待发货</span>',
        self::ORDER_CS_STATUS_TO_BE_REFUNDED => '<span class="label">退款中</span>',
        self::ORDER_CS_STATUS_REFUNDED_DONE => '<span class="label">退款完成</span>',
        self::ORDER_CS_STATUS_SHIPPED => '<span class="label label-success">已发货</span>',
        self::ORDER_CS_STATUS_COMPLETED => '<span class="label label-primary">已收货</span>',
        self::ORDER_CS_STATUS_TO_BE_RETURNED => '<span class="label label-warning">待退货</span>',
        self::ORDER_CS_STATUS_RETURNED => '<span class="label label-warning">退货中</span>', //  用户已退货（显示快递信息）
        self::ORDER_CS_STATUS_RETURNED_DONE => '<span class="label label-primary">退货完成</span>',
        self::ORDER_CS_STATUS_COMPLETED_OVER => '<span class="label label-primary">已完成</span>',  //  已完结（可分成）
        self::ORDER_CS_STATUS_ASK_4_RETURN => '<span class="label label-warning">申请退货</span>',  //  已完结（可分成）
        self::ORDER_CS_STATUS_SHIPPED_PART => '<span class="label label-primary">部分发货</span>',  //  部分发货
    ];

    //  给品牌商显示的综合状态
    public static $order_cs_status_show4brand = [
        self::ORDER_CS_STATUS_TO_BE_SHIPPED,
        self::ORDER_CS_STATUS_SHIPPED,
        self::ORDER_CS_STATUS_COMPLETED,
        self::ORDER_CS_STATUS_TO_BE_RETURNED,
        self::ORDER_CS_STATUS_RETURNED , //  用户已退货（显示快递信息）
        self::ORDER_CS_STATUS_RETURNED_DONE,
        self::ORDER_CS_STATUS_COMPLETED_OVER,  //  已完结（可分成）
        self::ORDER_CS_STATUS_SHIPPED_PART,
    ];

    public static $extensionCodeMap = [
        self::EXTENSION_CODE_GENERAL            => '普通商品(购物车结算)',
        self::EXTENSION_CODE_GENERAL_BUY_NOW    => '普通商品(立即购买)',
        self::EXTENSION_CODE_GENERAL_BATCH      => 'SPU 批量购买',
        self::EXTENSION_CODE_GROUPBUY           => '团拼商品',
        self::EXTENSION_CODE_FLASHSALE          => '秒杀商品',
        self::EXTENSION_CODE_INTEGRAL           => '积分兑换',
        self::EXTENSION_CODE_GIFT_PKG           => '礼包活动',
    ];

    //  退换中的订单状态
    /*public static $returnRefundedMap = [
        self::ORDER_CS_STATUS_TO_BE_REFUNDED,
        self::ORDER_CS_STATUS_ASK_4_RETURN,
        self::ORDER_CS_STATUS_TO_BE_RETURNED,
        self::ORDER_CS_STATUS_RETURNED
    ];*/

    //  综合状态 对应的状态数组
    public static $order_cs_status = [
        self::ORDER_CS_STATUS_CANCELED => [
            'order_status' =>  [self::ORDER_STATUS_CANCELED],
            'shipping_status' => [self::SHIPPING_STATUS_UNSHIPPED],
            'pay_status' => [self::PAY_STATUS_UNPAYED],
        ],
        self::ORDER_CS_STATUS_TO_BE_PAID => [
            'order_status' => [self::ORDER_STATUS_UNCONFIRMED, self::ORDER_STATUS_CONFIRMED],
            'shipping_status' => [self::SHIPPING_STATUS_UNSHIPPED],
            'pay_status' => [self::PAY_STATUS_UNPAYED],
        ],
        self::ORDER_CS_STATUS_TO_BE_SHIPPED => [
            'order_status' => [
                self::ORDER_STATUS_SPLITED,
                self::ORDER_STATUS_CONFIRMED
            ],
            'shipping_status' => [
                self::SHIPPING_STATUS_UNSHIPPED,    //  未发货 可能不在待发货状态中，生成发货单后才能发货，此事状态是备货中
                self::SHIPPING_STATUS_PREPARING,
                self::SHIPPING_STATUS_SHIPPED_ING,
            ],
            'pay_status' => [self::PAY_STATUS_PAYED],
        ],
        self::ORDER_CS_STATUS_SHIPPED_PART => [
            'order_status' => [self::ORDER_STATUS_SPLITED],
            'shipping_status' => [self::SHIPPING_STATUS_SHIPPED_PART],
            'pay_status' => [self::PAY_STATUS_PAYED],
        ],
        self::ORDER_CS_STATUS_TO_BE_REFUNDED => [
            'order_status' => [self::ORDER_STATUS_ASK_4_REFUND],
            'shipping_status' => [self::SHIPPING_STATUS_UNSHIPPED],
            'pay_status' => [self::PAY_STATUS_PAYED],
        ],
        self::ORDER_CS_STATUS_REFUNDED_DONE => [
            'order_status' => [self::ORDER_STATUS_REFUNDED_DONE],
            'shipping_status' => [self::SHIPPING_STATUS_UNSHIPPED],
            'pay_status' => [self::PAY_STATUS_PAYED],
        ],
        self::ORDER_CS_STATUS_SHIPPED => [
            'order_status' => [self::ORDER_STATUS_SPLITED],
            'shipping_status' => [self::SHIPPING_STATUS_SHIPPED],
            'pay_status' => [self::PAY_STATUS_PAYED],
        ],
        self::ORDER_CS_STATUS_COMPLETED => [
            'order_status' => [
                self::ORDER_STATUS_SPLITED,
                self::ORDER_STATUS_DONE,
            ],
            'shipping_status' => [self::SHIPPING_STATUS_RECEIVED],
            'pay_status' => [self::PAY_STATUS_PAYED],
        ],
        self::ORDER_CS_STATUS_TO_BE_RETURNED => [
            'order_status' => [self::ORDER_STATUS_AGREE_RETURN],
            'shipping_status' => [
                self::SHIPPING_STATUS_SHIPPED_PART,
                self::SHIPPING_STATUS_SHIPPED,
                self::SHIPPING_STATUS_RECEIVED,
                self::SHIPPING_STATUS_SHIPPED_ING,
            ],
            'pay_status' => [self::PAY_STATUS_PAYED],
        ],
        self::ORDER_CS_STATUS_RETURNED => [
            'order_status' => [self::ORDER_STATUS_RETURNED],
            'shipping_status' => [
                self::SHIPPING_STATUS_SHIPPED,
                self::SHIPPING_STATUS_RECEIVED,
            ],
            'pay_status' => [self::PAY_STATUS_PAYED],
        ],
        self::ORDER_CS_STATUS_RETURNED_DONE => [
            'order_status' => [self::ORDER_STATUS_RETURNED_DONE],
            'shipping_status' => [self::SHIPPING_STATUS_RECEIVED],
            'pay_status' => [self::PAY_STATUS_REFUND],
        ],
        self::ORDER_CS_STATUS_COMPLETED_OVER => [
            'order_status' => [self::ORDER_STATUS_REALLY_DONE],
            'shipping_status' => [self::SHIPPING_STATUS_RECEIVED],
            'pay_status' => [self::PAY_STATUS_PAYED],
        ],
        self::ORDER_CS_STATUS_ASK_4_RETURN => [
            'order_status' => [self::ORDER_STATUS_ASK_4_RETURN],
            'shipping_status' => [
                self::SHIPPING_STATUS_SHIPPED,
                self::SHIPPING_STATUS_RECEIVED
            ],
            'pay_status' => [self::PAY_STATUS_PAYED],
        ]
    ];


    public $delivery_id;
    public $note;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_order_info';
    }

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            RecordOrderActionBehavior::className(),
            RecordOrderModifyActionBehavior::className(),
        ]); // TODO: Change the autogenerated stub
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'order_status', 'shipping_status', 'pay_status', 'country', 'province', 'city', 'district', 'shipping_id', 'pay_id', 'integral', 'from_ad', 'add_time', 'confirm_time', 'pay_time', 'shipping_time', 'recv_time', 'pack_id', 'card_id', 'bonus_id', 'extension_id', 'agency_id', 'is_separate', 'parent_id', 'mobile_pay', 'mobile_order', 'brand_id', 'supplier_user_id', 'offline', 'group_identity'], 'integer'],
            [['goods_amount', 'shipping_fee', 'insure_fee', 'pay_fee', 'pack_fee', 'card_fee', 'money_paid', 'surplus', 'integral_money', 'bonus', 'order_amount', 'tax', 'discount'], 'number'],
            [['agency_id', 'tax', 'discount', 'supplier_user_id'], 'required'],
            [['order_sn'], 'string', 'max' => 20],
            [['consignee', 'zipcode', 'tel', 'mobile', 'email', 'inv_type'], 'string', 'max' => 60],
            [['address', 'card_message', 'referer', 'invoice_no', 'to_buyer', 'pay_note'], 'string', 'max' => 255],
            [['best_time', 'sign_building', 'shipping_name', 'pay_name', 'how_oos', 'how_surplus', 'pack_name', 'card_name', 'inv_payee', 'inv_content'], 'string', 'max' => 120],
            [['extension_code'], 'string', 'max' => 30],
            [['postscript'], 'string'],
            [['inv_type', 'district'], 'default', 'value' => ''],
            [['order_sn'], 'unique'],
            [['group_id'], 'string', 'max' => 22],
            [['country'], 'default', 'value' => 1],
            [['province', 'city', 'district'], 'default', 'value' => 0],
            ['shipping_id', 'default', 'value' => 3],   //  默认到付
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'order_id' => '订单ID',
            'order_sn' => '订单编号',
            'user_id' => '用户ID',
            'order_status' => '订单状态',
            'shipping_status' => '配送状态',
            'pay_status' => '支付状态',
            'consignee' => '收货人',
            'country' => '国家',
            'province' => '省份',
            'city' => '城市',
            'district' => '区域',
            'address' => '详细地址',
            'zipcode' => '邮编',
            'tel' => '电话',
            'mobile' => '手机号',
            'email' => '电子邮件',
            'best_time' => '最佳送货时间',
            'sign_building' => '标志性建筑',
            'postscript' => '订单附言',
            'shipping_id' => '配送方式ID',
            'shipping_name' => '快递名称',
            'pay_id' => '支付方式ID',
            'pay_name' => '支付方式名称',
            'how_oos' => '缺货处理方式',
            'how_surplus' => '余额处理方式',
            'pack_name' => '包装名称',
            'card_name' => '贺卡名称',
            'card_message' => '贺卡内容',
            'inv_payee' => '发票抬头',
            'inv_content' => '发票内容',    //  用户页面选择,取值o_shop_config的code字段的值 为invoice_content的value
            'goods_amount' => '商品总价',
            'shipping_fee' => '配送费用',
            'insure_fee' => '保价费用',
            'pay_fee' => 'Pay Fee', //  支付费用,跟支付方式的配置相关,取值表o_payment
            'pack_fee' => '包装费用',
            'card_fee' => '贺卡费用',
            'money_paid' => '已付款金额',
            'surplus' => '用户可用余额',
            'integral' => '积分',
            'integral_money' => '使用积分金额',
            'bonus' => '使用红包金额',
            'order_amount' => '应付款金额',
            'from_ad' => '来源广告',
            'referer' => '订单来源页面',
            'add_time' => '下单时间',
            'confirm_time' => '订单确认时间',
            'pay_time' => '订单支付时间',
            'shipping_time' => '订单发货时间',
            'recv_time' => '确认收货时间',
            'pack_id' => '包装ID',
            'card_id' => '贺卡ID',
            'bonus_id' => '红包ID',
            'invoice_no' => '发票编号',
            'extension_code' => '购买类型',
            'extension_id' => 'extension_id',   //  通过活动购买的商品ID
            'to_buyer' => '商城留言',   //  商家给客户的留言,当该字段值时可以在订单查询看到
            'pay_note' => '付款备注',
            'agency_id' => '办事处 ID',
            'inv_type' => '发票类型',
            'tax' => '发票税额',
            'is_separate' => '是否已分成', //  0未分成或等待分成;1已分成;2取消分成
            'parent_id' => '推荐人 ID',
            'discount' => '折扣金额',
            'mobile_pay' => '是否微信支付',
            'mobile_order' => '是否微信下单',
            'brand_id' => '品牌',
            'supplier_user_id' => '品牌商',
            'group_id' => '总单号',  //  一次下单产生的订单拥有同一个group_id， 可用于做大订单的关联字段
            'offline' => '是否线下单',
        ];
    }

    /**
     * @inheritdoc
     * @return OrderInfoQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new OrderInfoQuery(get_called_class());
    }

    /**
     * 1:1 关联users表
     * @param $user_id
     * @return \yii\db\ActiveQuery
    Users model 创建好后启用
     * private function getUsers($user_id) {
     * return self::hasOne(Users::className(), ['user_id', 'user_id']);
     * }
     * */

    /**
     * 获取订单中的商品信息
     * @return \yii\db\ActiveQuery
     */
    public function getOrdergoods()
    {
        return $this->hasMany(OrderGoods::className(), ['order_id' => 'order_id']);
    }

    /**
     * 获取订单的用户信息
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasOne(Users::className(), ['user_id' => 'user_id']);
    }

    /**
     * 获取订单的组合状态
     *
     * @param $status_array = ['order_status' => int, 'shipping_status' => int, 'pay_status' => int]
     * @return mixed|string
     */
    public static function getOrderCsStatus($status_array)
    {
        //  实际显示给品牌商综合状态，其他状态显示为处理中
        $supplier_order_status_map = [
            self::ORDER_CS_STATUS_TO_BE_SHIPPED,    //  待发货
            self::ORDER_CS_STATUS_SHIPPED,          //  已发货
            self::ORDER_CS_STATUS_COMPLETED,        //  已确认收货
            self::ORDER_CS_STATUS_TO_BE_RETURNED,   //  待退货
            self::ORDER_CS_STATUS_RETURNED,         //  退货中     用户已退货（显示快递信息）
            self::ORDER_CS_STATUS_RETURNED_DONE,    //  退货完成
            self::ORDER_CS_STATUS_COMPLETED_OVER,   //  订单完成    已完结（可分成）
            self::ORDER_CS_STATUS_SHIPPED_PART,
        ];
        $order_cs_status_array = self::$order_cs_status;
        foreach ($supplier_order_status_map as $status) {
            if (
                in_array($status_array['order_status'], self::$order_cs_status[$status]['order_status']) &&
                in_array($status_array['shipping_status'], self::$order_cs_status[$status]['shipping_status']) &&
                in_array($status_array['pay_status'], self::$order_cs_status[$status]['pay_status'])
            ) {
                return self::$order_cs_status_map[$status];
            }
        }
        return '<span class="label label-primary">已付款</span>';   //  实际显示给品牌商的只有 待发货、待退货、已完成、退货完成;除此之外的订单状态统一显示为 ‘处理中’
    }

    /**
     * 获取订单的发货单号
     * @param $order_id
     * @return mixed
     */
    public static function getDeliverySn($order_id)
    {
        $do_tb_name = DeliveryOrder::tableName();
        $rs = self::find()->select($do_tb_name . '.delivery_id')
            ->leftJoin($do_tb_name, $do_tb_name . '.order_id = ' . self::tableName() . '.order_id')
            ->where([self::tableName() . '.order_id' => $order_id])
            ->one();

        return $rs->delivery_id;
    }

    /**
     * 获取订单的快递单号
     * @param $order_id
     * @return mixed
     */
    public static function getInvoiceNo($order_id)
    {
        $do_tb_name = DeliveryOrder::tableName();
        $rs = self::find()->select($do_tb_name . '.invoice_no')
            ->leftJoin($do_tb_name, $do_tb_name . '.order_id = ' . self::tableName() . '.order_id')
            ->where([self::tableName() . '.order_id' => $order_id])
            ->one();

        return $rs->invoice_no;
    }

    /**
     * 获取订单的组合状态值
     *
     * @param $status_array
     * @return int|string
     */
    public static function getOrderCsStatusNo($status_array)
    {
        $order_cs_status_array = self::$order_cs_status_map_no_style;

        foreach ($order_cs_status_array as $status => $name) {
            if (
                in_array($status_array['order_status'], self::$order_cs_status[$status]['order_status']) &&
                in_array($status_array['shipping_status'], self::$order_cs_status[$status]['shipping_status']) &&
                in_array($status_array['pay_status'], self::$order_cs_status[$status]['pay_status'])
            ) {
                return $status;
            }
        }
        return self::ORDER_CS_STATUS_CANCELED;
    }

    /**
     * 获取品牌信息
     * @return \yii\db\ActiveQuery
     */
    public function getBrand()
    {
        return $this->hasOne(Brand::className(), ['brand_id' => 'brand_id']);
    }

    /**
     * 获取品牌信息
     * @return \yii\db\ActiveQuery
     */
    public function getSupplierUser()
    {
        return $this->hasOne(Users::className(), ['user_id' => 'supplier_user_id']);
    }

    /**
     * 获取配送方式
     * @return \yii\db\ActiveQuery
     */
    public function getShipping() {
        return $this->hasOne(Shipping::className(), ['shipping_id' => 'shipping_id']);
    }

    /**
     * 获取操作日志
     * @return \yii\db\ActiveQuery
     */
    public function getOrderAction() {
        return $this->hasMany(OrderAction::className(), ['order_id' => 'order_id'])->orderBy([
            'log_time' => SORT_DESC,
        ]);
    }

    public function getWechatPayInfo() {
        return $this->hasOne(WechatPayInfo::className(), ['order_sn' => 'order_sn'])->orderBy([
            'pay_id' => SORT_DESC,
        ]);
    }

    public function getAlipayInfo() {
        return $this->hasOne(AlipayInfo::className(), ['order_sn' => 'order_sn'])->orderBy([
            'id' => SORT_DESC,
        ]);
    }

    public function getYinlianPayInfo() {
        return $this->hasOne(YinlianPayinfo::className(), ['order_sn' => 'order_sn'])->orderBy([
            'id' => SORT_DESC,
        ]);
    }

    public function getYeePayInfo() {
        return $this->hasOne(YeePayinfo::className(), [
            'order_sn' => 'order_sn',
        ])->orderBy([
            'id' => SORT_DESC,
        ]);
    }

    public function getPayLog() {
        return $this->hasOne(PayLog::className(), [
            'order_id' => 'order_id',
        ]);
    }

    public function getDeliveryOrder() {
        return $this->hasMany(DeliveryOrder::className(), ['order_id' => 'order_id']);
    }

    //重新计算订单应付款
    public function recalcOrderAmount() {
        $this->order_amount = $this->getTotalAmount()
            - $this->money_paid;
    }

    //重新计算商品金额，关联重算了订单应付款
    public function recalcGoodsAmount() {
        $goodsAmount = 0;
        foreach ($this->ordergoods as $ordergoods) {
            $goodsAmount += $ordergoods->goods_price * $ordergoods->goods_number;
        }
        $this->goods_amount = $goodsAmount;
        $this->recalcOrderAmount();
    }

    //订单总金额
    public function getTotalAmount() {
        $result = $this->goods_amount - $this->discount
            + $this->tax
            + $this->shipping_fee
            + $this->insure_fee
            + $this->pay_fee
            + $this->pack_fee
            + $this->card_fee;
        return $result;
    }

    public function getTotalFee() {
        $result = $this->goods_amount
            + $this->tax
            + $this->shipping_fee
            + $this->insure_fee
            + $this->pay_fee
            + $this->pack_fee
            + $this->card_fee;
        return $result;
    }

    /**
     * 获取分成记录
     * @return \yii\db\ActiveQuery
     */
    public function getServicerDivideRecord() {
        return $this->hasMany(ServicerDivideRecord::className(), ['order_id' => 'order_id']);
    }

    public function getProvinceRegion() {
        return $this->hasOne(Region::className(), [
            'region_id' => 'province',
        ]);
    }

    public function getCityRegion() {
        return $this->hasOne(Region::className(), [
            'region_id' => 'city',
        ]);
    }

    /**
     * 获取订单中商品的总数量
     * @return int
     */
    public function getOrderGoodsCount() {
        $result = 0;
        if (empty($this->ordergoods)) {
            return $result;
        }
        foreach ($this->ordergoods as $orderGoods) {
            $result += $orderGoods->goods_number;
        }
        return $result;
    }

    /**
     * 获取订单中已发货商品的总数量
     * @return int
     */
    public function getDeliveryGoodsCount() {
        $result = 0;
        if (!empty($this->deliveryOrder)) {
            foreach ($this->deliveryOrder as $deliveryOrder) {
                foreach ($deliveryOrder->deliveryGoods as $goods) {
                    $result += $goods->send_number;
                }
            }
        }

        return $result;
    }


    /**
     * 是否所有货都发了
     * @return bool
     */
    public function isAllGoodsShipped() {

        Yii::warning('判断是否所有货都发完了 orderId = '. $this->order_id, __METHOD__);

        $deliveryOrderList = $this->deliveryOrder;
        $orderGoodsList = $this->ordergoods;

        //统计已发货的商品的数量
        $totalSendNumber = 0;
        foreach ($deliveryOrderList as $deliveryOrder) {
            foreach ($deliveryOrder->deliveryGoods as $deliveryGoods) {
                $totalSendNumber += $deliveryGoods->send_number;
            }
        }

        Yii::warning('totalSendNumber = '. $totalSendNumber, __METHOD__);

        $totalOrderGoodsCount = 0;
        //判断所有货是否都发了
        foreach ($orderGoodsList as $orderGoods) {
            $totalOrderGoodsCount += $orderGoods->goods_number;
        }

        return $totalSendNumber === $totalOrderGoodsCount;
    }

    public function getTotalDivideAmount() {
        $totalDivide = 0.0;
        //遍历商品列表，计算总分成金额
        $orderGoodsList = $this->ordergoods;
        foreach ($orderGoodsList as $orderGoods) {
            $goodsDivide = $orderGoods->getTotalDivideAmount();
            $totalDivide += $goodsDivide;
        }

        return $totalDivide;
    }

    public function getAlreadyTotalDivideAmount() {
        $amount=0;
        foreach($this->servicerDivideRecord as $value) {
            $amount += $value->divide_amount + $value->parent_divide_amount;
        }
        return $amount;
    }

    public function getAlreadyDivideAmount() {
        $amount=0;
        foreach($this->servicerDivideRecord as $value) {
            $amount += $value->divide_amount;
        }
        return $amount;
    }

    public function isCanceled() {
        if (($this->order_status == OrderInfo::ORDER_STATUS_CANCELED || $this->order_status == OrderInfo::ORDER_STATUS_INVALID)
            && $this->pay_status == OrderInfo::PAY_STATUS_UNPAYED
            && $this->shipping_status == OrderInfo::SHIPPING_STATUS_UNSHIPPED) {
            return true;
        }
        return false;
    }

    /**
     * 判断订单是否在待支付状态
     * @return bool
     */
    public function isUnpay() {
        if (($this->order_status == OrderInfo::ORDER_STATUS_UNCONFIRMED || $this->order_status == OrderInfo::ORDER_STATUS_CONFIRMED) && $this->pay_status == OrderInfo::PAY_STATUS_UNPAYED && $this->shipping_status == OrderInfo::SHIPPING_STATUS_UNSHIPPED) {
            return true;
        }
        return false;
    }

    /**
     * 判断订单是否已付款状态
     * @return bool
     */
    public function isPaid() {
        if ($this->order_status == OrderInfo::ORDER_STATUS_CONFIRMED && $this->pay_status == OrderInfo::PAY_STATUS_PAYED && $this->shipping_status == OrderInfo::SHIPPING_STATUS_UNSHIPPED) {
            return true;
        }
        return false;
    }

    /**
     * 判断订单是否已完结状态
     * @return bool
     */
    public function isFinished() {
        //取消和无效状态的算已完结
        if (($this->order_status == OrderInfo::ORDER_STATUS_CANCELED || $this->order_status == OrderInfo::ORDER_STATUS_INVALID) && $this->pay_status == OrderInfo::PAY_STATUS_UNPAYED && $this->shipping_status == OrderInfo::SHIPPING_STATUS_UNSHIPPED) {
            return true;
        }
        //退款或退货完成也是完结状态
        if ($this->order_status == OrderInfo::ORDER_STATUS_RETURNED_DONE || $this->order_status == OrderInfo::ORDER_STATUS_REFUNDED_DONE) {
            return true;
        }
        //还有就是真实完成的状态
        if ($this->order_status == OrderInfo::ORDER_STATUS_REALLY_DONE && $this->pay_status == OrderInfo::PAY_STATUS_PAYED && $this->shipping_status == OrderInfo::SHIPPING_STATUS_RECEIVED) {
            return true;
        }
        return false;
    }

    /**
     * 判断订单是否退款/退货完成
     * @return bool
     */
    public function isReturnOrRefund() {
        if ($this->order_status == OrderInfo::ORDER_STATUS_REFUNDED_DONE || $this->order_status == OrderInfo::ORDER_STATUS_RETURNED_DONE) {
            return true;
        }
        return false;
    }

    /**
     * 子单是否已经发货
     * @return bool
     */
    public function isShipped() {
        if ($this->order_status == OrderInfo::ORDER_STATUS_SPLITED && $this->pay_status = OrderInfo::PAY_STATUS_PAYED && $this->shipping_status == OrderInfo::SHIPPING_STATUS_SHIPPED) {
            return true;
        }
        return false;
    }

    public function getOrderGroup() {
        return $this->hasOne(OrderGroup::className(), [
            'group_id' => 'group_id',
        ]);
    }

    public function getOrderGroupIdentity() {
        return $this->hasOne(OrderGroup::className(), [
            'id' => 'group_identity',
        ]);
    }

    /**
     * 获取分成成本
     * @return string
     */
    public function getCost() {
        $cost = 0;
        foreach ($this->ordergoods as $ordergoods) {
            if (!empty($ordergoods->goods)) {
                $goodsNumber = $ordergoods->send_number;
                if ($goodsNumber < 0) {
                    $goodsNumber = 0;
                }

                //没有成本价暂时考虑为0利润
                if (empty($ordergoods->goods->supplyInfo)) {
                    $supplyPrice = $ordergoods->goods->shop_price;
                }
                else {
                    $supplyPrice = $ordergoods->goods->supplyInfo->supply_price;
                }

                if ($supplyPrice > $ordergoods->goods->shop_price) {
                    $supplyPrice = $ordergoods->goods->shop_price;
                }
                $cost += $supplyPrice * $goodsNumber;
            }
        }
        return NumberHelper::price_format($cost);
    }

    public function getTotalGoodsAmount() {
        $result = 0;
        foreach ($this->ordergoods as $ordergoods) {
            if (!empty($ordergoods->goods)) {
                $goodsNumber = $ordergoods->send_number;
                if ($goodsNumber < 0) {
                    $goodsNumber = 0;
                }
                $result += $ordergoods->goods_price * $goodsNumber;
            }
        }
        return NumberHelper::price_format($result);
    }

    /**
     * 取消订单
     * @param $note
     * @return bool
     */
    public function cancel($note) {
        if ($this->pay_status != self::PAY_STATUS_UNPAYED || $this->shipping_status != self::SHIPPING_STATUS_UNSHIPPED) {
            Yii::warning('不是未付款状态的订单不允许取消', __METHOD__);
            return false;
        }
        $this->note = $note;
        $this->order_status = self::ORDER_STATUS_CANCELED;
        $this->pay_status = self::PAY_STATUS_UNPAYED;
        $this->shipping_status = self::SHIPPING_STATUS_UNSHIPPED;
        $this->save(false);

        $orderGroup = $this->orderGroup;
        if (!empty($orderGroup)) {
            $orderGroup->setupOrderStatus();
            $orderGroup->save();
        }
        return true;
    }

    /**
     * 订单支付
     * @param $note
     * @return bool
     */
    public function pay($note) {
        //检查当前状态是否可以改为已付款
        if ($this->order_status != OrderInfo::ORDER_STATUS_UNCONFIRMED
            && $this->order_status != OrderInfo::ORDER_STATUS_CONFIRMED
        ) {
            $this->flashErrorMessage('只有未确认订单才可以修改为取消状态');
            Yii::warning('只有未确认订单才可以修改为取消状态', __METHOD__);
            return false;
        }

        $alreadyPaid = $this->pay_status == OrderInfo::PAY_STATUS_PAYED;

        $this->note = $note;
        $this->order_status = OrderInfo::ORDER_STATUS_CONFIRMED;
        $this->pay_status = OrderInfo::PAY_STATUS_PAYED;
        $this->pay_time = DateTimeHelper::getFormatGMTTimesTimestamp();
        $this->shipping_status = OrderInfo::SHIPPING_STATUS_UNSHIPPED;

        $this->money_paid = $this['money_paid'] + $this['order_amount'];
        $this->order_amount = 0;
        $this->pay_id = PaymentHelper::PAY_ID_BACKEND;
        $this->pay_name = PaymentHelper::$paymentMap[$this->pay_id];

        $payLog = $this->payLog;
        if (!empty($payLog)) {
            $payLog->is_paid = 1;
            $payLog->save();
        }

        //先保存日志，这样可以保证每个操作都是有日志的
        if ($this->save()) {

            $orderGroup = $this->orderGroup;
            if (!empty($orderGroup)) {
                $orderGroup->setupOrderStatus();
                $orderGroup->syncFeeInfo();
                $orderGroup->syncTimeInfo();
                if (!$orderGroup->save()) {
                    $this->flashError($orderGroup);
                    return false;
                }
            }

            //如果是支付减库存就在这里处理
            if (!$alreadyPaid) {
                $config = CacheHelper::getShopConfigParams(['use_storage', 'stock_dec_time']);
                if ($config['use_storage']['value'] == '1' && $config['stock_dec_time']['value'] == ShopConfig::SDT_PAID) {
                    foreach ($this->ordergoods as $goods) {
                        $goods->goods->goods_number -= $goods->goods_number;
                        if ($goods->goods->goods_number < 0) {
                            $goods->goods->goods_number = 0;
                        }
                        if (!$goods->goods->save()) {
                            $this->flashError($goods->goods);
                        }
                    }
                }
            }

            //给用户分成积分
            if ($this->extension_code != 'integral_exchange' && $this->extension_code != 'group_buy') {
                $integral = floor(($this['goods_amount'] - $this['discount']) / 10);
                $time = DateTimeHelper::gmtime();
                $integralModel = new Integral();

                $integralModel['integral'] = $integral;
                $integralModel['user_id'] = $this['user_id'];
                $integralModel['pay_code'] = 'backend';
                $integralModel['out_trade_no'] = $note;
                $integralModel['note'] = $this['order_id'];
                $integralModel['status'] = 0;
                $integralModel['created_at'] = $time;
                $integralModel['updated_at'] = $time;

                if (!$integralModel->save()) {
                    $this->flashError($integralModel);
                    return false;
                }
            }
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * 快捷发货
     * @param $note
     * @param $invoiceNo
     * @return bool
     */
    public function shipping($note, $invoiceNo, $shippingFee = 0) {
        //已确认，已分单，并且已经付款可以操作发货
        if (($this->order_status != self::ORDER_STATUS_CONFIRMED
            && $this->order_status != self::ORDER_STATUS_SPLITED)
        ) {
            $msg = '订单状态不是已确认或者已分单';
            Yii::error($msg, __METHOD__);
            $this->flashErrorMessage($msg);
            return false;
        }

        //未付款订单不允许操作发货
        if ($this->pay_status != self::PAY_STATUS_PAYED) {
            $msg = '支付状态不是已付款';
            Yii::error($msg, __METHOD__);
            $this->flashErrorMessage($msg);
            return false;
        }

        //已发货的订单不允许重复发货
        if ($this->shipping_status == self::SHIPPING_STATUS_SHIPPED
            || $this->shipping_status == self::SHIPPING_STATUS_SHIPPED_PART) {
            $msg = '发货状态是已发货或者部分发货，不允许使用快速发货';
            Yii::error($msg, __METHOD__);
            $this->flashErrorMessage($msg);
            return false;
        }

        $deliveryOrder = DeliveryOrder::createShippedDeliveryOrderFromOrderInfo($this);
        $deliveryOrder->invoice_no = $this->invoice_no = $invoiceNo;
        $deliveryOrder->group_id = $this->group_id;
        $deliveryOrder->shipping_fee = $shippingFee;

        //保存发货单
        if ($deliveryOrder->save()) {

            $this->note = $note;
            $this->order_status = self::ORDER_STATUS_SPLITED;
            $this->shipping_status = self::SHIPPING_STATUS_SHIPPED;
            $this->shipping_time = DateTimeHelper::getFormatGMTTimesTimestamp();
            //修改订单状态
            if ($this->save()) {

                $orderGroup = $this->orderGroup;
                if (!empty($orderGroup)) {
                    $orderGroup->setupOrderStatus();
                    $orderGroup->syncTimeInfo();
                    if (!$orderGroup->save()) {
                        $this->flashError($orderGroup);
                        return false;
                    }
                }

                if (empty($deliveryOrder->deliveryGoods)) {
                    foreach ($this->ordergoods as $goods) {
                        $deliveryGoods = new DeliveryGoods();
                        $deliveryGoods->delivery_id = $deliveryOrder->delivery_id;
                        $deliveryGoods->goods_id = $goods->goods_id;
                        $deliveryGoods->product_id = 0;
                        $deliveryGoods->goods_name = $goods->goods_name;
                        $deliveryGoods->brand_name = empty($goods->goods->brand->brand_name) ? '' : $goods->goods->brand->brand_name;
                        $deliveryGoods->goods_sn = $goods->goods_sn;
                        $deliveryGoods->is_real = $goods->is_real;
                        $deliveryGoods->extension_code = $goods->extension_code;
                        $deliveryGoods->parent_id = $goods->parent_id;
                        $deliveryGoods->send_number = ($goods->goods_number - $goods->back_number) > 0 ? ($goods->goods_number - $goods->back_number) : 0;
                        $deliveryGoods->goods_attr = $goods->goods_attr;
                        $deliveryGoods->goods_price = $goods->goods_price;
                        $deliveryGoods->order_goods_rec_id = $goods->rec_id;

                        $goods->send_number = $goods->goods_number;
                        $goods->save(false);

                        try {
                            $deliveryOrder->link('deliveryGoods', $deliveryGoods);
                            $deliveryGoods->link('orderGoods', $goods);
                        } catch (Exception $e) {
                            Yii::error('e = ', VarDumper::export($e), __METHOD__);
                            $this->flashError($deliveryGoods);
                            return false;
                        }
                    }
                }
                return true;
            }
            else {
                $this->flashError($this);
                Yii::error('操作失败1，'. OrderInfo::className(). ', '. VarDumper::export($this->errors));
                return false;
            }
        }
        else {
            $this->flashError($deliveryOrder);
            Yii::error('操作失败2，'. OrderInfo::className(). ', '. VarDumper::export($deliveryOrder->errors));
            return false;
        }
    }

    public function advanceShipping($orderGoodsList, $shippingInfo, $shippingFee) {

        if (($this->order_status == OrderInfo::ORDER_STATUS_CONFIRMED
                && $this->pay_status == OrderInfo::PAY_STATUS_PAYED
                && $this->shipping_status == OrderInfo::SHIPPING_STATUS_UNSHIPPED)
            ||
            ($this->order_status == OrderInfo::ORDER_STATUS_SPLITED
                && $this->pay_status == OrderInfo::PAY_STATUS_PAYED
                && $this->shipping_status == OrderInfo::SHIPPING_STATUS_SHIPPED_PART
            )
        ) {
            if (empty($orderGoodsList)) {
                Yii::error('缺少发货数量', __METHOD__);
                $this->flashErrorMessage('缺少发货数量');
                return false;
            }

            if (empty($shippingInfo)) {
                Yii::error('缺少物流信息', __METHOD__);
                $this->flashErrorMessage('缺少物流信息');
                return false;
            }

            $orderGoods = OrderGoods::find()->where([
                'rec_id' => array_keys($orderGoodsList),
            ])->andWhere([
                'order_id' => $this->order_id,
            ])->all();

            //总发货数量
            $totalCount = 0;
            foreach ($orderGoods as $k => $goods) {
                $shippingNum = $orderGoodsList[$goods->rec_id];
                if ($goods->goods_number - $goods->send_number < $shippingNum || $shippingNum < 0) {
                    $msg = '订单发货失败 '. $goods['goods_name']. ' 发货数量错误，待发货数量为：'. ($goods->goods_number - $goods->send_number). ', 这次发货数量为'. $shippingNum;
                    Yii::error($msg, __METHOD__);
                    $this->flashErrorMessage($msg);
                    return false;
                }
                $totalCount += $shippingNum;
            }

            if ($totalCount <= 0) {
                $msg = '发货数量为0，请检查发货数量是否填写有误';
                Yii::error($msg, __METHOD__);
                $this->flashErrorMessage($msg);
                return false;
            }

            $deliveryOrder = DeliveryOrder::createShippedDeliveryOrderFromOrderInfo($this);
            $deliveryOrder->invoice_no = $shippingInfo;
            $deliveryOrder->group_id = $this->group_id;
            $deliveryOrder->shipping_fee = $shippingFee;

            $transaction = DeliveryOrder::getDb()->beginTransaction();
            try {
                $this->link('deliveryOrder', $deliveryOrder);
                foreach($orderGoods as $goods) {
                    $shippingNum = $orderGoodsList[$goods->rec_id];
                    if ($shippingNum > 0) {
                        $deliveryGoods = new DeliveryGoods();
                        $deliveryGoods->goods_id = $goods->goods_id;
                        $deliveryGoods->goods_name = $goods->goods_name;
                        $deliveryGoods->send_number = $shippingNum;
                        $deliveryGoods->brand_name = $goods->goods->brand->brand_name ?: '';
                        $deliveryGoods->goods_sn = $goods->goods_sn;
                        $deliveryGoods->is_real = 1;
                        $deliveryGoods->extension_code = $goods->extension_code;
                        $deliveryGoods->parent_id = 0;
                        $deliveryGoods->goods_price = $goods->goods_price;
                        $deliveryGoods->order_goods_rec_id = $goods->rec_id;

                        $deliveryOrder->link('deliveryGoods', $deliveryGoods);
                        $deliveryGoods->link('orderGoods', $goods);

                        $goods->send_number += $shippingNum;
                        if ($goods->send_number > $goods->goods_number) {
                            Yii::error('发货数量超过了商品数量 rec_id = '. $goods->rec_id, __METHOD__);
                            $goods->send_number = $goods->goods_number;
                        }
                        $goods->save(false);
                    }
                }

                $transaction->commit();

                $this->order_status = OrderInfo::ORDER_STATUS_SPLITED;

                //如果全部商品都发货了，就把shipping_status改为已发货，否则改为部分发货
                if ($this->isAllGoodsShipped()) {
                    $this->shipping_status = self::SHIPPING_STATUS_SHIPPED;
                    $this->shipping_time = DateTimeHelper::getFormatGMTTimesTimestamp();
                }
                else {
                    $this->shipping_status = self::SHIPPING_STATUS_SHIPPED_PART;
                }

                if (!$this->save()) {
                    $msg = '订单修改状态失败 orderId = '. $this->order_id. ', orderSn = '. $this->order_sn;
                    Yii::error($msg, __METHOD__);
                    $this->flashErrorMessage($msg);
                    return false;
                }

                $orderGroup = $this->orderGroup;
                if (!empty($orderGroup)) {
                    $orderGroup->setupOrderStatus();
                    $orderGroup->syncTimeInfo();
                    $orderGroup->save();
                }

                $this->flashSuccess('本次发货成功');
                return true;
            } catch(\Exception $e) {
                $transaction->rollBack();
            } catch(\Throwable $e) {
                $transaction->rollBack();
            }
            return false;
        }
        else {
            $this->flashErrorMessage('只有未发货和部分发货的订单才允许自定义发货');
            return false;
        }
    }

    public function shipped($note) {

        //未付款的话直接取消
        if (($this->order_status == self::ORDER_STATUS_CONFIRMED || $this->order_status == self::ORDER_STATUS_UNCONFIRMED)
            && $this->pay_status == self::PAY_STATUS_UNPAYED
            && $this->shipping_status == self::SHIPPING_STATUS_UNSHIPPED) {
            $this->cancel($note);
            return;
        }

        //已付款的全部改成已发货
        if (($this->order_status == self::ORDER_STATUS_CONFIRMED || $this->order_status == self::ORDER_STATUS_SPLITED)
            && $this->pay_status == self::PAY_STATUS_PAYED
            && $this->shipping_status != self::SHIPPING_STATUS_RECEIVED
        ) {
            $this->note = $note;
            $this->order_status = self::ORDER_STATUS_SPLITED;
            $this->shipping_status = self::SHIPPING_STATUS_SHIPPED;
            $this->shipping_time = DateTimeHelper::getFormatGMTTimesTimestamp();

            if (!$this->save()) {
                $this->flashError($this);
            }

            $orderGroup = $this->orderGroup;
            if (!empty($orderGroup)) {
                $orderGroup->setupOrderStatus();
                $orderGroup->syncTimeInfo();
                $orderGroup->save();
            }
            return;
        }

        Yii::warning('只有部分发货的订单才能直接转为已发货状态', __METHOD__);
        $this->flashErrorMessage('只有部分发货的订单才能直接转为已发货状态');
    }

    public function back() {

    }

    public function flashSuccess($message) {
        Yii::$app->session->setFlash('success', $message);
    }

    public function flashError($model) {
        Yii::$app->session->setFlash('error', '操作失败，请截图并联系技术处理 '. get_class($model). ', e = '. VarDumper::export($model->errors));
    }

    public function flashErrorMessage($msg) {
        Yii::$app->session->setFlash('error', $msg);
    }

    public static function createFromOrderGroup($orderGroup, $date = null) {
        $orderInfo = new OrderInfo();
        $orderInfo->user_id = $orderGroup->user_id;
        $orderInfo->order_sn = OrderGroupHelper::getUniqueOrderSn($date);
        $orderInfo->order_status = 0;
        $orderInfo->pay_status = 0;
        $orderInfo->shipping_status = 0;
        $orderInfo->consignee = $orderGroup->consignee;
        $orderInfo->country = $orderGroup->country;
        $orderInfo->province = $orderGroup->province;
        $orderInfo->city = $orderGroup->city;
        $orderInfo->district = $orderGroup->district;
        $orderInfo->address = $orderGroup->address;
        $orderInfo->mobile = $orderGroup->mobile;
        $orderInfo->add_time = DateTimeHelper::getFormatGMTTimesTimestamp();
        $orderInfo->group_id = $orderGroup->group_id;
        $orderInfo->agency_id = 0;
        $orderInfo->tax = 0;
        $orderInfo->discount = 0;
        $orderInfo->money_paid = 0;
        $orderInfo->offline = $orderGroup->offline;
        $orderInfo->supplier_user_id = 0;
        $orderInfo->brand_id = 0;
        $orderInfo->inv_type = '';
        $orderInfo->group_identity = $orderGroup->id;
        return $orderInfo;
    }

    public function refund($note) {
        if ($this->order_status >= OrderInfo::ORDER_STATUS_CONFIRMED
            && $this->pay_status == OrderInfo::PAY_STATUS_PAYED
            ) {

            $backOrder = BackOrder::createByOrderInfo($this);
            $backOrder->reason = $note;
            $backOrder->save();

            foreach ($this->ordergoods as $ordergoods) {
                $backNumber = $ordergoods->goods_number - $ordergoods->send_number;
                if ($backNumber < 0) {
                    $backNumber = 0;
                }
                if ($backNumber > 0) {
                    $ordergoods->back_number = $backNumber;
                    $backGoods = BackGoods::createFromOrderGoods($ordergoods);
                    $backGoods->send_number = $backNumber;
                    $backOrder->link('backGoods', $backGoods);
                }
            }
        }
    }

    public function allRefund() {
        if (empty($this->ordergoods)) {
            return false;
        }
        $allRefund = true;
        foreach ($this->ordergoods as $ordergoods) {
            if (!$ordergoods->allRefund()) {
                $allRefund = false;
            }
        }
        return $allRefund;
    }

    /**
     * 判断总单是否是 团采/秒杀 活动的订单
     *
     * 如果是秒杀活动
     *      订单已超时未支付，系统自动取消订单
     *      订单在可支付时段内，返回订单有效期
     * @return array
     */
    public static function checkOrderExtension($orders)
    {
        if (count($orders) == 1) {
            if (!empty($orders[0])) {
                $orderItem = $orders[0];
                if ($orderItem['extension_code'] == 'flash_sale') {
                    Yii::warning(' 秒杀活动，判定是否可以支付 ');
                    //  秒杀活动的待支付订单，验证订单过期时间，如果超出，置为取消;如果未超出，返回 [group_id => $expiredTime]
                    $goodsActivity = GoodsActivity::find()
                        ->select(['order_expired_time'])
                        ->where([
                            'act_id' => $orderItem['extension_id'],
                        ])->one();

                    if (!empty($goodsActivity)) {
                        $add_time = DateTimeHelper::getFormatGMTTimesTimestamp($orderItem['add_time']);
                        $expiredTime = $add_time + $goodsActivity->order_expired_time;
                        $gmtNow = DateTimeHelper::getFormatGMTTimesTimestamp();
                        Yii::warning(' 订单过期时间(GMT) $expiredTime = '.$expiredTime.
                            ' ; 订单创建时间(GMT) $orderItem.add_time = '.$orderItem['add_time'].' —— $add_time = '.$add_time.
                            ' ; 订单有效时间 order_expired_time = '.$goodsActivity->order_expired_time.
                            ' ; 当前时间(GMT) $gmtNow = '.$gmtNow
                        );
                        if ($gmtNow >= $expiredTime) {
                            Yii::warning(' 取消订单 group_id = '.$orderItem['group_id']);
                            $orderGroup = OrderGroup::find()->where(['group_id' => $orderItem['group_id']])->one();
                            $orderGroup->cancel('订单未在有效时段内支付，系统自动取消');
                        } else {
                            $expiredCnTime = DateTimeHelper::getFormatCNDateTime($expiredTime);
                            return [$orderItem['group_id'] => $expiredCnTime];
                        }
                    }
                }
            }
        }

        return [];
    }

    public function getIntegral()
    {
        return $this->hasOne(Integral::className(), ['note' => 'integral']);
    }

    public function getGoodsActivity()
    {
        return $this->hasOne(GoodsActivity::className(), ['act_id' => 'extension_id']);
    }

    public function updateShippingInfo($shippingId, $shippingName, $shippingFee) {
        $this->shipping_id = $shippingId;
        $this->shipping_name = $shippingName;
        $this->shipping_fee = $shippingFee;

        $this->recalcOrderAmount();

        if (!$this->save()) {
            return false;
        }

        $orderGroup = $this->orderGroup;
        $orderGroup->syncFeeInfo();
        if (!$orderGroup->save()) {
            return false;
        }

        return true;
    }

    public function getPayActionList() {
        if (empty($this->orderAction)) {
            return null;
        }
        $orderActions = $this->orderAction;
        usort($orderActions, function ($a, $b) {
            return $a['action_id'] < $b['action_id'];
        });
        $result = [];
        foreach ($this->orderAction as $action) {
            if ($action['order_status'] == OrderInfo::ORDER_STATUS_CONFIRMED
                && $action['pay_status'] == OrderInfo::PAY_STATUS_PAYED
                && $action['shipping_status'] == OrderInfo::SHIPPING_STATUS_UNSHIPPED) {
                $result[] = $action;
            }
        }
        return $result;
    }

    public function getPayNote() {
        $payActionList = $this->getPayActionList();
        if (empty($payActionList)) {
            return '';
        }
        $result = '';
        foreach ($payActionList as $action) {
            $result .= $action['action_note']. '|';
        }
        if (empty($result)) {
            return '';
        }
        return substr($result, 0, -1);
    }
}
