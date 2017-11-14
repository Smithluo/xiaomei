<?php

namespace common\models;

use common\helper\ImageHelper;
use common\helper\NumberHelper;
use Yii;
use yii\db\Query;

/**
 * This is the model class for table "o_order_goods".
 *
 * @property string $rec_id
 * @property string $order_id
 * @property string $goods_id
 * @property string $goods_name
 * @property string $goods_sn
 * @property string $product_id
 * @property integer $goods_number
 * @property string $market_price
 * @property string $goods_price
 * @property string $pay_price
 * @property string $goods_attr
 * @property integer $send_number
 * @property integer $back_number
 * @property integer $is_real
 * @property string $extension_code
 * @property string $parent_id
 * @property integer $is_gift
 * @property string $goods_attr_id
 * @property integer $event_id
 * @property string $sample
 */
class OrderGoods extends \yii\db\ActiveRecord
{
    const IS_GIFT_NO        = 0;
    const IS_GIFT_GIFT      = Event::EVENT_TYPE_FULL_GIFT;  //  1
    const IS_GIFT_WULIAO    = Event::EVENT_TYPE_WULIAO;     //  4

    public static $isGiftMap = [
        self::IS_GIFT_NO        => '商品',
        self::IS_GIFT_GIFT      => '赠品',
        self::IS_GIFT_WULIAO    => '物料',
    ];

    public static $isGiftStyleMap = [
        self::IS_GIFT_NO        => '商品',
        self::IS_GIFT_GIFT      => '<span class="text-success"><strong>赠品</strong></span>',
        self::IS_GIFT_WULIAO    => '<span class="text-warning"><strong>物料</strong></span>',
    ];

    public $number;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_order_goods';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'order_id', 'goods_id', 'product_id', 'goods_number', 'send_number',
                    'is_real', 'parent_id', 'is_gift', 'event_id', 'back_number'
                ],
                'integer'
            ],
            [['market_price', 'goods_price', 'pay_price', 'is_gift'], 'number'],
//            [['goods_attr'], 'required'],     当前未启用SPU，允许为空
            [['goods_attr'], 'string'],
            [['goods_name'], 'string', 'max' => 120],
            [['goods_sn'], 'string', 'max' => 60],
            [['sample'], 'string', 'max' => 64],
            [['extension_code'], 'string', 'max' => 30],
            [['goods_attr_id'], 'string', 'max' => 255],
            ['event_id', 'default', 'value' => 0]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'rec_id' => '购物车ID',
            'order_id' => '订单ID',
            'goods_id' => '商品ID',
            'goods_name' => '商品名称',
            'goods_sn' => '货号',
            'product_id' => '货品ID',
            'goods_number' => '商品数量',
            'market_price' => '市场价格',
            'goods_price' => '实际售价',
            'pay_price' => '实际结算价', //  均摊 满减、红包、折扣 等优惠
            'goods_attr' => '商品属性',
            'send_number' => '发货数量',
            'is_real' => '是否是实际商品',
            'extension_code' => 'Extension Code',   //  商品的扩展属性,取自ecs_goods的extension_code
            'parent_id' => '父级ID',                 //  配件、子商品才有
            'is_gift' => '是否赠品',    //  是否赠品,0否; 1赠品; 2物料; 其他:未定义
            'goods_attr_id' => '商品属性ID',        //  取自goods_attr的goods_attr_id,
            'event_id' => '结算时参与的活动',        //  取自goods_attr的goods_attr_id,
            'sample' => '小样配比',
        ];
    }

    /**
     * @inheritdoc
     * @return OrderGoodsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new OrderGoodsQuery(get_called_class());
    }

    /**
     * 获取品牌商旗下品牌对应的所有订单列表
     * @param $brand_list
     * @return array|OrderGoods[]
     */
    public static function getOrderIdList($brand_list)
    {
        $rs = self::find()->select('o_order_goods.order_id')->leftJoin('o_goods', 'o_goods.goods_id = o_order_goods.goods_id')
            ->where([
                'o_goods.brand_id' => $brand_list
            ])->distinct()->asArray()->all();

        return array_column($rs, 'order_id');
    }

    /**
     * 1对1 获取订单商品所在的订单信息
     * @return \yii\db\ActiveQuery
     */
    public function getOrderInfo()
    {
        return $this->hasOne( OrderInfo::className(), ['order_id' => 'order_id']);
    }

    /**
     * 1对1 获取订单商品的详情
     * @return \yii\db\ActiveQuery
     */
    public function getGoods()
    {
        return $this->hasOne(Goods::className(), ['goods_id' => 'goods_id']);
    }

    /**
     * 获取团拼商品的活动信息
     * @return \yii\db\ActiveQuery
     */
    public function getGoodsActivity()
    {
        return $this->hasOne(GoodsActivity::className(), ['goods_id' => 'goods_id']);
    }

    /**
     * 获取订单中的商品在结算时参与的活动
     * @return \yii\db\ActiveQuery
     */
    public function getEvent()
    {
        return $this->hasOne(Event::className(), ['event_id' => 'event_id']);
    }

    public function getDeliveryGoods() {
        return $this->hasMany(DeliveryGoods::className(), ['order_goods_rec_id' => 'rec_id']);
    }

    /**
     * 获取订单商品的已发货的数量
     * @return int
     */
    public function getDeliveryCount() {

        if (empty($this->deliveryGoods)) {
            //获取所有发货单
            $orderInfo = $this->orderInfo;
            $deliveryOrderList = $orderInfo->deliveryOrder;

            //遍历发货单，得到自己这个商品的已发货数量
            $count = 0;
            foreach ($deliveryOrderList as $deliveryOrder) {
                foreach ($deliveryOrder->deliveryGoods as $goods) {
                    if ($goods->goods_id == $this->goods_id) {
                        $count += $goods->send_number;
                    }
                }
            }

            return $count;
        }
        else {
            $count = 0;
            foreach ($this->deliveryGoods as $deliveryGoods) {
                $count += $deliveryGoods->send_number;
            }
            return $count;
        }
    }

    /**
     * 计算商品应该分成多少
     * @return int|string
     */
    public function getTotalDivideAmount() {
        $orderInfo = $this->orderInfo;
        if (empty($orderInfo)) {
            return 0;
        }

        $discount = $orderInfo->discount;
        $totalAmount = $orderInfo->getTotalFee();

        //看订单有没有折扣，如果有折扣最终分成结果需要折下来
        if ($discount < 0.01) {
            $discountPercent = 1.0;
        }
        else {
            $offPercent = (float)$discount / (float)$totalAmount;
            $discountPercent = 1.0 - $offPercent;
        }

        //获取应该分成的百分比
        $strategyInfo = $this->goods->servicerStrategy ?: null;
        if (empty($strategyInfo)) {
            $strategyInfo = $this->goods->brand->servicerStrategy ?: null;
        }

        if (empty($strategyInfo)) {
            return 0;
        }

        $percentTotal = $strategyInfo->percent_total;
        //这里有千分之6的第三方支付的费用不算到提成里面
        $goodsTotalPrice = $this->goods_price * $this->goods_number * (1.0 - ShopConfig::getConfigValue('order_pay_fee'));

        //分成金额，这里要算上订单的折扣
        $goodsDivide = $goodsTotalPrice * ($percentTotal / 100.0) * $discountPercent;

        return $goodsDivide;
    }

    /**
     * 获取这个商品的发货物流单号
     * @return string
     */
    public function getShippingInfo() {
        $orderInfo = $this->orderInfo;
        if (empty($orderInfo)) {
            return '';
        }

        $result = '';
        foreach ($orderInfo->deliveryOrder as $deliveryOrder) {
            foreach ($deliveryOrder->deliveryGoods as $deliveryGood) {
                if ($deliveryGood->goods_id == $this->goods_id) {
                    $result .= $deliveryOrder->invoice_no. ',';
                    break;
                }
            }
        }

        if (empty($result)) {
            return $result;
        }

        return substr($result, 0, -1);
    }

    public static function createFromGoods($goods) {
        $orderGoods = new OrderGoods();
        $orderGoods->goods_id = $goods->goods_id;
        $orderGoods->goods_name = $goods->goods_name;
        $orderGoods->goods_sn = $goods->goods_sn;
        $orderGoods->market_price = $goods->market_price;
        $orderGoods->send_number = 0;
        $orderGoods->is_real = 1;
        $orderGoods->is_gift = self::IS_GIFT_NO;
        $orderGoods->parent_id = 0;
        return $orderGoods;
    }

    public function getShippingStatus() {
        if ($this->send_number == 0) {
            return '未发货';
        }
        else if ($this->send_number < $this->goods_number) {
            return '部分发货';
        }
        else {
            return '全部发货';
        }
    }

    public function getGoodsThumb() {
        if (empty($this->goods)) {
            return '';
        }
        return ImageHelper::get_image_path($this->goods->goods_thumb);
    }

    public function getTotalAmount() {
        return NumberHelper::price_format($this->goods_price * $this->goods_number);
    }

    public function allRefund() {
        return $this->goods_number == $this->back_number;
    }

    /**
     * 根据活动类型，获取 is_gift的值
     * @param $eventType
     * @return int
     */
    public static function isGift($eventType)
    {
        switch ($eventType) {
            //  满赠商品
            case Event::EVENT_TYPE_FULL_GIFT:
                $isGift = OrderGoods::IS_GIFT_GIFT;
                break;
            //  物料
            case Event::EVENT_TYPE_WULIAO:
                $isGift = OrderGoods::IS_GIFT_WULIAO;
                break;
            //  普通商品
            default :
                $isGift = OrderGoods::IS_GIFT_NO;
                break;
        }

        return $isGift;
    }
}
