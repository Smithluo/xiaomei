<?php

namespace common\models;

use common\behaviors\RecordOrderModifyActionBehavior;
use common\helper\CacheHelper;
use common\helper\DateTimeHelper;
use common\helper\EventHelper;
use common\helper\GoodsHelper;
use common\helper\NumberHelper;
use common\helper\OrderGroupHelper;
use common\helper\PaymentHelper;
use common\helper\TextHelper;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "o_order_group".
 *
 * @property integer $id
 * @property string $group_id
 * @property integer $user_id
 * @property integer $create_time
 * @property integer $group_status
 * @property string $consignee
 * @property integer $country
 * @property integer $province
 * @property integer $city
 * @property integer $district
 * @property string $address
 * @property string $mobile
 * @property integer $pay_id
 * @property string $pay_name
 * @property string $goods_amount
 * @property string $shipping_fee
 * @property string $money_paid
 * @property string $order_amount
 * @property string $pay_time
 * @property string $shipping_time
 * @property string $recv_time
 * @property string $discount
 * @property integer $event_id
 * @property integer $rule_id
 * @property integer $offline
 */
class OrderGroup extends \yii\db\ActiveRecord
{

    const ORDER_GROUP_STATUS_UNPAY = 0;
    const ORDER_GROUP_STATUS_PAID = 1;
    const ORDER_GROUP_STATUS_HANDLING = 2;
    const ORDER_GROUP_STATUS_FINISHED = 3;
    const ORDER_GROUP_STATUS_CANCELED = 4;
    const ORDER_GROUP_STATUS_SHIPPING_FINISH = 5;
    const ORDER_GROUP_STATUS_RETURN_REFUND = 6;
    const ORDER_GROUP_STATUS_ALL = 10;

    public static $order_group_status = [
        self::ORDER_GROUP_STATUS_UNPAY=>'未付款',
        self::ORDER_GROUP_STATUS_PAID =>'已付款',
        self::ORDER_GROUP_STATUS_HANDLING=>'处理中',
        self::ORDER_GROUP_STATUS_FINISHED=>'已完成',
        self::ORDER_GROUP_STATUS_CANCELED => '已取消',
        self::ORDER_GROUP_STATUS_SHIPPING_FINISH => '已发货',
        self::ORDER_GROUP_STATUS_RETURN_REFUND => '退款/退货',
    ];
    //小botton 按钮样式
    public static $group_status_cs_map = [
        self::ORDER_GROUP_STATUS_UNPAY=>'<span class="label label-warning">未付款</span>',
        self::ORDER_GROUP_STATUS_PAID =>'<span class="label label-primary">已付款</span>',
        self::ORDER_GROUP_STATUS_HANDLING=>'<span class="label label-danger">处理中</span>',
        self::ORDER_GROUP_STATUS_RETURN_REFUND=>'<span class="label label-danger">退款/退货</span>',
        self::ORDER_GROUP_STATUS_FINISHED=>'<span class="label label-success">已完成</span>',
        self::ORDER_GROUP_STATUS_CANCELED=>'<span class="label label-default">已取消</span>',
        self::ORDER_GROUP_STATUS_SHIPPING_FINISH=>'<span class="label label-danger">已发货</span>'
    ];

    public static $group_status_detail_cs_map =[
        self::ORDER_GROUP_STATUS_UNPAY=>'<span class="btn btn-w-m btn-warning od-status">未付款</span>',
        self::ORDER_GROUP_STATUS_PAID =>'<span class="btn btn-w-m btn-primary od-status">已付款</span>',
        self::ORDER_GROUP_STATUS_HANDLING=>'<span class="btn btn-w-m btn-danger od-status">处理中</span>',
        self::ORDER_GROUP_STATUS_RETURN_REFUND=>'<span class="btn btn-w-m btn-danger od-status">退款/退货</span>',
        self::ORDER_GROUP_STATUS_FINISHED=>'<span class="btn btn-w-m btn-success od-status">已完成</span>',
        self::ORDER_GROUP_STATUS_CANCELED=>'<span class="btn btn-w-m btn-default od-status">已取消</span>',
        self::ORDER_GROUP_STATUS_SHIPPING_FINISH=>'<span class="btn btn-w-m btn-danger od-status">已发货</span>'
    ];

    public $userName;
    public $total_amount;
    public $total_discount;
    public $note;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_order_group';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'create_time', 'group_status', 'country', 'province', 'city', 'district', 'pay_id', 'pay_time', 'shipping_time', 'recv_time', 'event_id', 'rule_id', 'offline'], 'integer'],
            [['goods_amount', 'shipping_fee', 'money_paid', 'order_amount', 'discount'], 'number'],
            [['group_id'], 'string', 'max' => 22],
            [['consignee', 'mobile'], 'string', 'max' => 60],
            [['address'], 'string', 'max' => 255],
            [['pay_name'], 'string', 'max' => 120],
            [['group_id'], 'unique'],
            [['country'], 'default', 'value' => 1],
            [['province', 'city', 'district'], 'default', 'value' => 0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'group_id' => '总单号',
            'user_id' => '用户ID',
            'create_time' => '创建时间戳',
            'group_status' => '总单综合状态',
            'consignee' => '收货人',
            'country' => '收货地址的国家',
            'province' => '收货地址的省份',
            'city' => '收货地址的城市',
            'district' => '收货地址的区县',
            'address' => '收货地址的详细地址',
            'mobile' => '收货人手机号',
            'pay_id' => '支付方式ID',
            'pay_name' => '支付方式名称',
            'goods_amount' => '总货款',
            'shipping_fee' => '总运费',
            'money_paid' => '总已付款',
            'order_amount' => '总待付款',
            'pay_time' => '支付时间',
            'shipping_time' => '发货处理完成的时间',
            'recv_time' => '总单确认收货的时间',
            'discount' => '总单的总折扣',
            'offline' => '是否线下单',
        ];
    }

    /**
     * 获取订单
     * @return \yii\db\ActiveQuery
     */
    public function getOrders() {
        return $this->hasMany(OrderInfo::className(), [
            'group_id' => 'group_id',
        ]);
    }

    /**
     * 通过主键获取子单列表
     * @return \yii\db\ActiveQuery
     */
    public function getOrderList() {
        return $this->hasMany(OrderInfo::className(), [
            'group_identity' => 'id',
        ]);
    }

    public function getFirstOrder() {
        if (!empty($this->orderList)) {
            return $this->orderList[0];
        }
        return null;
    }

    /**
     * 所有发货单
     * @return \yii\db\ActiveQuery
     */
    public function getDeliveryOrders() {
        return $this->hasMany(DeliveryOrder::className(), [
            'group_id' => 'group_id',
        ]);
    }

    /**
     * 获取订单所有的分成记录
     * @return \yii\db\ActiveQuery
     */
    public function getServicerDivideRecord() {
        return $this->hasMany(ServicerDivideRecord::className(), [
            'group_id' => 'group_id',
        ]);
    }

    /**
     * 获取下单用户
     * @return \yii\db\ActiveQuery
     */
    public function getUsers() {
        return $this->hasOne(Users::className(), [
            'user_id' => 'user_id',
        ]);
    }

    /**
     * 根据所有子单重设总单状态
     */
    public function setupOrderStatus() {
        $orders = $this->getSubOrders();
        $isUnpay = true;
        $isPaid = true;
        $isFinished = true;
        $allCanceled = true;
        $allShipped = true;
        $allReturnOrRefund = true;

        if (empty($orders)) {
            $isPaid = false;
            $isFinished = false;
            $allShipped = false;
        }

        foreach ($orders as $order) {
            if (!$order->isCanceled()) {
                $allCanceled = false;
            }
            if (!$order->isUnpay()) {
                $isUnpay = false;
            }
            if (!$order->isPaid()) {
                $isPaid = false;
            }
            if (!$order->isFinished()) {
                $isFinished = false;
            }
            if (!$order->isShipped() && !$order->isReturnOrRefund()) {
                $allShipped = false;
            }
            if (!$order->isReturnOrRefund()) {
                $allReturnOrRefund = false;
            }
        }

        if ($allReturnOrRefund) {
            $this->group_status = OrderGroup::ORDER_GROUP_STATUS_RETURN_REFUND;
        }
        elseif ($allCanceled) {
            $this->group_status = OrderGroup::ORDER_GROUP_STATUS_CANCELED;
        }
        elseif ($allShipped) {
            $this->group_status = OrderGroup::ORDER_GROUP_STATUS_SHIPPING_FINISH;
        }
        elseif ($isFinished) {
            $this->group_status = OrderGroup::ORDER_GROUP_STATUS_FINISHED;
        }
        elseif ($isPaid) {
            $this->group_status = OrderGroup::ORDER_GROUP_STATUS_PAID;
        }
        elseif ($isUnpay) {
            $this->group_status = OrderGroup::ORDER_GROUP_STATUS_UNPAY;
        }
        else {
            $this->group_status = OrderGroup::ORDER_GROUP_STATUS_HANDLING;
        }
    }

    //获取所有子单的商品数量
    public function getAllGoodsNumber()
    {
        $orderInfoList = $this->getSubOrders();
        $goodsNumber = 0;
        foreach($orderInfoList as $orderInfo)
        {
            foreach($orderInfo->ordergoods as $orderGoods)
            {
                $goodsNumber += $orderGoods['goods_number'];
            }
        }
        return $goodsNumber;
    }

    // 获取所有子单中已发货的数量
    public function getAllSendNumber()
    {
        $orderInfoList = $this->getSubOrders();
        $sendNumber =0;
        foreach($orderInfoList as $orderInfo)
        {
            foreach($orderInfo->ordergoods as $orderGoods)
            {
                $sendNumber += $orderGoods['send_number'];
            }
        }

        Yii::warning('sendNumber = '. $sendNumber, __METHOD__);

        return $sendNumber;
    }

    public function getTotalFee() {
        $totalFee = 0;
        foreach ($this->getSubOrders() as $order) {
            $totalFee += $order->getTotalAmount();
        }
        return NumberHelper::price_format($totalFee);
    }

    public function getMoneyPaid() {
        $total = 0;
        foreach ($this->getSubOrders() as $order) {
            $total += $order->money_paid;
        }
        return NumberHelper::price_format($total);
    }

    public function getTotalOrderAmount() {
        $total = 0;
        foreach ($this->getSubOrders() as $order) {
            $total += $order->order_amount;
        }
        return NumberHelper::price_format($total);
    }

    //发货 进度
    public function getProgress()
    {
        $all = $this->allGoodsNumber;
        $send = $this->allSendNumber;
        return $send/$all*100;
    }
    //是否已经全部发货
    public function getIsAllShipped()
    {
        $all = $this->allGoodsNumber;
        $send = $this->allSendNumber;
        $end = $all-$send;
        if($end==0)
        {
            return true;
        }else{
            return false;
        }
    }

    // 总提成
    public function getAlreadyDivide()
    {
        $servicerDivideRecord = $this->servicerDivideRecord;

        $totalCount = 0;
        foreach($servicerDivideRecord as $divide)
        {
            $totalCount += $divide->divide_amount+$divide->parent_divide_amount;
        }
        return $totalCount;
    }

    //已产生服务商提成
    public function getBossAlreadyDivide()
    {
        $servicerDivideRecord = $this->servicerDivideRecord;
        $totalCount = 0;
        foreach($servicerDivideRecord as $divide)
        {
            $totalCount += $divide->parent_divide_amount;
        }
        return $totalCount;
    }
    //业务员已产生提成
    public function getServicerAlreadyDivide()
    {
        $servicerDivideRecord = $this->servicerDivideRecord;
        $totalCount = 0;
        foreach($servicerDivideRecord as $divide)
        {
            $totalCount += $divide->divide_amount;
        }
        return $totalCount;
    }
    // 有没有分成记录
    public function getHasRecord()
    {
        $servicerDivideRecord = $this->servicerDivideRecord;
        return !empty($servicerDivideRecord);
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

    public function getDistrictRegion() {
        return $this->hasOne(Region::className(), [
            'region_id' => 'district',
        ]);
    }

    /**
     * 同步费用信息，用子单中的费用信息叠加
     */
    public function syncFeeInfo() {
        $totalGoodsAmount = 0;
        $totalShippingFee = 0;
        $totalMoneyPaid = 0;
        $totalOrderAmount = 0;
        $totalDiscount = 0;

        $orderList = $this->getSubOrders();
        foreach ($orderList as $orderInfo) {
            $totalGoodsAmount += $orderInfo->goods_amount;
            $totalShippingFee += $orderInfo->shipping_fee;
            $totalMoneyPaid += $orderInfo->money_paid;
            $totalOrderAmount += $orderInfo->order_amount;
            $totalDiscount += $orderInfo->discount;
        }

        $this->goods_amount = $totalGoodsAmount;
        $this->shipping_fee = $totalShippingFee;
        $this->money_paid = $totalMoneyPaid;
        $this->order_amount = $totalOrderAmount;
        $this->discount = $totalDiscount;
    }

    /**
     * 同步时间信息
     * 支付时间取子单中最后一笔支付时间
     * 发货时间取子单中最后一个发货时间
     * 收货时间取子单中最后一个收货时间
     */
    public function syncTimeInfo() {
        $lastPayTime = 0;
        $lastShippingTime = 0;
        $lastRecvTime = 0;

        foreach ($this->getSubOrders() as $order) {
            if ($lastPayTime < $order->pay_time) {
                $lastPayTime = $order->pay_time;
            }

            if ($lastShippingTime < $order->shipping_time) {
                $lastShippingTime = $order->shipping_time;
            }

            if ($lastRecvTime < $order->recv_time) {
                $lastRecvTime = $order->recv_time;
            }
        }

        $this->pay_time = $lastPayTime;
        $this->shipping_time = $lastShippingTime;
        $this->recv_time = $lastRecvTime;
    }

    public function getCoupon() {
        return $this->hasOne(CouponRecord::className(), [
            'group_id' => 'group_id',
        ]);
    }

    /**
     * 重新计算折扣
     */
    public function recalcDiscount($afterSendGoods = true) {
        $goodsMap = [];
        $orderKeyGoodsMap = [];

        foreach ($this->getSubOrders() as $order) {
            $orderGoodsList = $order->ordergoods;
            foreach ($orderGoodsList as $orderGoods) {
                if ($orderGoods->is_gift) {
                    Yii::warning('赠品、物料 不参与总价计算');
                    continue;
                }
                if ($afterSendGoods) {
                    $goodsNumber = $orderGoods->send_number - $orderGoods->back_number;
                }
                else {
                    $goodsNumber = $orderGoods->goods_number;
                }
                $goodsNumber = $goodsNumber >= 0 ? $goodsNumber : 0;
                if ($goodsNumber > 0) {
                    $goodsInfo = [
                        'goods_id' => $orderGoods['goods_id'],
                        'goods_number' => $goodsNumber,
                        'selected' => 1,
                        'goods_price' => $orderGoods['goods_price'],
                    ];
                    $goodsMap[] = $goodsInfo;

                    $orderKeyGoodsMap[$order['order_id']][] = $goodsInfo;
                }
            }
        }

        $events = EventHelper::processEvent($goodsMap, [], $this->user_id);

        if (empty($events['fullCut'])) {
            Yii::warning('没有减价需要计算 group_id = '. $this->group_id, __METHOD__);
            return false;
        }

        $fullCutMap = EventHelper::assignFullCut($events['fullCut'], $orderKeyGoodsMap);

        foreach ($this->getSubOrders() as $order) {
            if (!empty($fullCutMap[$order->order_id]['discount'])) {
                $newDiscount = $fullCutMap[$order->order_id]['discount'];
                $oldDiscount = $order->discount;

                if ($oldDiscount != $newDiscount) {
                    Yii::warning('订单折扣发生变化 old = '. $oldDiscount, ', new = '. $newDiscount, __METHOD__);
                }

                $order->discount = $newDiscount;
            }
            $order->recalcGoodsAmount();
            $order->save(false);

            if (!empty($orderKeyGoodsMap[$order->order_id])) {
                $goodsList = $orderKeyGoodsMap[$order->order_id];
                foreach ($order->ordergoods as $orderGoods) {
                    if ($orderGoods->is_gift) {
                        continue;
                    }
                    foreach ($goodsList as $goods) {
                        if ($orderGoods->goods_id == $goods['goods_id']) {
                            $orderGoods->pay_price = isset($goods['pay_price']) ? $goods['pay_price'] : $goods['goods_price'];
                            $orderGoods->save(false);
                        }
                    }
                }
            }
        }

        $this->syncFeeInfo();
        $this->save(false);
    }

    /**
     * 获取总成本，总成本为货款成本+运费成本
     * @return string
     */
    public function getTotalCost() {
        return $this->getCost() + $this->getShippingCost();
    }

    public function getTotalGoodsAmount() {
        $result = 0;
        foreach ($this->getSubOrders() as $order) {
            $result += $order->getTotalGoodsAmount();
        }
        return NumberHelper::price_format($result);
    }

    /**
     * 计算成本
     */
    public function getCost() {
        $cost = 0;
        foreach ($this->getSubOrders() as $order) {
            $cost += $order->getCost();
        }
        return NumberHelper::price_format($cost);
    }

    /**
     * 获取获取运费成本
     * @return string
     */
    public function getShippingCost() {
        $shippingCost = 0.0;
        foreach ($this->deliveryOrders as $deliveryOrder) {
            $shippingCost += $deliveryOrder->shipping_fee;
        }
        Yii::warning('总运费 = '. $shippingCost, __METHOD__);
        return NumberHelper::price_format($shippingCost);
    }

    /**
     * 获取利润
     * @return string
     */
    public function getProfit() {
        return NumberHelper::price_format($this->getTotalGoodsAmount() + $this->shipping_fee - $this->discount - $this->getTotalCost());
    }

    /**
     * 获取总单的商品总额
     * @return int
     */
    public function getGoodsAmount() {
        $amount = 0;
        foreach ($this->getSubOrders() as $order) {
            $amount += $order->goods_amount;
        }
        return $amount;
    }

    public function serviceDivide() {
        if ($this->offline != 0) {
            Yii::warning('线下订单不参与分成 group_id = '. $this->group_id, __METHOD__);
        }

        if (ServicerDivideRecord::find()->where([
            'group_id' => $this->group_id,
        ])->exists()) {
            $msg = '已经有过分成记录 group_id = '. $this->group_id;
            echo $msg. PHP_EOL;
            Yii::error($msg, __METHOD__);
            return;
        }

        $this->setupOrderStatus();

        if ($this->group_status != self::ORDER_GROUP_STATUS_FINISHED) {
            $msg = '订单未完成，不能分成 group_id = '. $this->group_id;
            echo $msg. PHP_EOL;
            Yii::error($msg, __METHOD__);
            return;
        }

        $orderList = $this->getSubOrders();
        if (empty($orderList)) {
            $msg = '没有可供分成的订单 group_id = '. $this->group_id;
            echo $msg. PHP_EOL;
            Yii::error($msg, __METHOD__);
            return;
        }

        //判断是否积分对话的订单，积分兑换订单不参与分成
        if ($orderList[0]['extension_code'] == OrderInfo::EXTENSION_CODE_INTEGRAL) {
            $msg = '积分兑换的订单不参与分成 group_id = '. $this->group_id;
            echo $msg. PHP_EOL;
            Yii::error($msg, __METHOD__);
            return;
        }

        //总成本
        $cost = $this->getTotalCost();
        Yii::warning('cost = '. $cost, __METHOD__);
        //总订单金额
        $totalAmount = $this->getTotalGoodsAmount() + $this->shipping_fee - $this->discount;
        Yii::warning('totalAmount = '. $totalAmount, __METHOD__);

        $profit = $totalAmount - $cost;
        Yii::warning('profit = '. $profit, __METHOD__);

        if ($profit <= 0) {
            $msg = '没有利润可以供分成 group_id = '. $this->group_id;
            echo $msg. PHP_EOL;
            Yii::error($msg, __METHOD__);
            return;
        }

        Yii::warning('利润是 '. $profit. ', group_id = '. $this->group_id, __METHOD__);

        //获取订单的用户
        $userInfo = $this->users;
        if (empty($userInfo)) {
            $msg = '订单缺少用户 id = '. $this->group_id;
            echo $msg. PHP_EOL;
            Yii::error($msg, __METHOD__);
            return;
        }

        //得到用户绑定的业务员
        $servicerUser = $userInfo->servicerUser;
        if (empty($servicerUser)) {
            $msg = '用户没有绑定服务商或服务商不存在 group_id = '. $this->group_id. ', servicerUserId = '. $userInfo->servicer_user_id;
            echo $msg. PHP_EOL;
            Yii::warning($msg, __METHOD__);
            return;
        }

        //获取业务员归属的服务商
        $superServicerInfo = $servicerUser->supserServicerUser;
        if (empty($superServicerInfo)) {
            $msg = '业务员没有归属的服务商 group_id = '. $this->group_id. ', super_user_id = '. $servicerUser->servicer_super_id;
            echo $msg. PHP_EOL;
            Yii::warning($msg, __METHOD__);
            return;
        }

        $authManager = Yii::$app->authManager;
        $rolesOfServicerUser = $authManager->getRolesByUser($servicerUser->user_id);

        //上级服务商没角色
        if (empty($rolesOfServicerUser)) {
            $msg = '上级服务商缺少角色';
            echo $msg. PHP_EOL;
            Yii::warning($msg, __METHOD__);
        }

        $servicerDividePercentConfig = CacheHelper::getShopConfigParams(['servicer_divide_pre']);

        if (!isset($servicerDividePercentConfig['servicer_divide_pre']['value'])) {
            $msg = '没有配置服务商分成比例';
            echo $msg. PHP_EOL;
            Yii::error($msg, __METHOD__);
            return;
        }

        $dividePercent = $servicerDividePercentConfig['servicer_divide_pre']['value'];

        $totalDivideAmount = $dividePercent * $profit / 100.0;

        //看业务员是否就是老板
        $servicerIsBoss = isset($rolesOfServicerUser['service_boss']);
        //如果业务员也是老板，就把分成全分给老板
        if ($servicerIsBoss) {
            $servicerDivideRecord = new ServicerDivideRecord();

            if (!empty($this->getSubOrders())) {
                foreach ($this->getSubOrders() as $order) {
                    if ($order->order_status == OrderInfo::ORDER_STATUS_REALLY_DONE
                        && $order->pay_status == OrderInfo::PAY_STATUS_PAYED
                        && $order->shipping_status == OrderInfo::SHIPPING_STATUS_RECEIVED) {
                        $servicerDivideRecord->order_id = $order->order_id;
                        break;
                    }
                }
            }
            if ($servicerDivideRecord->order_id == 0) {
                $servicerDivideRecord->order_id = $this->group_id;
            }

            $servicerDivideRecord->amount = $totalAmount;
            $servicerDivideRecord->spec_strategy_id = '';
            $servicerDivideRecord->user_id = $this->user_id;
            $servicerDivideRecord->servicer_user_id = $servicerUser->user_id;
            $servicerDivideRecord->servicer_user_name = $servicerUser->nickname;
            $servicerDivideRecord->parent_servicer_user_id = $servicerUser->user_id;
            $servicerDivideRecord->divide_amount = 0;
            $servicerDivideRecord->parent_divide_amount = $totalDivideAmount;
            $servicerDivideRecord->group_id = $this->group_id;

            if (!$servicerDivideRecord->save()) {
                Yii::error('分成流水保存失败 group_id = '. $this->group_id. ', errors = '. VarDumper::export($servicerDivideRecord->errors));
            }
            else {
                Yii::warning('分成成功 group_id = '. $this->group_id. ', divideRecord = '. VarDumper::export($servicerDivideRecord));
            }
        }
        else {
            $salemanDividePercent = $superServicerInfo->divide_percent;
            $salemanDivideAmount = 0;
            $parentDivideAmount = $totalDivideAmount;
            if (!empty($salemanDividePercent)) {
                $salemanDivideAmount = NumberHelper::price_format($totalDivideAmount * $salemanDividePercent / 100.0);
                $parentDivideAmount = $totalDivideAmount - $salemanDivideAmount;
            }

            $servicerDivideRecord = new ServicerDivideRecord();
            if (!empty($this->getSubOrders())) {
                foreach ($this->getSubOrders() as $order) {
                    if ($order->order_status == OrderInfo::ORDER_STATUS_REALLY_DONE
                        && $order->pay_status == OrderInfo::PAY_STATUS_PAYED
                        && $order->shipping_status == OrderInfo::SHIPPING_STATUS_RECEIVED) {
                        $servicerDivideRecord->order_id = $order->order_id;
                        break;
                    }
                }
            }
            if ($servicerDivideRecord->order_id == 0) {
                $servicerDivideRecord->order_id = $this->group_id;
            }

            $servicerDivideRecord->amount = $totalAmount;
            $servicerDivideRecord->spec_strategy_id = '';
            $servicerDivideRecord->user_id = $this->user_id;
            $servicerDivideRecord->servicer_user_id = $servicerUser->user_id;
            $servicerDivideRecord->servicer_user_name = $servicerUser->nickname;
            $servicerDivideRecord->group_id = $this->group_id;
            $servicerDivideRecord->parent_servicer_user_id = $superServicerInfo->user_id;
            $servicerDivideRecord->divide_amount = $salemanDivideAmount;
            $servicerDivideRecord->parent_divide_amount = $parentDivideAmount;

            if (!$servicerDivideRecord->save()) {
                Yii::error('分成流水保存失败 group_id = '. $this->group_id. ', errors = '. VarDumper::export($servicerDivideRecord->errors));
            }
            else {
                Yii::warning('分成成功 group_id = '. $this->group_id. ', divideRecord = '. VarDumper::export($servicerDivideRecord));
            }
        }

    }

    /**
     * 整单取消
     */
    public function cancel($note) {
        $orders = $this->getSubOrders();
        $success = true;
        if (!empty($orders)) {
            foreach ($orders as $order) {
                if (!$order->cancel($note)) {
                    $success = false;
                }
            }
        }
        $this->setupOrderStatus();
        if (!$this->save()) {
            Yii::error('总单取消失败 groupId = '. $this->group_id. ', errors = '. VarDumper::export($this->errors), __METHOD__);
            $success = false;
        }
        return $success;
    }

    /**
     * 整单支付
     */
    public function pay($note) {
        $orders = $this->getSubOrders();
        $success = true;
        if (!empty($orders)) {
            foreach ($orders as $order) {
                if (!$order->pay($note)) {
                    $success = false;
                }
            }
        }
        $this->pay_id = PaymentHelper::PAY_ID_BACKEND;
        $this->pay_name = PaymentHelper::$paymentMap[$this->pay_id];
        $this->setupOrderStatus();
        $this->syncFeeInfo();
        $this->syncTimeInfo();
        if (!$this->save()) {
            Yii::error('总单支付失败 groupId = '. $this->group_id. ', errors = '. VarDumper::export($this->errors), __METHOD__);
            $success = false;
        }
        return $success;
    }

    /**
     * 发货
     * @param $data ['order_id' => ['shipppingInfo' => xxx, 'note' => xxx, 'shippingFee' => xxx]]
     */
    public function shipping($data) {
        foreach ($this->getSubOrders() as $order) {
            $info = $data[$order->order_id];
            if (!empty($info['note']) && !empty($info['shippingInfo'])) {
                $order->shipping($info['note'], $info['shippingInfo'], $info['shippingFee'] ?: 0);
            }
        }
    }

    public function shipped($note) {
        foreach ($this->getSubOrders() as $order) {
            $order->shipped($note);
        }
        $this->setupOrderStatus();
        $this->save();
    }

    public function getWechatPayInfo() {
        return $this->hasOne(WechatPayInfo::className(), ['group_id' => 'group_id'])->orderBy([
            'pay_id' => SORT_DESC,
        ]);
    }

    public function getAlipayInfo() {
        return $this->hasOne(AlipayInfo::className(), ['group_id' => 'group_id'])->orderBy([
            'id' => SORT_DESC,
        ]);
    }

    public function getYinlianPayInfo() {
        return $this->hasOne(YinlianPayinfo::className(), ['group_id' => 'group_id'])->orderBy([
            'id' => SORT_DESC,
        ]);
    }

    public function getYeePayInfo() {
        return $this->hasOne(YeePayinfo::className(), [
            'group_id' => 'group_id',
        ])->orderBy([
            'id' => SORT_DESC,
        ]);
    }

    public function getBackOrderList() {
        return $this->hasMany(BackOrder::className(), [
            'group_id' => 'group_id',
        ]);
    }

    public function isIntegralExchange() {
        if (empty($this->getSubOrders())) {
            return false;
        }
        if ($this->getSubOrders()[0] == OrderInfo::EXTENSION_CODE_INTEGRAL) {
            return true;
        }
        return false;
    }

    public function getEvent() {
        return $this->hasOne(Event::className(), [
            'event_id' => 'event_id',
        ]);
    }

    public function getFullCutRule() {
        return $this->hasOne(FullCutRule::className(), [
            'rule_id' => 'rule_id',
        ]);
    }

    /**
     * 总单取消 并 把总单的商品全部加入购车
     * @param string $group_id 总单编号
     * @param string $note 取消原因
     * @return bool 如果有一个商品被复制成功，则返回true； 否则返回false
     */
    public static function cancelOrderAndAddToCart($group_id, $note = '') {
        Yii::warning(' 入参 $group_id = '.$group_id, __METHOD__);
        $status = false;
        $orderGroup = OrderGroup::find()->with([
            'orders',
            'orders.ordergoods',
            'orders.ordergoods.goods'
        ])->where([
            'group_id' => $group_id,
        ])->one();

        if (!empty($orderGroup)) {

            $isGeneralOrder = true;
            foreach ($orderGroup->getSubOrders() as $order) {
                //  团采/秒杀 积分兑换，暂时不返单，团采/秒杀 需要经过活动页， 积分兑换为立即支付，积分不足自动取消
                if ($order->extension_code != 'general' && $order->extension_code != 'general_buy_now') {
                    $isGeneralOrder = false;
                }
            }

            //  团采、秒杀、积分兑换  不返单
            if ($isGeneralOrder) {
                $rs = self::addOrderGoodsToCart($group_id);
                if (!$rs) {
                    Yii::warning($group_id.' 订单返到购物车失败', __METHOD__);
                }
            }
            $status = $orderGroup->cancel($note);
        }

        return $status;
    }

    /**
     * 把总单的商品全部加入购物车
     *
     * 应先把购物车中的其他商品置为未选中
     * 向购物车插入新的记录之前判断购物车中是否存在该商品，如果存在，修正goods_number、goods_price、勾选状态
     * @param string $group_id 总单编号
     * @return bool 如果有一个商品被复制成功，则返回true； 否则返回false
     */
    public static function addOrderGoodsToCart($group_id) {
        $rs = false;   //  默认没有商品被复制到购物车中
        $orderGroup = OrderGroup::find()->with([
            'orders',
            'orders.ordergoods',
            'orders.ordergoods.goods'
        ])->where([
            'group_id' => $group_id,
        ])->one();

        if (!empty($orderGroup)) {
            Cart::updateAll(['selected' => 0], ['user_id' => $_SESSION['user_id']]);
            $cartList = Cart::find()->where(['user_id' => $_SESSION['user_id']])->indexBy('goods_id')->all();

            foreach ($orderGroup->getSubOrders() as $order) {
                foreach ($order->ordergoods as $ordergoods) {
                    if (
                        empty($ordergoods['goods']) ||
                        !$ordergoods->goods['is_on_sale'] ||
                        $ordergoods->goods['is_delete']
                    ) {
                        ToLog(5, __METHOD__. ' 商品已经下架或者删除 rec_id = '. $ordergoods['rec_id']. ', order_id = '. $order['order_id']. ', group_id = '. $group_id);
                        continue;
                    }
                    if ($ordergoods['is_gift']) {
                        continue;
                    }

                    //  修正返回购物车的商品购买数量，超库存则使用库存， 不超库存则使用原订单的商品数量
                    $min_goods_number = min($ordergoods['goods']['goods_number'], $ordergoods['goods_number']);
                    //  如果购物车中存在 当前商品，修正数据，否则insert
                    if (isset($cartList[$ordergoods['goods']['goods_id']])) {
                        $cart = $cartList[$ordergoods['goods']['goods_id']];
                        $cart->goods_number = $min_goods_number;
                    } else {
                        $cart = Cart::createFromGoods($ordergoods['goods'], $_SESSION['user_id'], $min_goods_number);
                    }

                    if (!empty($cart)) {
                        $cart->goods_price = GoodsHelper::getFinalPrice($ordergoods['goods'], $cart->goods_number,
                            $_SESSION['user_rank']);
                        $cart->setAttribute('selected', Cart::IS_SELECTED);
                        if ($cart->save()) {
                            $rs = true;
                        } else {
                            Yii::warning('订单商品返到购物车失败 $cart->errors = '.TextHelper::getErrorsMsg($cart->errors));
                        }
                    }
                }
            }
        }

        return $rs; //  如果有一个商品被复制成功，则返回true
    }

    /**
     * 获取总单的子单
     * 优先通过 o_order_group.id 关联，如果获取不到，通过 o_order_group.group_id关联;
     * @return mixed
     */
    public function getSubOrders() {
        $orderList = $this->orderList;
        if (!empty($orderList)) {
            return $orderList;
        } else {
            return $this->orders;
        }
    }

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            RecordOrderModifyActionBehavior::className(),
        ]); // TODO: Change the autogenerated stub
    }

    /**
     * 把列表中的支付方式筛选改为下拉选择
     * 2017.07.24
     * @author HongXunPan
     */
    public static function getAllPayName(){
        $payNameMap = [];
        $payName = \common\models\OrderGroup::find()->select('pay_name')->groupBy('pay_name')->asArray()->all();

        foreach ($payName as $payNameStr){
            $payNameMap[$payNameStr['pay_name']] = $payNameStr['pay_name'];
        }

        return $payNameMap;
    }
}
