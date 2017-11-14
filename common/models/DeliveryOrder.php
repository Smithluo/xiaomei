<?php

namespace common\models;

use common\helper\DateTimeHelper;
use common\helper\NumberHelper;
use Yii;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "o_delivery_order".
 *
 * @property string $delivery_id
 * @property string $delivery_sn
 * @property string $order_sn
 * @property string $order_id
 * @property string $invoice_no
 * @property string $add_time
 * @property integer $shipping_id
 * @property string $shipping_name
 * @property string $user_id
 * @property string $action_user
 * @property string $consignee
 * @property string $address
 * @property integer $country
 * @property integer $province
 * @property integer $city
 * @property integer $district
 * @property string $sign_building
 * @property string $email
 * @property string $zipcode
 * @property string $tel
 * @property string $mobile
 * @property string $best_time
 * @property string $postscript
 * @property string $how_oos
 * @property string $insure_fee
 * @property string $shipping_fee
 * @property string $update_time
 * @property integer $suppliers_id
 * @property integer $status
 * @property integer $agency_id
 * @property string $group_id
 */
class DeliveryOrder extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_delivery_order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['delivery_sn', 'order_sn'], 'required'],
            [['order_id', 'add_time', 'shipping_id', 'user_id', 'country', 'province', 'city', 'district', 'update_time', 'suppliers_id', 'status', 'agency_id'], 'integer'],
            [['insure_fee', 'shipping_fee'], 'number'],
            [['delivery_sn', 'order_sn'], 'string', 'max' => 20],
            [['group_id'], 'string', 'max' => 22],
            [['invoice_no'], 'string', 'max' => 50],
            [['shipping_name', 'sign_building', 'best_time', 'how_oos'], 'string', 'max' => 120],
            [['action_user'], 'string', 'max' => 30],
            [['consignee', 'email', 'zipcode', 'tel', 'mobile'], 'string', 'max' => 60],
            [['address'], 'string', 'max' => 250],
            [['postscript'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'delivery_id' => '发货单ID',
            'delivery_sn' => '发货单号',
            'order_sn' => '订 单 号',
            'order_id' => '订单ID',
            'invoice_no' => '快递单号',
            'add_time' => '分单时间',
            'shipping_id' => 'Shipping ID',
            'shipping_name' => '快递公司',
            'user_id' => '用户ID',
            'action_user' => '操作人',
            'consignee' => '收 件 人',
            'address' => '详细地址',
            'country' => '国家',
            'province' => '省',
            'city' => '市',
            'district' => '县/区',
            'sign_building' => 'Sign Building',
            'email' => 'Email',
            'zipcode' => '邮政编码',
            'tel' => 'Tel',
            'mobile' => '手机号码',
            'best_time' => 'Best Time',
            'postscript' => 'Postscript',
            'how_oos' => 'How Oos',
            'insure_fee' => 'Insure Fee',
            'shipping_fee' => '运费',
            'update_time' => '更新时间',
            'suppliers_id' => 'Suppliers ID',
            'status' => 'Status',
            'agency_id' => 'Agency ID',
        ];
    }

    /**
     * 得到新发货单号
     * @return  string
     */
    public static function generateDeliverySn() {
        /* 选择一个随机的方案 */
        mt_srand((double) microtime() * 1000000);
        $sn = date('YmdHi') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        //已存在就重新生成
        while (DeliveryOrder::findOne([
            'delivery_sn' => $sn
        ])) {
            mt_srand((double) microtime() * 1000000);
            $sn = date('YmdHi') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        }
        return $sn;
    }

    public function getDeliveryGoods() {
        return $this->hasMany(DeliveryGoods::className(), ['delivery_id' => 'delivery_id']);
    }

    public function getServicerDivideRecord() {
        return $this->hasOne(ServicerDivideRecord::className(), ['delivery_id' => 'delivery_id']);
    }

    public static function createShippedDeliveryOrderFromOrderInfo($orderInfo) {
        $deliveryOrder = new DeliveryOrder();
        $deliveryOrder->delivery_sn = DeliveryOrder::generateDeliverySn();
        $deliveryOrder->order_sn = $orderInfo->order_sn;
        $deliveryOrder->order_id = $orderInfo->order_id;
        $deliveryOrder->add_time = DateTimeHelper::gmtime();
        $deliveryOrder->shipping_id = $orderInfo->shipping_id;
        $deliveryOrder->shipping_name = $orderInfo->shipping_name;
        $deliveryOrder->user_id = $orderInfo->user_id;
        $deliveryOrder->action_user = Yii::$app->user->identity['user_name'];
        $deliveryOrder->consignee = $orderInfo->consignee;
        $deliveryOrder->address = $orderInfo->address;
        $deliveryOrder->country = $orderInfo->country;
        $deliveryOrder->province = $orderInfo->province;
        $deliveryOrder->city = $orderInfo->city;
        $deliveryOrder->district = $orderInfo->district;
        $deliveryOrder->sign_building = null;
        $deliveryOrder->mobile = $orderInfo->mobile;
        $deliveryOrder->how_oos = '等待所有商品备齐后再发';
        $deliveryOrder->insure_fee = 0;
        $deliveryOrder->shipping_fee = 0;
        $deliveryOrder->update_time = DateTimeHelper::gmtime();
        $deliveryOrder->suppliers_id = 0;
        $deliveryOrder->status = 0;
        $deliveryOrder->agency_id = 0;
        return $deliveryOrder;
    }

    public function getOrderInfo() {
        return $this->hasOne(OrderInfo::className(), [
            'order_id' => 'order_id',
        ]);
    }

    /**
     * 对这个发货单进行分成
     */
    public function servicerDivide() {
        if (!empty($this->servicerDivideRecord)) {
            Yii::warning('发货单已经分成 id = '. $this->delivery_id, __METHOD__);
            return;
        }

        //获取订单
        $orderInfo = $this->orderInfo;
        if (empty($orderInfo)) {
            Yii::error('发货单缺少订单 id = '. $this->delivery_id, __METHOD__);
            return;
        }

        //积分兑换的商品不参与服务商分成
        if ($orderInfo['extension_code'] == 'integral_exchange') {
            Yii::warning('积分兑换订单不参与分成 id = '. $this->delivery_id, __METHOD__);
            return;
        }

        $deliveryGoodsList = $this->deliveryGoods;
        if (empty($deliveryGoodsList)) {
            Yii::error('发货单缺少商品 id = '. $this->delivery_id, __METHOD__);
            return;
        }

        //获取订单的用户
        $userInfo = $orderInfo->users;
        if (empty($userInfo)) {
            Yii::error('订单缺少用户 id = '. $this->delivery_id, __METHOD__);
            return;
        }

        //得到用户绑定的业务员
        $servicerUser = $userInfo->servicerUser;
        if (empty($servicerUser)) {
            Yii::warning('用户没有绑定服务商或服务商不存在 delivery_id = '. $this->delivery_id. ', servicerUserId = '. $userInfo->servicer_user_id, __METHOD__);
            return;
        }

        //获取业务员归属的服务商
        $superServicerInfo = $servicerUser->supserServicerUser;
        if (empty($superServicerInfo)) {
            Yii::warning('业务员没有归属的服务商 delivery_id = '. $this->delivery_id. ', super_user_id = '. $servicerUser->servicer_super_id);
        }

        //二级服务商总分成
        $totalDivideAmount = 0;
        //一级服务商总分成
        $totalParentDivideAmount = 0;
        //商品列表的总计金额
        $totalGoodsAmount = 0;
        //所有商品总的分成金额
        $totalGoodsDivideAmount = 0;
        //所有的分成策略,做个记录
        $allSpecStrategyIds = [];

        //遍历发货单中的商品进行分成计算
        foreach ($deliveryGoodsList as $deliveryGoods) {

            //商品信息
            $goodsInfo = $deliveryGoods->goods;
            if (empty($goodsInfo)) {
                Yii::error('缺少对应商品信息', __METHOD__);
            }
            //商品品牌
            $brandInfo = $deliveryGoods->goods->brand;

            Yii::warning('serviceDivide 商品名 = '. $deliveryGoods['goods_name']. ', goods_id = '. $deliveryGoods['goods_id'], __METHOD__);

            if (empty($brandInfo)) {
                Yii::error('缺少商品的品牌信息', __METHOD__);
            }

            //获取分成策略（即商品总的分成比例）
            if(!empty($goodsInfo['servicer_strategy_id'])) {
                $strategy_id = $goodsInfo['servicer_strategy_id'];
            }
            elseif(!empty($brandInfo['servicer_strategy_id'])) {
                $strategy_id = $brandInfo['servicer_strategy_id'];
            }
            else {
                Yii::error('serviceDivide no strategy_id  goods_id = '. $deliveryGoods['goods_id']. ', brand_id = '. $deliveryGoods['brand_id'], __METHOD__);
                continue;
            }

            $strategyInfo = ServicerStrategy::findOne([
                'id' => $strategy_id,
            ]);

            if (empty($strategyInfo)) {
                Yii::error('缺少分成比例 strategy_id = '. $strategy_id, __METHOD__);
                continue;
            }

            $percent_total = $strategyInfo['percent_total'];

            if ($percent_total < 0.00001) {
                Yii::error('分成总比例为0, percent = '. $percent_total, __METHOD__);
                continue;
            }

            //如果有上级代理商，就用上级代理商的分成策略
            if(!empty($superServicerInfo) && $superServicerInfo['user_id'] > 0) {
                $useServiceSpecStrategy = ServicerSpecStrategy::find()->where([
                    'servicer_user_id' => $superServicerInfo['user_id'],
                    'brand_id' => $brandInfo['brand_id'],
                ])->orderBy([
                    ServicerSpecStrategy::tableName(). '.id' => SORT_DESC,
                ])->one();
            }
            else {
                $useServiceSpecStrategy = ServicerSpecStrategy::find()->where([
                    'servicer_user_id' => $servicerUser['user_id'],
                    'brand_id' => $brandInfo['brand_id'],
                ])->orderBy([
                    ServicerStrategy::tableName(). '.id' => SORT_DESC,
                ])->one();
            }

            //如果有指定业务员的分成比例
            if(!empty($useServiceSpecStrategy)) {

                $allSpecStrategyIds[$deliveryGoods['goods_id']] = $useServiceSpecStrategy['id'];

                Yii::warning('分成策略 strategyInfo = '. VarDumper::export($strategyInfo). ', spec = '. VarDumper::export($useServiceSpecStrategy), __METHOD__);

                //业务员的分成比例
                $percent_level_2 = $useServiceSpecStrategy['percent_level_2'];

                //商品总金额
                $goods_amount = NumberHelper::price_format($deliveryGoods->goods_price * $deliveryGoods->send_number) * (1.0 - ShopConfig::getConfigValue('order_pay_fee'));
                $totalGoodsAmount += $goods_amount;

                //按照总金额，计算总共拿出来分成的金额
                $goods_total_divide_amount = NumberHelper::price_format((float)($goods_amount * $percent_total) / 100.0);
                $totalGoodsDivideAmount += $goods_total_divide_amount;

                //给二级服务商分成的金额
                $servicer_divide_amount = NumberHelper::price_format($goods_total_divide_amount * $percent_level_2 / 100.0);
                $totalDivideAmount += $servicer_divide_amount;

                //给一级服务商分成的金额
                $parent_servicer_divide_amount = NumberHelper::price_format($goods_total_divide_amount - $servicer_divide_amount);
                $totalParentDivideAmount += $parent_servicer_divide_amount;
            }
            //没指定业务员的分成比例的情况下就全分给服务商
            else {
                if (!empty($strategyInfo)) {
                    Yii::warning('指定分成策略为空，使用分成策略分成 service_spec_strategy_infos use strategy_info', __METHOD__);
                    //商品总金额
                    $goods_amount = NumberHelper::price_format($deliveryGoods->goods_price * $deliveryGoods->send_number) * (1.0 - ShopConfig::getConfigValue('order_pay_fee'));
                    $totalGoodsAmount += $goods_amount;
                    //按照总金额，计算总共拿出来分成的金额
                    $goods_total_divide_amount = NumberHelper::price_format((float)($goods_amount * $percent_total) / 100.0);
                    $totalGoodsDivideAmount += $goods_total_divide_amount;

                    //给一级服务商分成的金额
                    $parent_servicer_divide_amount = $goods_total_divide_amount;
                    $totalParentDivideAmount += $parent_servicer_divide_amount;
                }
                else {
                    Yii::warning('指定分成策略为空，只能continue goods_id = '. $deliveryGoods['goods_id']. ', brand = '. $brandInfo['brand_id'], __METHOD__);
                    continue;
                }
            }
        }

        //考虑满减折扣
        $discount = $orderInfo['discount'];
        if ($discount > 0) {
            if ($totalGoodsAmount > 0) {
                //不参与分成的百分比
                $percent = $discount / $orderInfo->getTotalFee();

                //纠正为折扣后的额度
                $totalGoodsDivideAmount = NumberHelper::price_format($totalGoodsDivideAmount * (1 - $percent));
                $totalDivideAmount = NumberHelper::price_format($totalDivideAmount * (1 - $percent));
                $totalParentDivideAmount = NumberHelper::price_format($totalGoodsDivideAmount - $totalDivideAmount);
            }
            else {
                Yii::error('商品总额为0, order_id = '. $orderInfo['order_id'], __METHOD__);
            }
        }

        //一级二级服务商都需要分成
        if(!empty($superServicerInfo)) {

            $record = new ServicerDivideRecord();
            //二级分成
            $record['group_id'] = $orderInfo['group_id'];
            $record['order_id'] = $orderInfo['order_id'];
            $record['amount'] = $totalGoodsAmount;
            if(count($allSpecStrategyIds)) {
                $record['spec_strategy_id'] = json_encode($allSpecStrategyIds);
            }
            $record['user_id'] = $orderInfo['user_id'];
            $record['servicer_user_id'] = $servicerUser['user_id'];
            $record['parent_servicer_user_id'] = $superServicerInfo['user_id'];
            $record['divide_amount'] = $totalDivideAmount;
            $record['parent_divide_amount'] = $totalParentDivideAmount;
            $record['servicer_user_name'] = $servicerUser['nickname'] ? $servicerUser['nickname'] : $servicerUser['user_name'];
            $record->delivery_id = $this->delivery_id;

            if (!$record->save()) {
                $this->flashError($record);
            }
            else {
                Yii::warning('生成分成成功 delivery_sn = '. $this->delivery_sn. ', order_sn = '. $orderInfo->order_sn. ', group_id = '. $orderInfo->group_id);
            }

        }
        //一级服务商，提取所有分成
        else {

            $divide_amount = $totalGoodsDivideAmount;

            if($goods_amount > 0 && $divide_amount > 0) {
                $record = new ServicerDivideRecord();
                $record['group_id'] = $orderInfo['group_id'];
                $record['order_id'] = $orderInfo['order_id'];
                $record['amount'] = $goods_amount;
                if(count($allSpecStrategyIds)) {
                    $record['spec_strategy_id'] = json_encode($allSpecStrategyIds);
                }
                $record['user_id'] = $orderInfo['user_id'];
                $record['parent_servicer_user_id'] = $servicerUser['user_id'];
                $record['parent_divide_amount'] = $divide_amount;
                $record['servicer_user_name'] = $servicerUser['nickname'] ? $servicerUser['nickname'] : $servicerUser['user_name'];
                $record->delivery_id = $this->delivery_id;
                if (!$record->save()) {
                    $this->flashError($record);
                }
                else {
                    Yii::warning('生成分成成功 delivery_sn = '. $this->delivery_sn. ', order_sn = '. $orderInfo->order_sn. ', group_id = '. $orderInfo->group_id);
                }
            }
        }
    }
}
