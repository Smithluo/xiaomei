<?php
/**
 * Created by PhpStorm.
 * User: Clark
 * Date: 2016-10-19
 * Time: 21:30
 */

namespace api\modules\v1\controllers;

use api\modules\v1\models\AlipayInfo;
use api\modules\v1\models\Integral;
use api\modules\v1\models\OrderGroup;
use api\modules\v1\models\WechatPayInfo;
use api\modules\v1\models\PayLog;
use api\helper\OrderHelper;
use api\modules\v1\models\GoodsActivity;
use api\modules\v1\models\Users;
use api\modules\v1\models\UserAddress;
use api\modules\v1\models\Cart;
use api\modules\v1\models\Goods;
use api\modules\v1\models\GoodsBuyForm;
use api\modules\v1\models\OrderGoods;
use api\modules\v1\models\OrderInfo;
use common\helper\CacheHelper;
use common\helper\CartHelper;
use common\helper\DateTimeHelper;
use common\helper\EventHelper;
use common\helper\NumberHelper;
use common\helper\PaymentHelper;
use common\helper\UrlHelper;
use common\helper\TextHelper;
use common\helper\OrderGroupHelper;
use common\models\ShopConfig;
use Faker\Provider\en_US\PaymentTest;
use \Yii;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\data\ActiveDataProvider;
use yii\rest\Serializer;
use yii\web\ServerErrorHttpException;

defined('WECHAT_PAY_PREPAY_EFFECTIVE_TIME') or define('WECHAT_PAY_PREPAY_EFFECTIVE_TIME', 60 * 60);

class OrderController extends BaseAuthActiveController
{

    public $modelClass = 'api\modules\v1\models\OrderInfo';


    /**
     * 获取订单的综合状态，应用场景不明确
     *
     * @return int|string
     * @throws BadRequestHttpException
     */
    public function actionCs_status_no()
    {
        $status_array = Yii::$app->request->post();

        if (!isset($status_array['order_status']) ||
            !isset($status_array['shipping_status']) ||
            !isset($status_array['pay_status'])
        ) {
            Yii::error('参数错误');
            throw new BadRequestHttpException('参数错误', 1);
        }
        $no = OrderInfo::getOrderCsStatusNo($status_array);
        return $no;
    }

    /**
     * POST order/list   获取订单列表
     *
     * $params = [
     *      type => string, //  【必填】in_array(type, ['all', 'needPay', 'needReceive', 'refuse'])
     *      page => int,    //  页码
     *      pageSize => int, //  每页数量
     * ];
     *
     * return [
     *      count = [
     *          'all' => int
     *          'needPay' => int
     *          'needReceive' => int
     *          'refuse' => int
     *      ],              //  分类别计数
     *      'items' => []   //  要显示的结果集合
     *      '_links' => []  //  分页信息
     *      '_meta' => []   //  页码总数
     * ]
     */
    public function actionList()
    {
        //  【1】初始化基础数据
        $userModel = Yii::$app->user->identity;
        $params = Yii::$app->request->post('data');
        $defaultParams = [
            'page' => 1,
            'pageSize' => 5
        ];
        $params = array_merge($defaultParams, $params);
        $oTb = OrderInfo::tableName();

        //  【2】获取当前查询的结果集
        $query = OrderInfo::find()
            ->joinWith('orderGoods')
            ->where([
                'user_id' => $userModel->user_id
            ]);

        if (!empty($params['type']) || $params['type'] != 'all') {
            if ($params['type'] == 'needPay') {
                //  needPay 订单综合状态：1待支付
                $query->andWhere(OrderInfo::$order_cs_status[OrderInfo::ORDER_CS_STATUS_TO_BE_PAID]);
            } elseif ($params['type'] == 'needReceive') {
                //  needReceive 订单综合状态：5待收货
                $query->andWhere([
                    'or',
                    OrderInfo::$order_cs_status[OrderInfo::ORDER_CS_STATUS_TO_BE_SHIPPED],
                    OrderInfo::$order_cs_status[OrderInfo::ORDER_CS_STATUS_SHIPPED],
                    OrderInfo::$order_cs_status[OrderInfo::ORDER_CS_STATUS_SHIPPED_PART]
                ]);
            } elseif ($params['type'] == 'refuse') {
                //  refuse  订单状态：[7,4,13]退换中
                $query->andWhere([
                    'order_status' => [
                        OrderInfo::ORDER_STATUS_RETURNED,
                        OrderInfo::ORDER_STATUS_ASK_4_REFUND,
                        OrderInfo::ORDER_STATUS_ASK_4_RETURN,
                        OrderInfo::ORDER_STATUS_AGREE_RETURN
                    ]
                ]);
            }

            $query->distinct([$oTb.'.order_id'])
                ->orderBy([
                    'order_id' => SORT_DESC
                ]);
        } else {
            $query->distinct([$oTb.'.order_id'])->orderBy(['order_id' => SORT_DESC]);
        }

        $provider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $params['pageSize'],
                'page' => $params['page'] - 1,
            ]
        ]);

        //  【3】构建provider
        $serializer = new Serializer();
        $serializer->collectionEnvelope = 'items';
        $result = $serializer->serialize($provider);
        $sql =$query->createCommand()->getRawSql();
        Yii::warning(__METHOD__.' SQL: '.$sql);

        //  【4】格式化结果集
        $list = OrderInfo::orderListFormat($result['items'], $params['type']);
        $links = UrlHelper::formatLinks($result['_links']);

        //  【5】获取各类订单的数量
        $count = [];
        $count['all'] = OrderInfo::find()->where([
                'user_id' => $userModel->user_id
            ])->count();
        $count['needPay'] = OrderInfo::find()->where([
                'user_id' => $userModel->user_id
            ])->andWhere(OrderInfo::$order_cs_status[OrderInfo::ORDER_CS_STATUS_TO_BE_PAID])
            ->count();
        $count['needReceive'] = OrderInfo::find()->where([
                'user_id' => $userModel->user_id
            ])->andWhere(OrderInfo::$order_cs_status[OrderInfo::ORDER_CS_STATUS_SHIPPED])
            ->count();
        $count['refuse'] = OrderInfo::find()->where([
                'user_id' => $userModel->user_id
            ])->andWhere([
                'order_status' => [
                    OrderInfo::ORDER_STATUS_RETURNED,
                    OrderInfo::ORDER_STATUS_ASK_4_REFUND,
                    OrderInfo::ORDER_STATUS_ASK_4_RETURN,
                    OrderInfo::ORDER_STATUS_AGREE_RETURN
                ]
            ])->count();

        return [
            'count' => $count,
            'items' => $list,
            '_links' => $links,
            '_meta' => $result['_meta'],
        ];
    }

    /**
     * POST order/group-list   获取分组订单列表
     *
     * $params = [
     *      type => string, //  【必填】in_array(type, ['all', 'needPay', 'needReceive', 'refuse'])
     *      pageIndex => int,    //  页码
     *      pageSize => int, //  每页数量
     * ];
     *
     * return [
     *      count = [
     *          'all' => int
     *          'needPay' => int
     *          'needReceive' => int
     *          'refuse' => int
     *      ],              //  分类别计数
     *      'items' => []   //  要显示的结果集合
     *      '_links' => []  //  分页信息
     *      '_meta' => []   //  页码总数
     * ]
     */
    public function actionGroup_list() {

        $userModel = Yii::$app->user->identity;
        $data = Yii::$app->request->post('data');

        $types = [
            'all',
            'needPay',
            'needReceive',
            'refuse',
        ];

        Yii::info('data = '. VarDumper::export($data), __METHOD__);

        $page = (isset($data['pageIndex']) && is_numeric($data['pageIndex'])) ? $data['pageIndex']: 1;
        $size = (isset($data['pageSize']) && is_numeric($data['pageSize'])) ? $data['pageSize']: 5;
        $type = (isset($data['type']) && in_array($data['type'], $types)) ? $data['type']: 'all';

        Yii::info('page = '. $page. ', size = '. $size. ', type = '. $type, __METHOD__);

        $query = OrderGroup::find()->joinWith([
            'orders orders',
            'orders.brand brand',
            'orders.orderGoods orderGoods',
            'orders.orderGoods.event event',
        ])->where([
            OrderGroup::tableName().'.user_id' => $userModel['user_id'],
        ])->orderBy([
            'create_time' => SORT_DESC,
            'id' => SORT_DESC,
        ])->groupBy('id');

        if ($type === 'needPay') {
            Yii::info('待支付的订单', __METHOD__);
            $query->andWhere([
                'orders.order_status' => [OrderInfo::ORDER_STATUS_UNCONFIRMED, OrderInfo::ORDER_STATUS_CONFIRMED],
                'orders.shipping_status' => OrderInfo::SHIPPING_STATUS_UNSHIPPED,
                'orders.pay_status' => OrderInfo::PAY_STATUS_UNPAYED,
            ]);
        } elseif ($type === 'needReceive') {
            Yii::info('待收货的订单', __METHOD__);
            //  needReceive 订单综合状态：5待收货
            $query->andWhere([
                'orders.order_status' => [
                    OrderInfo::ORDER_STATUS_SPLITED,
                    OrderInfo::ORDER_STATUS_CONFIRMED
                ],
                'orders.pay_status' => OrderInfo::PAY_STATUS_PAYED,
            ]);
        } elseif ($type === 'refuse') {
            Yii::info('退换中的订单', __METHOD__);
            //  refuse  订单状态：[7,4,13]退换中
            $query->andWhere([
                'orders.order_status' => [
                    OrderInfo::ORDER_STATUS_RETURNED,
                    OrderInfo::ORDER_STATUS_ASK_4_REFUND,
                    OrderInfo::ORDER_STATUS_ASK_4_RETURN,
                    OrderInfo::ORDER_STATUS_AGREE_RETURN
                ]
            ]);
        }

        $provider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $size,
                'page' => $page - 1,
            ]
        ]);

        //  【3】构建provider
        $serializer = new Serializer();
        $serializer->collectionEnvelope = 'items';
        $result = $serializer->serialize($provider);

        Yii::info('result = '. VarDumper::export($result), __METHOD__);

        //  【4】格式化结果集
        foreach ($result['items'] as $key => $orderGroup) {
            $result['items'][$key]['orders'] = OrderInfo::orderListFormat($orderGroup['orders'], $type);
        }
//        $list = OrderInfo::orderListFormat($result['items'], $type);
        $links = UrlHelper::formatLinks($result['_links']);


        //  【5】获取各类订单的数量
        $count = [];
        //  全部
        $count['all'] = OrderGroup::find()->where([
            OrderGroup::tableName().'.user_id' => $userModel['user_id'],
        ])->groupBy('id')->count();
        //  待支付
        $count['needPay'] = OrderGroup::find()->joinWith(['orders orders'])->where([
            OrderGroup::tableName().'.user_id' => $userModel['user_id'],
        ])->andWhere([
            'orders.order_status' => [OrderInfo::ORDER_STATUS_UNCONFIRMED, OrderInfo::ORDER_STATUS_CONFIRMED],
            'orders.shipping_status' => OrderInfo::SHIPPING_STATUS_UNSHIPPED,
            'orders.pay_status' => OrderInfo::PAY_STATUS_UNPAYED,
        ])->groupBy('id')->count();
        //  待收货
        $count['needReceive'] = OrderGroup::find()->joinWith(['orders orders'])->where([
            OrderGroup::tableName().'.user_id' => $userModel->user_id
        ])->andWhere([
            'orders.order_status' => [
                OrderInfo::ORDER_STATUS_CONFIRMED,
                OrderInfo::ORDER_STATUS_SPLITED],
            'orders.pay_status' => OrderInfo::PAY_STATUS_PAYED,
        ])->groupBy('id')->count();
        //  退换中
        $count['refuse'] = OrderGroup::find()->joinWith(['orders orders'])->where([
            OrderGroup::tableName().'.user_id' => $userModel->user_id
        ])->andWhere([
            'orders.order_status' => [
                OrderInfo::ORDER_STATUS_RETURNED,
                OrderInfo::ORDER_STATUS_ASK_4_REFUND,
                OrderInfo::ORDER_STATUS_ASK_4_RETURN,
                OrderInfo::ORDER_STATUS_AGREE_RETURN
            ]
        ])->groupBy('id')->count();

        Yii::info('总数：'. VarDumper::export($count), __METHOD__);

        return [
            'count' => $count,
            'items' => $result['items'],
            '_links' => $links,
            '_meta' => $result['_meta'],
        ];
    }

    /**
     * GET order/view?id=$获取指定order_id 获取指定order_id 的商品信息
     *
     * @return array|\common\models\OrderGoods|null
     * @throws BadRequestHttpException
     */
    public function actionView()
    {
        //  【1】初始化基础数据
        $userModel = Yii::$app->user->identity;
        $oTb = OrderInfo::tableName();

        if (Yii::$app->request->isGet) {
            $params = Yii::$app->request->get();
            $orderId = $params['id'];
        } else {
            throw new BadRequestHttpException('非法访问', 1);
        }

        if (empty($orderId)) {
            throw new BadRequestHttpException('无效参数', 2);
        } else {
            $order = OrderInfo::find()->joinWith('orderGoods')
                ->where([
                    'user_id' => $userModel->user_id,
                    $oTb.'.order_id' => $orderId,
                ])->asArray()
                ->one();

            if (empty($order)) {
                throw new BadRequestHttpException('您只能查看自己的订单', 3);
            } else {
                $goodsIdList = array_column($order['orderGoods'], 'goods_id');
                $goodsThumbMap = Goods::getThumbMap($goodsIdList);

                //  格式化商品信息，处理赠品的所属关系
                $order['orderGoods'] = OrderInfo::orderGoodsFormat($order['orderGoods'], $goodsThumbMap);
                $order['csStatus'] = OrderInfo::getOrderCsStatusNo([
                    'order_status'      => $order['order_status'],
                    'shipping_status'   => $order['shipping_status'],
                    'pay_status'        => $order['pay_status'],
                ]);
                return $order;
            }
        }
    }

    /**
     * POST order/cancel    取消订单
     *
     * $params = ['id' => $order_id]
     *
     * @return array
     * @throws BadRequestHttpException
     * @throws ServerErrorHttpException
     */
    public function actionCancel() {
        $userModel = Yii::$app->user->identity;
        $params = Yii::$app->request->post('data');

        if (empty($params['id'])) {
            throw new BadRequestHttpException('无效参数', 1);
        } else {
            $orderId = $params['id'];
            $order = OrderInfo::find()->where([
                    'user_id' => $userModel->user_id,
                    'order_id' => $orderId,
                ])->one();

            if (empty($order)) {
                throw new BadRequestHttpException('您只能取消自己的订单', 2);
            } else {
                //  判断 订单当前是否可以被取消  —— 只有综合状态为待支付的订单才能被取消
                if (
                    in_array($order->order_status, [OrderInfo::ORDER_STATUS_UNCONFIRMED, OrderInfo::ORDER_STATUS_CONFIRMED]) &&
                    $order->shipping_status == OrderInfo::SHIPPING_STATUS_UNSHIPPED &&
                    $order->pay_status == OrderInfo::PAY_STATUS_UNPAYED
                ) {
                    $order->setAttribute('order_status', OrderInfo::ORDER_STATUS_CANCELED);

                    if ($order->save()) {
                        return ['msg' => '订单取消成功'];
                    } else {
                        throw new ServerErrorHttpException(TextHelper::getErrorsMsg($order->errors), 3);
                    }
                } else {
                    if (
                        $order->order_status == OrderInfo::ORDER_STATUS_CANCELED &&
                        $order->shipping_status == OrderInfo::SHIPPING_STATUS_UNSHIPPED &&
                        $order->pay_status == OrderInfo::PAY_STATUS_UNPAYED
                    ) {
                        throw new BadRequestHttpException('当前订单已取消，不可以重复操作', 4);
                    }
                    throw new BadRequestHttpException('当前订单不可以取消', 5);
                }
            }
        }

    }

    /**
     * 订单组取消，只取消可以取消的子订单（未付款）
     * @return array
     * @throws BadRequestHttpException
     * @throws ServerErrorHttpException
     */
    public function actionGroup_cancel() {
        $userModel = Yii::$app->user->identity;
        $params = Yii::$app->request->post('data');

        if (empty($params['group_id'])) {
            throw new BadRequestHttpException('无效参数', 1);
        } else {
            $group_id = $params['group_id'];

            $orderGroup = OrderGroup::findOne([
                'user_id' => $userModel['user_id'],
                'group_id' => $group_id,
            ]);

            if (empty($orderGroup)) {
                throw new BadRequestHttpException('未找到订单', 3);
            }

            $orderList = $orderGroup->orders;

            if (empty($orderList)) {
                throw new BadRequestHttpException('未找到订单', 2);
            } else {
                foreach ($orderList as $order) {
                    //  判断 订单当前是否可以被取消  —— 只有综合状态为待支付的订单才能被取消
                    if (
                        in_array($order->order_status, [OrderInfo::ORDER_STATUS_UNCONFIRMED, OrderInfo::ORDER_STATUS_CONFIRMED]) &&
                        $order->shipping_status == OrderInfo::SHIPPING_STATUS_UNSHIPPED &&
                        $order->pay_status == OrderInfo::PAY_STATUS_UNPAYED
                    ) {
                        $order->order_status = OrderInfo::ORDER_STATUS_CANCELED;

                        if (!$order->save()) {
                            throw new ServerErrorHttpException(TextHelper::getErrorsMsg($order->errors), 3);
                        }
                    }
                }
            }

            $orderGroup->setupOrderStatus();
            $orderGroup->save(false);

            return '修改成功';
        }
    }

    /**
     * POST order/checkout  检验订单
     *
     * $params = [
     *      'flowType' => int   //  【立即购买时为必要参数,默认为普通商品】购买类型 in_array(flowType, [array_keys(Cart::$flowTypeMap)])
     *      'addressId' => int  //  选中的收货地址
     * ]
     *
     * 2017年6月20日 IOS 订单处理同步 PC、微信站 小美直发商品单独成单，计算会员等级优惠信息
     */
    public function actionCheckout()
    {
        //  【0】基础参数
        $userModel = Yii::$app->user->identity;
        $params = Yii::$app->request->post('data');
        $addressId = !empty($params['addressId']) ? $params['addressId'] : 0;
        $order = [];

        $goodsList = [];
//        $canNotBuyGoods = [];   //  购物车中不能被勾选的商品 ——抽离， 给刚进入购物车时判定哪些商品不应该显示复选框
        $addressCheck = 0;      //  用户地址的有效状态。0:没有地址; 1:有地址但信息不完整; 2:有地址并且信息完整
        if (empty($params['flowType'])) {
            $params['flowType'] = Cart::CART_GENERAL_GOODS;
        }

        //  【1】区分购买方式
        //  【1.1】立即购买
        if (!empty($params['goods_id']) && !empty($params['goods_number'])) {
            //  判断商品是否存在
            $goods = Goods::findOne($params['goods_id']);
            if (empty($goods)) {
                throw new BadRequestHttpException('商品不存在', 1);
            }
            //  【立即购买时为必要参数】购买类型
            if (empty($params['flowType'])) {
                $params['flowType'] = Cart::CART_GENERAL_GOODS;
            }
            switch ($params['flowType']) {
                //  判断普通商品是否可购买
                case Cart::CART_GENERAL_GOODS:
                    if ($goods->is_on_sale != Goods::IS_ON_SALE) {
                        throw new BadRequestHttpException('商品已下架', 2);
                    } else {
                        $goods->start_num = OrderHelper::getUserStartNum($userModel->user_rank, $params['goods_id']);
                        //  修正会员等级对应的起售数量后验证库存
                        if ($goods->buy_by_box && $params['goods_number'] % $goods->number_per_box) {
                            //  按箱购买 并且 当前购买数量不是整箱数量    修正数量和价格
                            $maxBoxNum = floor($goods->goods_number / $goods->number_per_box);
                            //  如果最大可购买数量为0，则将商品置库存不足不可购买
                            if ($maxBoxNum) {
                                $canBuyBox = round($params['goods_number'] / $goods->number_per_box);
                                //  如果四舍五入的装箱数 大于库存，则修正为可购买的数量
                                if ($canBuyBox > $maxBoxNum) {
                                    $canBuyBox = $maxBoxNum;
                                }
                                $params['goods_number'] = $canBuyBox * $goods->number_per_box;
                            }
                        }

                        if ($params['goods_number'] > $goods->goods_number) {
                            \Yii::trace(__CLASS__.' | '.__FUNCTION__.' | 库存不足 | goodsId:'.$params['goods_id'].' | goodsNumber:'.$params['goods_number']);
                            throw new BadRequestHttpException('库存不足', 3);
                        } elseif ($params['goods_number'] < $goods->start_num) {
                            \Yii::trace(__CLASS__.' | '.__FUNCTION__.' | 当前购买商品的起售数量为 '.$goods->start_num);
                            throw new BadRequestHttpException('当前购买商品的起售数量为'.$goods->start_num, 4);
                        }
                    }
                    break;

                //  判断团拼活动是否可买 团拼活动不验证用户等级对应的起售数量
                case Cart::CART_GROUP_BUY_GOODS:
                    $groupBuyMap = GoodsActivity::getGroupBuyMap();

                    if (!empty($groupBuyMap[$params['goods_id']])) {
                        $goods_type = 'groupbuy';
                        $actId = $groupBuyMap[$params['goods_id']];

                        $order['extension_code'] = 'group_buy';
                        $order['extension_id'] = $actId;

                        //  修正团拼活动对应的起售数量后验证 库存
                        $goodsActivity = GoodsActivity::find()
                            ->select(['start_num', 'limit_num'])
                            ->where(['act_id' => $actId])
                            ->one();

                        //  修正会员等级对应的起售数量后验证库存
                        if ($goods->buy_by_box && $params['goods_number'] % $goods->number_per_box) {
                            //  按箱购买 并且 当前购买数量不是整箱数量    修正数量和价格
                            $maxBoxNum = floor($goods->goods_number / $goods->number_per_box);
                            //  如果最大可购买数量为0，则将商品置库存不足不可购买
                            if ($maxBoxNum) {
                                $canBuyBox = round($params['goods_number'] / $goods->number_per_box);
                                //  如果四舍五入的装箱数 大于库存，则修正为可购买的数量
                                if ($canBuyBox > $maxBoxNum) {
                                    $canBuyBox = $maxBoxNum;
                                }
                                $params['goods_number'] = $canBuyBox * $goods->number_per_box;
                            }
                        }

                        //  团拼活动的起售数量 修改会 同步修复团拼商品的起售数量
                        if ($params['goods_number'] > $goods->goods_number) {
                            \Yii::trace(__CLASS__.' | '.__FUNCTION__.' | 库存不足 | goodsId:'.$params['goods_id'].' | goodsNumber:'.$params['goods_number']);
                            throw new BadRequestHttpException('库存不足', 3);
                        } elseif ($params['goods_number'] < $goodsActivity->start_num) {
                            \Yii::trace(__CLASS__.' | '.__FUNCTION__.' | 当前购买商品的起售数量为'.$goodsActivity->start_num);
                            throw new BadRequestHttpException('当前购买商品的起售数量为'.$goodsActivity->start_num, 4);
                        } elseif ($params['goods_number'] > $goodsActivity->limit_num) {
                            \Yii::trace(__CLASS__.' | '.__FUNCTION__.' | 当前购买商品的最大购买数量为'.$goodsActivity->limit_num);
                            throw new BadRequestHttpException('当前购买商品的最大购买数量为'.$goodsActivity->limit_num, 5);
                        }
                    } else {
                        \Yii::trace(__CLASS__.' | '.__FUNCTION__.' | 团购活动已结束');
                        throw new BadRequestHttpException('团购活动已结束，下次请早些参与', 6);
                    }
                    break;

                //  判断积分商品是否可购买
                case Cart::CART_INTEGRAL_EXCHANGE_GOODS:
                    if ($goods->is_on_sale != Goods::IS_ON_SALE) {
                        \Yii::trace(__CLASS__.' | '.__FUNCTION__.' | 商品已下架 | goodsId:'.$params['goods_id']);
                        throw new BadRequestHttpException('商品已下架', 2);
                    } else {
                        $goods->start_num = OrderHelper::getUserStartNum($userModel->user_rank, $params['goods_id']);
                        //  修正会员等级对应的起售数量后验证库存
                        if ($goods->need_rank > $userModel->user_rank) {
                            throw new BadRequestHttpException('抱歉，您当前的会员等级不能兑换该商品', 11);
                        } elseif ($userModel->int_balance < $goods->shop_price) {
                            throw new BadRequestHttpException('抱歉，您当前的可用积分不足以兑换该商品', 12);
                        } elseif ($params['goods_number'] > $goods->goods_number) {
                            \Yii::trace(__CLASS__.' | '.__FUNCTION__.' | 库存不足 | goodsId:'.$params['goods_id'].' | goodsNumber:'.$params['goods_number']);
                            throw new BadRequestHttpException('库存不足', 3);
                        } elseif ($params['goods_number'] < $goods->start_num) {
                            \Yii::trace(__CLASS__.' | '.__FUNCTION__.' | 当前购买商品的起售数量为'.$goods->start_num);
                            throw new BadRequestHttpException('当前购买商品的起售数量为'.$goods->start_num, 4);
                        }
                    }
                    break;

                default:
                    \Yii::trace(__CLASS__.' | '.__FUNCTION__.' | 暂时不支持该活动'.$params['flowType']);
                    throw new BadRequestHttpException('暂时不支持该活动', 4);
                    break;
            }

        }
        //  【1.2】购物车提交
        elseif (empty($params['goods_id']) && empty($params['goods_number'])) {
            //  修正购物车中数量不满足起售数量的商品；下架商品、库存不足的商品(数组)不能被选中，返回给调用端
            //  判断购物车是否有可买商品，如果没有，则返回
            $rs = Cart::check($userModel->user_id);
            if (empty($rs['canBuy'])) {
                throw new BadRequestHttpException('您的购物车中没有可购买的商品', 10);
            }
        }
        //  【1.3】缺少参数
        else {
            throw new BadRequestHttpException('缺少必要参数', 7);
        }

        //  【2】如果有实体商品，则需要验证收货人信息
        //  有选择收货人信息则验证指定信息；没有选择则验证默认收货人信息;没有默认收货人信息则设置默认; 没有收获地址则返回0
        //  优惠券是虚拟商品，优惠券上架时补逻辑
        if (empty($params['addressId'])) {
            if (!empty($userModel->address_id)) {
                $hasDefault = UserAddress::checkDeafultAddress($userModel->user_id, $userModel->address_id);
                if ($hasDefault) {
                    $addressId = $userModel->address_id;
                }
            }

            //  如果没有默认收货地址 或 默认收货地址无效，则设置最近添加的地址为默认
            if (!$addressId) {
                $list = UserAddress::getList($userModel->user_id);
                if ($list) {
                    $last = end($list);
                    $addressId = $last->address_id;
                    Users::setDefaultAddress($userModel->user_id, $addressId);
                }
            }
        }

        if ($addressId) {
            //  检验用户选中地址的有效性
            $addressCheck = UserAddress::checkUserAddress($userModel->user_id, $addressId);
        }

        //  【3】取得折扣，计算费用
        //  【3.1】立即购买
        if (!empty($params['goods_id']) && !empty($params['goods_number'])) {
            switch ($params['flowType']) {
                //  判断普通商品是否可购买
                case Cart::CART_GENERAL_GOODS:
                    $goods_Info = Goods::getGoodsForBuy(
                        $params['goods_id'],
                        $params['goods_number'],
                        $userModel->user_rank
                    );

                    if ($goods_Info['code'] == 0) {
                        $goodsList = [$goods_Info];
                    } elseif ($goods_Info['msg']) {
                        \Yii::trace(__CLASS__.' | '.__FUNCTION__.' | '.__LINE__.' | '.$goods_Info['msg']);
                        throw new BadRequestHttpException($goods_Info['msg'], 8);
                    } else {
                        \Yii::trace(__CLASS__.' | '.__FUNCTION__.' | '.__LINE__.' | '.json_encode($params));
                        throw new BadRequestHttpException('参数错误', 9);   //  没有匹配到逻辑
                    }

                    break;
                //  判断团拼活动是否可买 团拼活动不验证用户等级对应的起售数量
                case Cart::CART_GROUP_BUY_GOODS:
                    $groupBuyInfo = Goods::getGroupGoodsForBuy($params['goods_id'], $params['goods_number']);

                    if ($groupBuyInfo['code'] == 0) {
                        $goodsList = [$groupBuyInfo];
                    } elseif ($groupBuyInfo['msg']) {
                        \Yii::trace(__CLASS__.' | '.__FUNCTION__.' | '.__LINE__.' | '.$groupBuyInfo['msg']);
                        throw new BadRequestHttpException($groupBuyInfo['msg'], 8);
                    } else {
                        \Yii::trace(__CLASS__.' | '.__FUNCTION__.' | '.__LINE__.' | '.json_encode($params));
                        throw new BadRequestHttpException('参数错误', 9);   //  没有匹配到逻辑
                    }

                    break;
                //  判断积分商品是否可购买
                case Cart::CART_INTEGRAL_EXCHANGE_GOODS:
                    if ($goods->need_rank > $userModel->user_rank) {
                        throw new BadRequestHttpException('抱歉，您当前的会员等级不能兑换该商品', 11);
                    }

                    $goods_Info = Goods::getGoodsForBuy($params['goods_id'], $params['goods_number'], $userModel->user_rank);
                    if ($goods_Info['code'] == 0) {
                        $goodsList = [$goods_Info];
                    }
                    //  判断用户等级 和积分是否可以 兑换
                    if ($userModel->int_balance < $goods->shop_price) {
                        throw new BadRequestHttpException('抱歉，您当前的可用积分不足以兑换该商品', 12);
                    }
                    break;
                default:
                    break;
            }

        }
        //  【3.2】购物车提交  按供应商拆单、满赠信息
        elseif (empty($params['goods_id']) && empty($params['goods_number'])) {
            //  获取购物车中的商品可能要拆出去   区分获取全部和 已选中的可买
            $goodsList = OrderHelper::cartGoods($userModel->user_id, $userModel->user_rank);
        }

        //  【4】获取总价
        $consignee = UserAddress::find()->where(['address_id' => $addressId])->asArray()->one();
        if (!$consignee['country']) {
            $consignee['country'] = 1;
        }

        $orderRs = OrderHelper::getOrderFee($order, $goodsList, $consignee, $params['flowType']);
        $total = [];
        $update_shipping_goods_list = [];
        if ($orderRs) {
            if (!empty($orderRs['total'])) {
                $total = $orderRs['total'];
                if ($params['flowType'] == Cart::CART_INTEGRAL_EXCHANGE_GOODS) {
                    $total['extension_code'] = Goods::INTEGRAL_EXCHANGE;
                    $total['amount_formated'] = (int)$total['amount_formated'];

                } else {
                    $total['extension_code'] = Goods::GENERAL;
                }
            }

            if (!empty($orderRs['grouped_goods_list'])) {
                foreach ($orderRs['grouped_goods_list'] as $_goods_list) {
                    if (empty($update_shipping_goods_list)) {
                        $update_shipping_goods_list = $_goods_list;
                    } else {
                        $update_shipping_goods_list = array_merge($update_shipping_goods_list, $_goods_list);
                    }
                }
            }
        }

        return [
            'addressCheck'      => $addressCheck,
            'goodsList'         => $update_shipping_goods_list,  //  修正配送信息的 商品列表  为空表示当前没有可购买的商品
//            'goodsList_old'     => $goodsList,  //  --test
//            'grouped_goods_list'     => $orderRs['grouped_goods_list'],  //  --test
            'total'             => $total,  //  费用总计
        ];
    }

    public function actionCheckout_v2() {
        //  【0】基础参数
        $userModel = Yii::$app->user->identity;
        $params = Yii::$app->request->post('data');
        //  收货地址
        $addressId = isset($params['addressId']) && is_numeric($params['addressId'])
            ? $params['addressId']
            : 0;

        $prepay = intval(ArrayHelper::getValue($params, 'prepay', 0), 0);
        $couponId = intval(ArrayHelper::getValue($params, 'coupon_id', 0), 0); //  用户选择的优惠券
        $actId = intval(ArrayHelper::getValue($params, 'act_id', 0), 0);
        $buyGoodsNum = intval(ArrayHelper::getValue($params, 'buy_goods_num', 0), 0);
        $buyGoodsId = intval(ArrayHelper::getValue($params, 'buy_goods_id', 0), 0);
        $pkgId = intval(ArrayHelper::getValue($params, 'pkg_id', 0), 0);
        $pkgNum = intval(ArrayHelper::getValue($params, 'pkg_num', 0), 0);

        $extensionCode = !empty($params['extensionCode']) ? $params['extensionCode']: OrderInfo::EXTENSION_CODE_GENERAL;
        //  没有设置extensiongCode 或 立即购买没有设置商品ID、商品数量的 则跳转到购物车，防止重复提交
        if ($extensionCode != 'general' && (empty($params['buy_goods_num']) || !is_numeric($params['buy_goods_num'])) && (empty($params['pkg_num']) || !is_numeric($params['pkg_num']))) {
            throw new BadRequestHttpException('确认订单失败，请输入正确参数', 1);
        }

        //  【2】验证地址
        $validAddress = OrderGroupHelper::checkAddress($userModel->user_id, $addressId);
        if (empty($validAddress)) {
            //  跳转到完善收货人信息的页面
            throw new BadRequestHttpException('请完善地址信息', 1);
        }

        $extParams = [
            'buy_goods_id' => $buyGoodsId,
            'buy_goods_num' => $buyGoodsNum,
            'pkg_id' => $pkgId,
            'pkg_num' => $pkgNum,
            'act_id' => $actId,
        ];

        //  【3】验证订单
        $rs = OrderGroupHelper::checkoutGoods(
            $userModel['user_id'],
            $extensionCode,
            $validAddress,
            $prepay,
            $couponId,
            $extParams
        );

        $cartGoods = $rs['cartGoods'];
        $total = $rs['total'];

        //  如果结算商品信息为空，跳转到购物车
        if (empty($cartGoods)) {
            throw new BadRequestHttpException('购物车为空', 2);
        } elseif ($extensionCode == 'integral_exchange' && $total['canBuy'] == false) {
            throw new BadRequestHttpException('积分商品无法购买', 3);
        }

        //  【4】优惠券
        $selectedCoupon = [];
        //  只有购物车、普通商品的立即购买 可以使用优惠券
        if (in_array($extensionCode, ['general_buy_now', 'general'])) {
            $selectedCouponId = $couponId;
            if (!empty($total['selectedCut']['coupon_id']) && $total['selectedCut']['coupon_id'] == $selectedCouponId) {
                $selectedCoupon = $total['selectedCut'];
            }
        }

        return [
                'goodsList' => $cartGoods,
                'total' => $total,
                'selectedCoupon' => $selectedCoupon,
            ];
    }

    /**
     * POST [
     *      params => [],
     *      extParams => [],
     * ]
     * @return mixed
     * @throws BadRequestHttpException
     * @throws ServerErrorHttpException
     */
    public function actionCreate_v2() {
        //  【1】验证参数
        $userModel = Yii::$app->user->identity;
        if (empty($userModel)) {
            throw new BadRequestHttpException('非法访问', 21);
        }
        $data = Yii::$app->request->post('data');
        Yii::warning('入参 $data = '.json_encode($data));
        if (empty($data['params']) || empty($data['params']['extensionCode'])) {
            throw new BadRequestHttpException('缺少必要参数', 1);
        } elseif (
            $data['params']['extensionCode'] != 'general'
            && empty($data['extParams']['buy_goods_num'])
            && empty($data['extParams']['pkg_id'])
        ) {
            throw new BadRequestHttpException('立即购买缺少必要参数', 2);
        } else {
            $params = $data['params'];
            $extParams = $data['extParams'];
        }

        //  设默认值
        $paramsDefault = [
            'addressId' => 0,
            'prepay' => 0,
            'couponId' => 0,
            'postscript' => '',
            'referer' => '',
            'from_ad' => 0,
        ];
        $params = array_merge($paramsDefault, $params);
        $extParamsDefault = [
            'buy_goods_id' => 0,
            'buy_goods_num' => 0,
            'pkg_id' => 0,
            'pkg_num' => 0,
            'act_id' => 0,
        ];
        $extParams = array_merge($extParamsDefault, $extParams);
        

        //  【2】验证地址
        $validAddress = OrderGroupHelper::checkAddress($userModel->user_id, $params['addressId']);
        if (empty($validAddress)) {
            if (empty($params['addressId'])) {
                throw new BadRequestHttpException('请选择有效的收获地址', 3);
            } else {
                throw new ServerErrorHttpException('请把您选择的收获地址补充完整', 4);
            }
        } else {
            $return['validAddress'] = $validAddress;
        }

        //  【3】验证订单 已处理过库存校验
        $rs = OrderGroupHelper::checkoutGoods(
            $userModel->user_id,
            $params['extensionCode'],
            $validAddress,
            $params['prepay'],
            $params['couponId'],
            $extParams
        );
        $cartGoods  = $rs['cartGoods'];
        $total      = $rs['total'];

        if (empty($cartGoods)) {
            switch ($params['extensionCode']) {
                case 'general':
                    throw new BadRequestHttpException('您的购物车中没有可结算的商品', 5);
                    break;
                case 'general_buy_now':
                    throw new BadRequestHttpException('商品库存不足，请修改购买数量', 6);
                    break;
                case 'group_buy':
                    throw new BadRequestHttpException('购买数量超出当前活动商品的累计可购买数量', 7);
                    break;
                case 'flash_sale':
                    throw new BadRequestHttpException('购买数量超出当前活动商品的最大可购买数量', 8);
                    break;
                case 'gift_pkg':
                    throw new BadRequestHttpException('礼包库存不足', 10);
                    break;

                default :
                    throw new ServerErrorHttpException('下单失败，请重试', 11);
                    break;
            }
        } else {
            $platFormDefaultPayment = Yii::$app->params['platFormDefaultPayment'];
            $platFormMap = array_keys($platFormDefaultPayment);
            if (empty($params['platform']) || !in_array($params['platform'], $platFormMap)) {
                throw new BadRequestHttpException('缺少必要参数', 12);
            } else {
                //  支付方式
                $defaultPayId = OrderHelper::getPaymentId($platFormDefaultPayment[$params['platform']], $params['platform']);
                if ($defaultPayId > 0) {
                    $paymentMap = Yii::$app->params['paymentMap'];
                    $payName = $paymentMap[$defaultPayId];
                }
            }
        }

        //  生成总单
        if (!empty($total['selectedCut']['cut'])) {
            $orderGroupEventId = $total['selectedCut']['event_id'];
            $orderGroupRuleId = $total['selectedCut']['rule_id'];
        } else {
            $orderGroupEventId = 0;
            $orderGroupRuleId = 0;
        }
        $order_uniq_id = OrderGroupHelper::getUniqidGroupId($userModel->user_id);
        $return['order_uniq_id'] = $order_uniq_id;
        //  遍历生成子单
        if (!empty($cartGoods) && !empty($total)) {
            //  先生成总单， 总单的主键ID有写入到 子单中
            $transaction = ActiveRecord::getDb()->beginTransaction();
            try {
                $totalAmount = NumberHelper::price_format($total['totalAmount']);
                $gmtime = DateTimeHelper::gmtime();

                $orderGroup = new OrderGroup();
                $orderGroup['group_id'] = (string)$order_uniq_id;
                $orderGroup['user_id'] = (int)$userModel->user_id;
                $orderGroup['create_time'] = DateTimeHelper::gmtime();
                $orderGroup['group_status'] = OrderGroup::ORDER_GROUP_STATUS_UNPAY;
                $orderGroup['event_id'] = (int)$orderGroupEventId;
                $orderGroup['rule_id'] = (int)$orderGroupRuleId;

                //  收货人信息
                foreach ($validAddress as $key => $value) {
                    if (in_array($key, [
                        'consignee',
                        'mobile',
                        'country',
                        'province',
                        'city',
                        'district',
                        'address',
                        'mobile',
                    ])) {
                        $orderGroup[$key] = addslashes($value);
                    }
                }

                $orderGroup['pay_id'] = 0;
                $orderGroup['pay_name'] = '未支付';
                $orderGroup['goods_amount'] = NumberHelper::price_format($total['goods_amount']);
                $orderGroup['shipping_fee'] = NumberHelper::price_format($total['shippingFee']);
                $orderGroup['money_paid'] = 0.00;
                $orderGroup['order_amount'] = NumberHelper::price_format($totalAmount);

                $orderGroup['pay_time'] = 0;
                $orderGroup['shipping_time'] = 0;
                $orderGroup['recv_time'] = 0;
                $orderGroup['discount'] = NumberHelper::price_format($total['discount']);
                $orderGroup['event_id'] = (int)$orderGroupEventId;
                $orderGroup['rule_id'] = (int)$orderGroupRuleId;

                if (!$orderGroup->save()) {
                    throw new ServerErrorHttpException('总单入库失败', 1);
                    Yii::warning(__LINE__.'总单入库失败: $orderGroup = '.VarDumper::export($orderGroup).
                        '; $orderGroup->errors = '.VarDumper::export($orderGroup->errors), __METHOD__);
                }

                //  如果使用了优惠券，修改优惠券状态
                if (
                    !empty($useCouponId) &&
                    !empty($orderGroupEventId) &&
                    !empty($total['selectedCut']['coupon_id']) &&
                    $total['selectedCut']['coupon_id'] == $useCouponId
                ) {
                    $updateRs = CouponRecord::updateAll(
                        [
                            'used_at' => $orderGroup['create_time'],
                            'group_id' => $orderGroup['group_id'],
                            'status' => CouponRecord::COUPON_STATUS_USED,
                        ],
                        [
                            'coupon_id' => $useCouponId,
                            'user_id' => $userModel->user_id,
                        ]
                    );
                    if ($updateRs) {
                        Yii::warning('优惠券使用状态修改成功 group_id：' . $orderGroup['group_id'] . ' ; coupon_id = ' . $useCouponId, __METHOD__);
                    } else {
                        Yii::warning('优惠券使用状态修改失败 group_id：' . $orderGroup['group_id'] . ' ; coupon_id = ' . $useCouponId, __METHOD__);
                    }
                }

                //  设置订单的基础数据
                $orderBase = [
                    'group_id'          => $orderGroup->group_id,
                    'group_identity'    => (int)$orderGroup->id,
                    'user_id'           => (int)$orderGroup->user_id,

                    'order_status'      => (int)OrderInfo::ORDER_STATUS_UNCONFIRMED,
                    'shipping_status'   => (int)OrderInfo::SHIPPING_STATUS_UNSHIPPED,
                    'pay_status'        => (int)OrderInfo::PAY_STATUS_UNPAYED,

                    'consignee'         => (string)$orderGroup->consignee,
                    'country'           => (int)$orderGroup->country,
                    'province'          => (int)$orderGroup->province,
                    'city'              => (int)$orderGroup->city,
                    'district'          => (int)$orderGroup->district,
                    'address'           => (string)$orderGroup->address,
                    'zipcode'           => (string)$validAddress->zipcode,
                    'tel'               => (string)$validAddress->tel,
                    'mobile'            => (string)$validAddress->mobile,
                    'email'             => (string)$validAddress->email,
                    'best_time'         => (string)$validAddress->best_time,
                    'sign_building'     => (string)$validAddress->sign_building,

                    'pay_id'            => (int)$defaultPayId,
                    'pay_name'          => (string)$payName,
                    'how_oos'           => '',
                    'how_surplus'       => '',
                    'pack_name'         => '',
                    'card_name'         => '',
                    'card_message'      => '',
                    'insure_fee'        => 0.00,  //  保价
                    'pay_fee'           => 0.00,  //  支付手续费
                    'pack_fee'          => 0.00,
                    'card_fee'          => 0.00,
                    'money_paid'        => 0.00,    //  默认未支付（未启用红包），积分兑换没走这个流程
                    'surplus'           => 0.00,    //  未启用余额支付
                    'integral'          => 0,       //  未启用积分支付
                    'integral_money'    => 0.00,    //  未启用积分支付
                    'bonus'             => 0.00,    //  未启用红包

                    'inv_payee'         => '',  //  发票费用
                    'inv_content'       => '',  //  发票明细
                    'inv_type'          => '',  //  发票类型
                    'tax'               => 0.00,  //  税额

                    'from_ad'           => 0,    //  站内广告引流
                    'referer'           => '', //  外部来源，未启用
                    'add_time'          => (int)$gmtime,     //  与总单保持一致
                    'confirm_time'      => 0,           //  支付时修正确认时间
                    'recv_time'         => 0,

                    'pack_id'           => 0,
                    'card_id'           => 0,
                    'bonus_id'          => 0,
                    'invoice_no'        => '',  //  发票编号 未启用
                    'to_buyer'          => '',
                    'pay_note'          => '',
                    'agency_id'         => 0,   //  当前未启用代理机构
                    'is_separate'       => 0,   //  是否已分成
                    'parent_id'         => 0,   //  推荐人 ID  未启用分销

                    'discount'          => 0,   //  优惠金额，默认0
                    'mobile_pay'        => 0,   //  是否移动端支付 支付时修正
                    'mobile_order'      => 0,   //  是否移动端下单
                ];

                //  子单入库
                foreach ($cartGoods as $item) {
                    $order = $orderBase;

                    $order['order_sn'] = OrderGroupHelper::getUniqueOrderSn();
                    $order['postscript']    = (string)$params['postscript'];    //  区分具体订单的备注，并插入订单商品的物料配比
                    $order['shipping_id']   = $item['shipping_id'] ?: 3;
                    $order['shipping_name'] = $item['shipping_name'] ? (string)$item['shipping_name'] : '到付';
                    $order['shipping_fee']  = !empty($params['prepay']) ? $item['shipping_fee'] : 0.00;   // 用户选择现付运费 才入库

                    //  修正 小美支付未选中现付运费 的入库参数
                    if ($order['shipping_id'] == 4 && empty($order['shipping_fee'])) {
                        $order['shipping_id'] = 3;
                        $order['shipping_name'] = '到付';   //  小美直发(运费到付)
                    }

                    $order['goods_amount']  = $item['brandGoodsAmount'];

                    if ($params['extensionCode'] == 'gift_pkg') {
                        $order['discount'] = $orderGroup['discount'];
                        $order['order_amount'] = NumberHelper::price_format($orderGroup['order_amount']);
                    } else {
                        $order['discount']      = $item['discount'] ? NumberHelper::price_format($item['discount']) : 0;
                        $orderAmount = $order['goods_amount'] + $order['shipping_fee'] - $order['discount'];
                        $order['order_amount']  = NumberHelper::price_format($orderAmount);
                    }

                    $order['extension_code']= $params['extensionCode'] ? (string)$params['extensionCode'] : '';
                    $order['extension_id']  = $extParams['act_id'] ? (int)$extParams['act_id'] : 0;

                    $order['brand_id']      = (int)$item['brand_id'];
                    $order['supplier_user_id']  = (int)$item['supplier_user_id'];
                    $order['pay_id'] = 0;
                    $order['pay_name'] = '';

                    $return['orderList'][] = $order;

                    $orderModel = new OrderInfo();
                    $orderModel->setAttributes($order);
                    if ($orderModel->save()) {
                        $order_id = $orderModel->order_id;
                        //  子单入库获取到 order_id， 遍历订单商品入库
                        foreach ($item['goodsList'] as $goods) {
                            $goods['order_id'] = $order_id;
                            $orderGoods = new OrderGoods();
                            $orderGoods->setAttributes($goods);

                            if (!$orderGoods->save()) {
                                Yii::warning(__LINE__.'订单商品入库失败: $goods = '.json_encode($goods).
                                    '; $orderGoods->errors = '.json_encode($orderGoods->errors), __METHOD__);
                                throw new ServerErrorHttpException('订单商品入库失败', 14);
                            }

                            //  赠品入库
                            if (!empty($goods['gift']) && $goods['gift']['goods_number'] > 0) {
                                $goods['gift']['order_id'] = $order_id;

                                $orderGoodsGift = new OrderGoods();
                                $orderGoodsGift->setAttributes($goods['gift']);

                                if (!$orderGoodsGift->save()) {
                                    Yii::warning(__LINE__.'订单商品入库失败: $goods.gift = '.json_encode($goods['gift']).
                                        '; $orderGoodsGift->errors = '.json_encode($orderGoodsGift->errors), __METHOD__);
                                    throw new ServerErrorHttpException('订单赠品入库失败', 15);
                                }
                            }

                            //  赠品入库
                            if (!empty($goods['gift'])) {
                                foreach ($goods['gift'] as $gift) {
                                    if ($gift['goods_number'] > 0) {
                                        $gift['order_id'] = $order_id;

                                        $orderGoodsGift = new OrderGoods();
                                        $orderGoodsGift->setAttributes($gift);

                                        if (!$orderGoodsGift->save()) {
                                            Yii::warning(__LINE__.'订单赠品入库失败: $gift = '.json_encode($gift).
                                                '; $orderGoodsGift->errors = '.json_encode($orderGoodsGift->errors), __METHOD__);
                                            throw new ServerErrorHttpException('订单赠品入库失败', 15);
                                        }
                                    }
                                }
                            }

                            //  物料入库
                            if (!empty($goods['wuliaoList'])) {
                                foreach ($goods['wuliaoList'] as $wuliao) {
                                    if ($wuliao['goods_number'] > 0) {
                                        $wuliao['order_id'] = $order_id;

                                        $orderGoodsGift = new OrderGoods();
                                        $orderGoodsGift->setAttributes($wuliao);

                                        if (!$orderGoodsGift->save()) {
                                            Yii::warning(__LINE__.'订单物料入库失败: $wuliao = '.json_encode($wuliao).
                                                '; $orderGoodsGift->errors = '.json_encode($orderGoodsGift->errors), __METHOD__);
                                            throw new ServerErrorHttpException('订单物料入库失败', 15);
                                        }
                                    }
                                }
                            }
                        }

                        //  支付记录入库
                        $payLog = new PayLog();
                        $payLog->order_id = $orderModel->order_id;
                        $payLog->order_amount = $orderModel->order_amount;
                        $payLog->order_type = 0;
                        $payLog->is_paid = 0;
                        if (!$payLog->save()) {
                            Yii::warning(__LINE__ . ' pay_log 支付记录入库失败 $payLog = ' . json_encode($payLog) .
                                '; $payLog->errors = ' . json_encode($orderModel->errors));
                            throw new ServerErrorHttpException('支付记录入库失败', 16);
                        }
                    } else {
                        Yii::warning(__LINE__ . ' 订单入库失败 $order = ' . json_encode($order) .
                            '; $orderModel = ' . json_encode($orderModel) .
                            '; $orderModel->errors = ' . json_encode($orderModel->errors), __METHOD__);
                        throw new ServerErrorHttpException('子单入库失败', 17);
                    }
                }

                $orderGroup->syncFeeInfo();
                if (!$orderGroup->save()) {
                    Yii::warning(__LINE__ . ' pay_log 支付记录入库失败 $payLog = ' . json_encode($payLog) .
                        '; $payLog->errors = ' . json_encode($orderModel->errors));
                    throw new ServerErrorHttpException('总单信息同步失败', 18);
                }

                //  通过购物车购买 成单后 清空采购车
                if ($params['extensionCode'] == 'general') {
                    OrderGroupHelper::clearCart($userModel->user_id);
                }

                $transaction->commit();
                $return['amount'] = NumberHelper::price_format($orderGroup['order_amount']);
            } catch (\Exception $exception) {
                $transaction->rollBack();
                Yii::warning('创建订单失败 $exception = '.VarDumper::dumpAsString($exception).PHP_EOL.' json_encode $exception = '
                    .json_encode($exception->getTrace()), __METHOD__);
                $transaction->rollBack();
                switch ($params['platform']) {
                    case 'm':
                        $return['redirect'] = '/default/flow/index.html';
                        break;
                    case 'pc':
                        $return['redirect'] = '/flow.php';
                        break;
                    default :
                        break;
                }
                throw new ServerErrorHttpException('创建订单失败', 19);
            } catch (\Throwable $throwable) {
                Yii::warning('创建订单失败 $throwable = '.VarDumper::dumpAsString($throwable), __METHOD__);
                $transaction->rollBack();
                throw new ServerErrorHttpException('创建订单失败', 20);
            }
        }

        Yii::warning(' 返回参数 $return = '.json_encode($return), __METHOD__);
        return $return;
    }

    /**
     * POST order/cart-create  从购物车下单
     *
     * 从购物车结算
     *      from_type => string, //  【必填】in_array($platForm, ['m', 'pc', 'ios', 'android'])
     * $data = [
     *      addressId => int,   //  选中的收货地址
     *      postscript => string    //  订单留言
     * ]
     *
     * 【1】checkout  没有地址则返回 完善地址，没有可购买商品则返回原因，  配送方式
     * 【2】拆分订单，
     * 【3】积分、红包、余额、
     * 【4】订单入库（团拼等活动商品要设置 extension_code, extension_id） 如果下单减库存，则减库存。如果是通过购物车购买则清空购物车
     * 【5】短信、邮件、微信提醒
     * 【6】支付方式、支付日志
     * 【7】如果没有实体商品，修改发货状态
     */
    public function actionCart_create() {
        //  【0】基础参数
        $userModel = Yii::$app->user->identity;
        $params = Yii::$app->request->post('data');
        $from_type = Yii::$app->request->post('from_type');
        $flowType = Cart::CART_GENERAL_GOODS;
//        $goods_type = 'goods';
        $addressId = !empty($params['addressId']) ? $params['addressId'] : 0;
        //  获取平台对应的默认支付方式
        $platFormDefaultPayment = Yii::$app->params['platFormDefaultPayment'];
        $platFormMap = array_keys($platFormDefaultPayment);
        if (empty($from_type) || !in_array($from_type, $platFormMap)) {
            throw new BadRequestHttpException('缺少必要参数', 1);
        } else {
            //  支付方式
            $payId = OrderHelper::getPaymentId($platFormDefaultPayment[$from_type], $from_type);
            if ($payId > 0) {
                $paymentMap = Yii::$app->params['paymentMap'];
                $payName = $paymentMap[$payId];
            }
        }
        $order = [];
        //  多个品牌一次下单 由调用端拼接好备注 插入到多有订单中
        $postscript = isset($params['postscript']) ? trim($params['postscript']) : '';
        $userId = $userModel->user_id;
        $fromCart = 1;

        //  【1】实体商品需要验证收货人信息
        $addressId = $this->checkAddress($addressId, $userId, $userModel->address_id);

        //  【2】获取购物车中的可购买商品, 如果没有，则返回; 如果有，则判断是否参与了活动
        $goodsList = OrderHelper::cartGoods($userId, $userModel->user_rank);
        if (empty($goodsList)) {
            throw new BadRequestHttpException('您的购物车中没有可购买的商品', 4);
        }

        //  【3】拆分订单  优先供应商、其次品牌商 分组
        $consignee = UserAddress::find()->where(['address_id' => $addressId])->asArray()->one();
        if (!$consignee['country']) {
            $consignee['country'] = 1;
        }
        $orderRs = OrderHelper::getOrderFee($order, $goodsList, $consignee, $flowType);

        if (empty($orderRs)) {
            throw new ServerErrorHttpException('服务器出错，请重试', 5);
        }
        $total = $orderRs['total'];
        $grouped_goods_list = $orderRs['grouped_goods_list'];

        //  如果使用库存，且下订单时减库存，则减少库存
        $shopConfigParams = CacheHelper::getShopConfigParams(['use_storage', 'stock_dec_time']);
        if ($shopConfigParams['use_storage']['value'] == 1 && $shopConfigParams['stock_dec_time']['value'] == 1) {
            $changeGoodsNumber = true;
        } else {
            $changeGoodsNumber = false;
        }

        //  【4】判断是否有参与活动(满赠满减)
        $fullCutMap = [];
        $goodsMap = CartHelper::getSelectedMap($userId);
        $validEvents = EventHelper::formatValidEvents($goodsMap, $userId);
        if ($validEvents) {
            $fullCut = current($validEvents['fullCut']);

            //  修正商品、订单的结算价格
            if ($fullCut['cut'] > 0) {
                $fullCutMap = EventHelper::assignFullCut($fullCut, $grouped_goods_list);
            }
        }

        //  【5】优先供应商、其次品牌商 拆单
        $order_uniq_id = OrderGroupHelper::getUniqidGroupId($userId);  //  会被拆分成多个订单的 设置一个唯一的总单号
        $addTime = DateTimeHelper::getFormatGMTTimesTimestamp();
        $order_done = [];

        //  初始化订单信息
        $order = [
            'pay_id'          => $payId,
            'pay_name'        => $payName,
            'consignee'       => $consignee['consignee'],
            'country'         => $consignee['country'],
            'province'        => $consignee['province'],
            'city'            => $consignee['city'],
            'district'        => $consignee['district'],
            'address'         => $consignee['address'],
            'zipcode'         => $consignee['zipcode'],
            'tel'             => $consignee['tel'],
            'mobile'          => $consignee['mobile'],
            'pack_id'         => 0,
            'card_id'         => 0,
            'card_message'    => '当前不支持贺卡',
            'surplus'         => $total['surplus'],  //  用户可用余额
            'integral'        => 0, //  积分
            'bonus_id'        => $total['bonus'], //  红包ID
//            'discount'        => 0.00,  //  减免的金额，不是享受的折扣
            'tax'             => 0.00,
            'need_inv'        => 0, //  是否需要发票
            'inv_type'        => '无',
            'inv_payee'       => '',    //  发票抬头
            'inv_content'     => '',    //  发票内容
            'how_oos'         => '',    //  缺货处理方式
            'need_insure'     => 0, //  是否保价
            'user_id'         => $userId,
            'order_status'    => OrderInfo::ORDER_STATUS_UNCONFIRMED,
            'shipping_status' => OrderInfo::SHIPPING_STATUS_UNSHIPPED,
            'pay_status'      => OrderInfo::PAY_STATUS_UNPAYED,
            'group_id'        => $order_uniq_id,
            'agency_id'       => 0,   //  办事处 ID
            'postscript'      => $postscript,   //  留言，给所有的订单都留同样的留言
            'add_time'        => $addTime,
            'shipping_fee'    => 0.00,
            'insure_fee'      => 0.00,
            'card_name'       => '',
            'card_fee'        => $total['card_fee'],    //  祝福贺卡
            'pack_fee'        => $total['pack_fee'],    //  商品包装
            'pay_fee'         => $total['pay_fee'],
            'cod_fee'         => $total['cod_fee'],
            'from_ad'         => 0,    //  来源——广告位
            'referer'         => '',    //  来源——页面
            'parent_id'       => 0,    //  不启用分成功能
            'extension_code'  => '',
            'extension_id'      => 0,
        ];

        $shopConfigParams = CacheHelper::getShopConfigParams(['use_storage', 'stock_dec_time']);
        if ($shopConfigParams['use_storage']['value'] == 1 && $shopConfigParams['stock_dec_time']['value'] == 1) {
            $changeGoodsNumber = true;
        } else {
            $changeGoodsNumber = false;
        }

        $this->processMutiOrder(
            $grouped_goods_list,
            $userId,
            $order_uniq_id,
            $order,
            $changeGoodsNumber,
            $fromCart,
            $order_done
        );

        return $order_done;
    }

    /**
     * POST order/general-create  立即购买普通商品
     *
     * 从购物车结算
     *      from_type => string, //  【必填】in_array($platForm, ['m', 'pc', 'ios', 'android'])
     * $data = [
     *      goods_id => int,        //  【必填】商品ID
     *      goods_number => int,   //  【必填】购买数量
     *      addressId => int,       //  选中的收货地址
     *      postscript => string    //  订单留言
     * ]
     *
     * 普通商品立即购买
     */
    public function actionGeneral_create()
    {
        //  【0】基础参数
        $userModel = Yii::$app->user->identity;
        $params = Yii::$app->request->post('data');
        $from_type = Yii::$app->request->post('from_type');
        $order_type = 0;    //  按品牌拆单
        $addressId = !empty($params['addressId']) ? $params['addressId'] : 0;
        //  获取平台对应的默认支付方式
        $platFormDefaultPayment = Yii::$app->params['platFormDefaultPayment'];
        $platFormMap = array_keys($platFormDefaultPayment);
        if (empty($from_type) || !in_array($from_type, $platFormMap) || empty($params['goods_id']) || empty($params['goods_number'])) {
            throw new BadRequestHttpException('缺少必要参数', 1);
        } else {
            //  支付方式
            $payId = OrderHelper::getPaymentId($platFormDefaultPayment[$from_type], $from_type);
            if ($payId > 0) {
                $paymentMap = Yii::$app->params['paymentMap'];
                $payName = $paymentMap[$payId];
            }
        }
        $order_done = [];
        $postscript = isset($params['postscript']) ? trim($params['postscript']) : '';
        $userId = $userModel->user_id;
        $fromCart = 0;

        //  【1】实体商品需要验证收货人信息
        try {
            $addressId = $this->checkAddress($addressId, $userId, $userModel->address_id);
        } catch (BadRequestHttpException $e) {
            throw $e;
        }

        //  【2】获取待入库的商品信息
        $goods_Info = Goods::getGoodsForBuy(
            $params['goods_id'],
            $params['goods_number'],
            $userModel->user_rank
        );
        if ($goods_Info['code'] == 0) {
            $goodsList = [$goods_Info];
        } elseif ($goods_Info['msg']) {
            \Yii::trace(__CLASS__.' | '.__FUNCTION__.' | '.__LINE__.' | '.$goods_Info['msg']);
            throw new BadRequestHttpException($goods_Info['msg'], 8);
        } else {
            \Yii::trace(__CLASS__.' | '.__FUNCTION__.' | '.__LINE__.' | '.json_encode($params));
            throw new BadRequestHttpException('参数错误', 9);   //  没有匹配到逻辑
        }

        //  【3】获取待入库的订单信息
        $consignee = UserAddress::find()->where(['address_id' => $addressId])->asArray()->one();

        if (!$consignee['country']) {
            $consignee['country'] = 1;
        }

        //  判定是否要减库存——如果使用库存，且下订单时减库存，则减少库存
        $shopConfigParams = CacheHelper::getShopConfigParams(['use_storage', 'stock_dec_time']);
        if ($shopConfigParams['use_storage']['value'] == 1 && $shopConfigParams['stock_dec_time']['value'] == 1) {
            $changeGoodsNumber = true;
        } else {
            $changeGoodsNumber = false;
        }

        $order_uniq_id = OrderGroupHelper::getUniqidGroupId($userId);  //  会被拆分成多个订单的 设置一个唯一的总单号
        $addTime = DateTimeHelper::getFormatGMTTimesTimestamp();

        //  初始化订单信息
        $order = [
            'pay_id'          => $payId,
            'pay_name'        => $payName,
            'consignee'       => $consignee['consignee'],
            'country'         => $consignee['country'],
            'province'        => $consignee['province'],
            'city'            => $consignee['city'],
            'district'        => $consignee['district'],
            'address'         => $consignee['address'],
            'zipcode'         => $consignee['zipcode'],
            'tel'             => $consignee['tel'],
            'mobile'          => $consignee['mobile'],
            'pack_id'         => 0,
            'card_id'         => 0,
            'card_message'    => '当前不支持贺卡',
            'surplus'         => 0.00,  //  用户可用余额
            'integral'        => 0, //  积分
            'bonus_id'        => 0, //  红包ID
            'discount'        => 0.00,  //  减免的金额，不是享受的折扣
            'tax'             => 0.00,
            'need_inv'        => 0, //  是否需要发票
            'inv_type'        => '无',
            'inv_payee'       => '',    //  发票抬头
            'inv_content'     => '',    //  发票内容
            'how_oos'         => '',    //  缺货处理方式
            'need_insure'     => 0, //  是否保价
            'user_id'         => $userId,
            'order_status'    => OrderInfo::ORDER_STATUS_UNCONFIRMED,
            'shipping_status' => OrderInfo::SHIPPING_STATUS_UNSHIPPED,
            'pay_status'      => OrderInfo::PAY_STATUS_UNPAYED,
            'group_id'        => $order_uniq_id,
            'agency_id'       => 0,   //  办事处 ID
            'postscript'      => $postscript,   //  留言，给所有的订单都留同样的留言
            'add_time'        => $addTime,
            'shipping_fee'    => 0.00,
            'insure_fee'      => 0.00,
            'card_name'       => '',
            'card_fee'        => 0.00,    //  祝福贺卡
            'pack_fee'        => 0.00,    //  商品包装
            'pay_fee'         => 0.00,
            'cod_fee'         => 0.00,
            'from_ad'         => 0,    //  来源——广告位
            'referer'         => '',    //  来源——页面
            'parent_id'       => 0,    //  不启用分成功能
            'extension_code'  => '',
            'extension_id'      => 0,
        ];

        //  【4】判断是否有参与活动(满赠满减)
        $goodsMap = [
            [
                'goods_id'      => $params['goods_id'],
                'goods_number'  => $params['goods_number'],
                'selected'      => 1,
            ]
        ];
        $validEvents = EventHelper::formatValidEvents($goodsMap, $userId);
        if ($validEvents) {
            $fullCut = current($validEvents['fullCut']);

            //  修正商品、订单的结算价格
            if ($fullCut['cut'] > 0) {
                $grouped_goods_list[] = $goodsList;
                $fullCutMap = EventHelper::assignFullCut($fullCut, $grouped_goods_list);
                $goodsList = $grouped_goods_list[0];

                //  修正订单金额
                if (!empty($fullCutMap) && current($fullCutMap)['discount'] > 0) {
                    $order['discount'] = NumberHelper::price_format($order['discount'] + current($fullCutMap)['discount']);
                }
            }
        }

        //  【5】订单处理
        $this->processSingleOrder(
            $goodsList,
            $order_type,
            $goods_Info['brand_id'],
            $userId,
            $changeGoodsNumber,
            $order_uniq_id,
            $order,
            $fromCart,
            $order_done
        );

        return $order_done;
    }

    /**
     * POST order/groupbuy-create  立即购买团拼商品
     *
     * 从购物车结算
     *      from_type => string, //  【必填】in_array($platForm, ['m', 'pc', 'ios', 'android'])
     * $data = [
     *      addressId => int,   //  选中的收货地址
     *     'goods_id' => int   //  【必填】立即购买的商品
     *     'goods_number' => int  //  【必填】立即购买的数量
     *      postscript => string    //  订单留言
     * ]

     * @return array
     * @throws BadRequestHttpException
     */
    public function actionGroupbuy_create()
    {
        //  【0】基础参数
        $userModel = Yii::$app->user->identity;
        $params = Yii::$app->request->post('data');
        if (empty($params['actId'])) {
            throw new BadRequestHttpException('请指定要参与的团采活动', 1);
        } else {
            $actId = $params['actId'];
        }
        $from_type = Yii::$app->request->post('from_type');
        $order_type = 0;    //  按品牌拆单
        $addressId = !empty($params['addressId']) ? $params['addressId'] : 0;
        //  获取平台对应的默认支付方式
        $platFormDefaultPayment = Yii::$app->params['platFormDefaultPayment'];
        $platFormMap = array_keys($platFormDefaultPayment);
        if (empty($from_type) || !in_array($from_type, $platFormMap) || empty($params['goods_id']) || empty($params['goods_number'])) {
            throw new BadRequestHttpException('缺少必要参数', 1);
        } else {
            //  支付方式
            $payId = OrderHelper::getPaymentId($platFormDefaultPayment[$from_type], $from_type);
            if ($payId > 0) {
                $paymentMap = Yii::$app->params['paymentMap'];
                $payName = $paymentMap[$payId];
            }
        }
        $order_done = [];
        $postscript = isset($params['postscript']) ? trim($params['postscript']) : '';
        $userId = $userModel->user_id;
        $fromCart = 0;


        //  【1】实体商品需要验证收货人信息
        try {
            $addressId = $this->checkAddress($addressId, $userId, $userModel->address_id);
        } catch (BadRequestHttpException $e) {
            throw $e;
        }

        //  【2】获取待入库的商品信息
        $goods_Info = Goods::getGroupGoodsForBuy(
            $params['goods_id'],
            $params['goods_number']
        );
        if ($goods_Info['code'] == 0) {
            $goodsList = [$goods_Info];
        } elseif ($goods_Info['msg']) {
            \Yii::trace(__CLASS__.' | '.__FUNCTION__.' | '.__LINE__.' | '.$goods_Info['msg']);
            throw new BadRequestHttpException($goods_Info['msg'], 8);
        } else {
            \Yii::trace(__CLASS__.' | '.__FUNCTION__.' | '.__LINE__.' | '.json_encode($params));
            throw new BadRequestHttpException('参数错误', 9);   //  没有匹配到逻辑
        }

        //  【3】获取待入库的订单信息
        $consignee = UserAddress::find()->where(['address_id' => $addressId])->asArray()->one();
        if (!$consignee['country']) {
            $consignee['country'] = 1;
        }

        //  判定是否要减库存——如果使用库存，且下订单时减库存，则减少库存
        $shopConfigParams = CacheHelper::getShopConfigParams(['use_storage', 'stock_dec_time']);
        if ($shopConfigParams['use_storage']['value'] == 1 && $shopConfigParams['stock_dec_time']['value'] == 1) {
            $changeGoodsNumber = true;
        } else {
            $changeGoodsNumber = false;
        }

        $order_uniq_id = OrderGroupHelper::getUniqidGroupId($userId);  //  会被拆分成多个订单的 设置一个唯一的总单号
        $addTime = DateTimeHelper::getFormatGMTTimesTimestamp();

        //  初始化订单信息
        $order = [
            'pay_id'          => $payId,
            'pay_name'        => $payName,
            'consignee'       => $consignee['consignee'],
            'country'         => $consignee['country'],
            'province'        => $consignee['province'],
            'city'            => $consignee['city'],
            'district'        => $consignee['district'],
            'address'         => $consignee['address'],
            'zipcode'         => $consignee['zipcode'],
            'tel'             => $consignee['tel'],
            'mobile'          => $consignee['mobile'],
            'pack_id'         => 0,
            'card_id'         => 0,
            'card_message'    => '当前不支持贺卡',
            'surplus'         => 0.00,  //  用户可用余额
            'integral'        => 0, //  积分
            'bonus_id'        => 0, //  红包ID
            'discount'        => 0.00,  //  减免的金额，不是享受的折扣
            'tax'             => 0.00,
            'need_inv'        => 0, //  是否需要发票
            'inv_type'        => '无',
            'inv_payee'       => '',    //  发票抬头
            'inv_content'     => '',    //  发票内容
            'how_oos'         => '',    //  缺货处理方式
            'need_insure'     => 0, //  是否保价
            'user_id'         => $userId,
            'order_status'    => OrderInfo::ORDER_STATUS_UNCONFIRMED,
            'shipping_status' => OrderInfo::SHIPPING_STATUS_UNSHIPPED,
            'pay_status'      => OrderInfo::PAY_STATUS_UNPAYED,
            'group_id'        => $order_uniq_id,
            'agency_id'       => 0,   //  办事处 ID
            'postscript'      => $postscript,   //  留言，给所有的订单都留同样的留言
            'add_time'        => $addTime,
            'shipping_fee'    => 0.00,
            'insure_fee'      => 0.00,
            'card_name'       => '',
            'card_fee'        => 0.00,    //  祝福贺卡
            'pack_fee'        => 0.00,    //  商品包装
            'pay_fee'         => 0.00,
            'cod_fee'         => 0.00,
            'from_ad'         => 0,    //  来源——广告位
            'referer'         => '',    //  来源——页面
            'parent_id'       => 0,    //  不启用分成功能
            'extension_code'  => 'group_buy',
            'extension_id'    => $actId,
        ];

        //  【4】判断是否有参与活动(满赠满减)
        $goodsMap = [
            [
                'goods_id'      => $params['goods_id'],
                'goods_number'  => $params['goods_number'],
                'selected'      => 1,
            ]
        ];
        $validEvents = EventHelper::formatValidEvents($goodsMap, $userId);
        if ($validEvents) {
            $fullCut = current($validEvents['fullCut']);

            //  修正商品、订单的结算价格
            if ($fullCut['cut'] > 0) {
                $grouped_goods_list[] = $goodsList;
                $fullCutMap = EventHelper::assignFullCut($fullCut, $grouped_goods_list);
                $goodsList = $grouped_goods_list[0];

                //  修正订单金额
                if (!empty($fullCutMap) && current($fullCutMap)['discount'] > 0) {
                    $order['discount'] = NumberHelper::price_format($order['discount'] + current($fullCutMap)['discount']);
                }
            }
        }

        //  【5】订单处理
        $this->processSingleOrder(
            $goodsList,
            $order_type,
            $goods_Info['brand_id'],
            $userId,
            $changeGoodsNumber,
            $order_uniq_id,
            $order,
            $fromCart,
            $order_done
        );

        return $order_done;
    }

    /**
     * 虚拟商品立即购买
     */
    public function actionExchange_create()
    {
        //  【0】基础参数
        $userModel = Yii::$app->user->identity;
        $params = Yii::$app->request->post('data');
        $from_type = Yii::$app->request->post('from_type');
        $order_type = 0;    //  按品牌拆单
        $addressId = !empty($params['address_id']) ? $params['address_id'] : 0;
        //  设定平台对应的默认支付方式
        $payId = PaymentHelper::PAY_ID_INTEGRAL;
        $payName = PaymentHelper::$paymentMap[$payId];

        $order_done = [];
        $postscript = isset($params['postscript']) ? trim($params['postscript']) : '';
        $userId = $userModel->user_id;
        $fromCart = 0;

        //  【1】实体商品需要验证收货人信息
        try {
            $addressId = $this->checkAddress($addressId, $userId, $userModel->address_id);
        } catch (BadRequestHttpException $e) {
            throw $e;
        }

        //  【2】获取待入库的商品信息
        $goods_Info = Goods::getGoodsForBuy(
            $params['goods_id'],
            $params['goods_number'],
            $userModel->user_rank
        );

        if ($goods_Info['code'] == 0) {
            if ($goods_Info['need_rank'] > $userModel->user_rank) {
                throw new BadRequestHttpException('抱歉，您当前的会员等级不能兑换该商品', 10);
            }
            $goodsList = [$goods_Info];
        } elseif ($goods_Info['msg']) {
            \Yii::trace(__CLASS__.' | '.__FUNCTION__.' | '.__LINE__.' | '.$goods_Info['msg']);
            throw new BadRequestHttpException($goods_Info['msg'], 8);
        } else {
            \Yii::trace(__CLASS__.' | '.__FUNCTION__.' | '.__LINE__.' | '.json_encode($params));
            throw new BadRequestHttpException('参数错误', 9);   //  没有匹配到逻辑
        }

        //  【3】获取待入库的订单信息
        $consignee = UserAddress::find()->where(['address_id' => $addressId])->asArray()->one();

        if (!$consignee['country']) {
            $consignee['country'] = 1;
        }

        //  判定是否要减库存——如果使用库存，且下订单时减库存，则减少库存
        /*$shopConfigParams = CacheHelper::getShopConfigParams(['use_storage', 'stock_dec_time']);
        if ($shopConfigParams['use_storage']['value'] == 1 && $shopConfigParams['stock_dec_time']['value'] == 1) {
            $changeGoodsNumber = true;
        } else {
            $changeGoodsNumber = false;
        }*/
        //  积分商品为立即支付，支付减库存
        $changeGoodsNumber = true;

        $order_uniq_id = OrderGroupHelper::getUniqidGroupId($userId);  //  会被拆分成多个订单的 设置一个唯一的总单号
        $addTime = DateTimeHelper::getFormatGMTTimesTimestamp();

        //  初始化订单信息
        $order = [
            'pay_id'          => $payId,
            'pay_name'        => $payName,
            'consignee'       => $consignee['consignee'],
            'country'         => $consignee['country'],
            'province'        => $consignee['province'],
            'city'            => $consignee['city'],
            'district'        => $consignee['district'],
            'address'         => $consignee['address'],
            'zipcode'         => $consignee['zipcode'],
            'tel'             => $consignee['tel'],
            'mobile'          => $consignee['mobile'],
            'pack_id'         => 0,
            'card_id'         => 0,
            'card_message'    => '当前不支持贺卡',
            'surplus'         => 0.00,  //  用户可用余额
            'integral'        => 0, //  积分
            'bonus_id'        => 0, //  红包ID
            'discount'        => 0.00,  //  减免的金额，不是享受的折扣
            'tax'             => 0.00,
            'need_inv'        => 0, //  是否需要发票
            'inv_type'        => '无',
            'inv_payee'       => '',    //  发票抬头
            'inv_content'     => '',    //  发票内容
            'how_oos'         => '',    //  缺货处理方式
            'need_insure'     => 0, //  是否保价
            'user_id'         => $userId,
            'order_status'    => OrderInfo::ORDER_STATUS_CONFIRMED,
            'shipping_status' => OrderInfo::SHIPPING_STATUS_UNSHIPPED,
            'pay_status'      => OrderInfo::PAY_STATUS_PAYED,
            'group_id'        => $order_uniq_id,
            'agency_id'       => 0,   //  办事处 ID
            'postscript'      => $postscript,   //  留言，给所有的订单都留同样的留言
            'add_time'        => $addTime,
            'shipping_fee'    => 0.00,
            'insure_fee'      => 0.00,
            'card_name'       => '',
            'card_fee'        => 0.00,    //  祝福贺卡
            'pack_fee'        => 0.00,    //  商品包装
            'pay_fee'         => 0.00,
            'cod_fee'         => 0.00,
            'from_ad'         => 0,    //  来源——广告位
            'referer'         => '',    //  来源——页面
            'parent_id'       => 0,    //  不启用分成功能
            'extension_code'  => 'integral_exchange',   //    积分或优惠券  要做区分
            'extension_id'    => 0,
        ];

        //  积分暂时不考虑满减
        $this->processSingleOrder(
            $goodsList,
            $order_type,
            $goods_Info['brand_id'],
            $userId,
            $changeGoodsNumber,
            $order_uniq_id,
            $order,
            $fromCart,
            $order_done
        );

        return $order_done;
    }

    /**
     * POST order/receive 确认收货
     */
    public function actionReceive()
    {
        //  暂时不做
    }

//    /**
//     * 支付宝加签接口
//     * @return array
//     */
//    public function actionAlipay_sign() {
//        $orderInfo = Yii::$app->request->post('data');
//
//        $c = new \AopClient();
//        $c->gatewayUrl = "https://openapi.alipay.com/gateway.do";
//        $c->appId = "2016042001316372";
//        $c->rsaPrivateKey = 'MIICXAIBAAKBgQC34aSx5bRS887FjmuP4wwdexm5dF7jK74FEgSFAR4K158zPCAoxlFow1PqEdg8l6ouqNhvtA5ipgil0B/CDV2Pz74GeTdx5RFqNG2XZ+L78BB+UvkH7mPiI0ltrUxt6TQUTZB1Gq8gGfBwVOoLCT2vdOtn6X1Klyf5lCO5FwqX/QIDAQABAoGBAIA/q4u8JynYDDYzoAeqFtAVBJsZc/jDkHOe3nIZlmd/ffTREaj+sNb9rPBLY+LW8QY8E0DNd18MaGmloBDLG548UAo0l1rq8jI1Ho+TdekGjceO/AbGYQoaQPAv5QpT1V6d4cBVnARQtWNn933jq8Sy3TY7pYswAwjp24mTbYkhAkEA3c5jtaZObIHCuwK8p1Clqnfwdlfr3eJBFiqzv+vew+1nVmow1QRPadKkrwflk3IGwwbe23AIRUwghyzymtRISQJBANQ6jBJ6GDqPtChxzQ77y2JoH+2UR6bsivMPWwrBxLMgGIqP41nVTVfVxUFyki9B3qjAwgUWHsZidbfZMLZ/WhUCQCF8VSVJVDGhbWqaQSzpSflwXgcfwuYekEDudXCWBW2C28T6ByFBo1OQj3g1Mv2Ni2PKF779LOJ5WbfOm1mwjrECQA7kmOXHCaI6aWMZMVGm28vpEKyEZk7RdKK/5hyoIlyLmQ1IMaUhMc9DIheIqbeFQNHIzRS8S6aSTD59kEyOPU0CQDZN8LiVwu/CBlScM15mqDiL+xf1Z9StBk9fkRFbXadQKzqNoHUlWa8VeRSF8zvmcaCPiIInswYrhxNGKpPgBKg=';
//        $c->format = "json";
//        $c->charset= "UTF-8";
//        $c->alipayrsaPublicKey = 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDDI6d306Q8fIfCOaTXyiUeJHkrIvYISRcc73s3vF1ZT7XN8RNPwJxo8pWaJMmvyTn9N4HQ632qJBVHf8sxHi/fEsraprwCtzvzQETrNRwVxLO5jVmRGi60j8Ue1efIlzPXV9je9mkjzOmdssymZkh2QhUrCmZYI/FCEa3/cNMW0QIDAQAB';
//
//        $signed = $c->sign($orderInfo);
//        return [
//            'signed' => urlencode($signed),
//        ];
//    }

    /**
     * 支付宝支付，服务端签名并回传给客户端，并保存支付的alipay_info
     * @return array
     * @throws BadRequestHttpException
     */
    public function actionAlipay() {
        $data = Yii::$app->request->post('data');
        if (!empty($data['orderId'])) {
            $orderInfo = OrderInfo::findOne(['order_id' => $data['orderId']]);
            if (empty($orderInfo)) {
                throw new BadRequestHttpException('未找到订单', 1);
            }
            $orderList = [
                $orderInfo,
            ];
        }
        elseif (!empty($data['groupId'])) {
            $groupId = $data['groupId'];
            $orderList = OrderInfo::findAll([
                'group_id' => $groupId,
            ]);
        }
        else {
            throw new BadRequestHttpException('缺少请求参数', 2);
        }

        if (empty($orderList)) {
            throw new BadRequestHttpException('未找到订单', 3);
        }

        //标题
        $productName = '订单编号：'. $orderList[0]['order_sn'];

        //out_trade_no
        $outTradeNo = OrderHelper::generateOutTradeNo($orderList, 'alipay');

        //交易金额
        $amount = 0;
        foreach ($orderList as $orderInfo) {
            $amount += $orderInfo->order_amount;

            $alipayInfo = new AlipayInfo();
            $alipayInfo->out_trade_no = $outTradeNo;
            $alipayInfo->order_sn = $orderInfo->order_sn;
            $alipayInfo->pay_log_id = $orderInfo->paylog->log_id;
            $alipayInfo->total_fee = 0.00;
            $alipayInfo->save();
        }

        //下面是签名
        $c = new \AopClient();
        $c->gatewayUrl = "https://openapi.alipay.com/gateway.do";
        $c->appId = "2016042001316372";
        $c->rsaPrivateKey = 'MIICXgIBAAKBgQDdBRWSSXb7tg2mmav2VzAiEVNuLU1NQkxl684LdLBUSyx9oCiXJ6tJVXW37DyLhSxsbdqivwTV2Xb3Czi91J9GhQqDwqpU4vSDDHDZSyEgjq/wrflATo8+ST48eVKlyscMkwPhv2lc2oJSGjgmkorb3Jl58eG7YfcCk3Aw9P6w2wIDAQABAoGAA7CACa8cQ1toou1Rx4zxCsCLSf2LmsyOhe0HxX0vLFkM5xPzWYKaA2Ff07An2pRgh3bV/X1+0SsOJ1WSnuibuAOuev8QXTXYrMPtX6MLYvRP1HrqlZoVBO1Bmc68jdoxS7omHSaK86m4yrPQIwaP02K0k3XGRqCXQ3VxODReZTECQQD/Ly7YeEd3O6is35iTca0sMPrc92ORo3IwhEdrPhOiZAyJlIb4IDBSkYxvEwbFvKcjfp8CePBZv/vaE5gp9wcnAkEA3bnxzcv2cG3UJfhQ//ysFY8nt/QrNQ25x6M+dAV093H+w0Vl22Ldv4bnOTkNYl5dUL+OGZd/8rZQCTtzcud5LQJBALuA1OAUSRbQTFlyFi9I2ODewIX6dTv/KBmEKOIhA9ZPw3KYIzBQnpEdB15aUaCbxQfsszPi32BjE9Ciky1KqQMCQQC4yPDGTEdz53Q4uLv4u0FHLmkxm6IusuOzh07TLoEOf8iMQNfkgH7B0dH+FJgc9PvcAeiRV3tgcaQ+LXfHuTV5AkEApkWYz+r5/z8yhLkNkhu0CVG863K4rWYRPOs9yWleSvn5BCYGJz07NS2j23qebQh6kDWvX+zt96Sfb4f/QITExQ==';
        $c->format = "json";
        $c->charset= "UTF-8";
        $c->alipayrsaPublicKey = 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDDI6d306Q8fIfCOaTXyiUeJHkrIvYISRcc73s3vF1ZT7XN8RNPwJxo8pWaJMmvyTn9N4HQ632qJBVHf8sxHi/fEsraprwCtzvzQETrNRwVxLO5jVmRGi60j8Ue1efIlzPXV9je9mkjzOmdssymZkh2QhUrCmZYI/FCEa3/cNMW0QIDAQAB';

        $params['app_id'] = '2016042001316372';
        $params['biz_content'] = '{"timeout_express":"1440m","product_code":"QUICK_MSECURITY_PAY",'.
            '"total_amount":"'.$amount.'",'.
            '"subject":"'. $productName. '",'.
            '"body":"'. $productName .'",'.
            '"out_trade_no":"'. $outTradeNo. '"}';
        $params['charset'] = 'utf-8';
        $params['method'] = 'alipay.trade.app.pay';
        $params['sign_type'] = 'RSA';
        $params['timestamp'] = date("Y-m-d H:i:s");
        $params['version'] = '1.0';
        $params['notify_url'] = 'http://api.xiaomei360.com/v1/notify/alipay-rsp';
        $params['sign'] = $c->rsaSign($params);

        $reqParams = "";
        foreach ($params as $key => $value) {
            $reqParams .= "$key=" . urlencode($value) . "&";
        }
        $result = substr($reqParams, 0, -1);

        return [
            'signed' => $result,
        ];
    }

    /**
     * 微信支付统一收单接口
     * @return string
     * @throws BadRequestHttpException
     * @throws ServerErrorHttpException
     */
    public function actionWxpay() {
        $data = Yii::$app->request->post('data');
        if (!empty($data['orderId'])) {
            $orderInfo = OrderInfo::findOne(['order_id' => $data['orderId']]);
            if (empty($orderInfo)) {
                throw new BadRequestHttpException('未找到订单', 1);
            }
            $orderList = [
                $orderInfo,
            ];
        }
        elseif (!empty($data['groupId'])) {
            $groupId = $data['groupId'];
            $orderList = OrderInfo::findAll([
                'group_id' => $groupId,
            ]);
        }
        else {
            throw new BadRequestHttpException('缺少请求参数', 2);
        }

        if (empty($orderList)) {
            throw new BadRequestHttpException('未找到订单', 3);
        }

        $productName = '订单编号：'. $orderList[0]['order_sn'];

        //总金额
        $amount = 0;
        //商户交易号
        $outTradeNo = OrderHelper::generateOutTradeNo($orderList, 'wxpay');

        foreach ($orderList as $orderInfo) {
            $amount += $orderInfo->order_amount;
        }

        $prepay_info = $this->req_prepay_info($amount, $productName, $outTradeNo);

        foreach ($orderList as $orderInfo) {
            //交易金额
            $wechatPayInfo = new WechatPayInfo();
            if ($prepay_info['return_code'] != 'SUCCESS') {
                $wechatPayInfo->return_msg = $prepay_info['return_msg'];
            }
            else {
                $wechatPayInfo->appid = $prepay_info['appid'];
                $wechatPayInfo->mch_id = $prepay_info['mch_id'];
                $wechatPayInfo->nonce_str = $prepay_info['nonce_str'];
                $wechatPayInfo->result_code = $prepay_info['result_code'];
                $wechatPayInfo->return_code = $prepay_info['return_code'];
                $wechatPayInfo->return_msg = $prepay_info['return_msg'];
                $wechatPayInfo->sign = $prepay_info['sign'];
                $wechatPayInfo->prepay_id = $prepay_info['prepay_id'];
                $wechatPayInfo->trade_type = $prepay_info['trade_type'];

                $wechatPayInfo->out_trade_no = $outTradeNo;
                $wechatPayInfo->order_sn = $orderInfo->order_sn;
                $wechatPayInfo->pay_log_id = $orderInfo->paylog->log_id;
                $wechatPayInfo->user_id = $orderInfo->user_id;
                $wechatPayInfo->enable = 1;
            }

            if (!$wechatPayInfo->save()) {
                throw new ServerErrorHttpException(json_encode($wechatPayInfo->firstErrors), 1);
            }
        }

        $data = [
            'appid' => $prepay_info['appid'],
            'noncestr' => $prepay_info['nonce_str'],
            'package' => 'Sign=WXPay',
            'partnerid' => '1377397002',
            'prepayid' => $prepay_info['prepay_id'],
            'timestamp' => time(),
        ];

        $appPay = new \WxPayAppPay();
        $appPay->SetAppid($prepay_info['appid']);
        $appPay->SetNoncestr($prepay_info['nonce_str']);
        $appPay->SetPackage('Sign=WXPay');
        $appPay->SetPartnerid('1377397002');
        $appPay->SetPrepayid($prepay_info['prepay_id']);
        $appPay->SetTimestamp(time());
        $appPay->SetSign();

        $data['sign'] = $appPay->GetSign();

        return [
            'prepay_id' => $data['prepayid'],
            'nonce_str' => $data['noncestr'],
            'sign' => $data['sign'],
            'time' => $data['timestamp'],
        ];
    }

    private function req_prepay_info($order_amount, $attach, $out_trade_no) {
        //支付总价
        $total_fee = floatval($order_amount);
        $attach = empty($attach) ? WECHAT_PAY_ATTACH_NORMAL : $attach;
        $total_fee = intval($total_fee * 100);
        $notify_url = 'http://api.xiaomei360.com/v1/notify/wxpay-rsp';

        $exploded = explode('O',$out_trade_no);
        $body = "小美诚品-订单编号:".$exploded[0];

        $input = new \WxPayUnifiedOrder();
        $input->SetBody($body);
        $input->SetAttach($attach);
        $input->SetOut_trade_no($out_trade_no);
        $input->SetTotal_fee($total_fee);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + WECHAT_PAY_PREPAY_EFFECTIVE_TIME));
        $input->SetGoods_tag("tag");
        $input->SetNotify_url($notify_url);
        $input->SetTrade_type("APP");
        $input->SetProduct_id($out_trade_no);

        //拉取prepay_info，用于生成o_wechat_pay_info数据
        $prepay_info = \WxPayApi::unifiedOrder($input);
        return $prepay_info;
    }


    /**
     * 订单入库前检验收货地址  返回有效的收获地址ID
     * @param $addressId    调用端传入的地址ID
     * @param $userId       用户ID
     * @param $defaultAddressId 用户默认地址ID
     * @return int  0：没有地址 1：有地址但信息不完整 2：有地址并且信息完整
     * @throws BadRequestHttpException  0：没有地址 1：有地址但信息不完整 抛出异常
     */
    private function checkAddress($addressId, $userId, $defaultAddressId)
    {
        if (!$addressId) {
            if (!empty($defaultAddressId)) {
                $hasDefault = UserAddress::checkDeafultAddress($userId, $defaultAddressId);
                if ($hasDefault) {
                    $addressId = $defaultAddressId;
                }
            }

            //  如果没有默认收货地址 或 默认收货地址无效，则设置最近添加的地址为默认
            if (!$addressId) {
                $list = UserAddress::getList($userId);
                if ($list) {
                    $last = end($list);
                    $addressId = $last->address_id;
                    Users::setDefaultAddress($userId, $addressId);
                }
            }
        }

        //  检验用户选中地址的有效性
        $addressCheck = UserAddress::checkUserAddress($userId, $addressId);
        if ($addressCheck == 1) {
            throw new BadRequestHttpException('您当前选择的地址信息不完整，请完善地址后再下单', 2);
        } elseif ($addressCheck == 0) {
            throw new BadRequestHttpException('您当前没有收获地址，请完善地址后再下单', 3);
        } elseif ($addressCheck == 2) {
            return $addressId;  //  返回有效的收获地址ID
        }
    }


    /**
     * 处理 【团采/秒杀/立即购买】 的订单入库
     * @param $goods_list   单个订单的商品列表
     * @param $order_type   是否按供应商拆单
     * @param $brand_id_or_supplier_user_id 供应商或品牌ID
     * @param $userId       用户ID
     * @param $changeGoodsNumber            是否减库存
     * @param $order_done   已完成订单列表
     * @throws BadRequestHttpException
     * @throws ServerErrorHttpException
     */
    private function processSingleOrder(
        $goods_list,
        $order_type,
        $brand_id_or_supplier_user_id,
        $userId,
        $changeGoodsNumber,
        $order_uniq_id,
        $order,
        $fromCart,
        &$order_done
    ) {
        Yii::warning('订单处理开始'. date('Y-m-d H:i:s').
            ' $userId = '.$userId.
            ' $order_type = '.$order_type.
            ' $order = '.json_encode($order));

        $time = DateTimeHelper::getFormatGMTTimesTimestamp();
        $item = current($goods_list);
        $brand_ids[$item['brand_id']] = $item['brand_name'];
        $shipping_fee_format = $item['shipping_fee_format'];

        $goodsAmount = 0;
        $goodsAmountMsg = '';
        foreach ($goods_list as $goods) {
            $goodsAmount += bcmul($goods['goods_number'], $goods['goods_price'], 2);
            $goodsAmountMsg .= ' goods_number = '.$goods['goods_number'].' * goods_price = '.$goods['goods_price'].' + ';
        }
        $goodsAmount = NumberHelper::price_format($goodsAmount);
        Yii::error('费用计算 '.$goodsAmountMsg.' $goodsAmount = '.$goodsAmount);


        //  【4.1】   组合订单信息
        $order['goods_amount'] = $goodsAmount;
        $order['order_amount'] = bcsub($goodsAmount, $order['discount'], 2);   //  后续修正 + 运费 - 优惠券
        $order['shipping_name'] = $shipping_fee_format;

        //  如果是积分兑换，则判断用户积分是否足够,如果足够，订单置为已支付
        $userModel = Users::find()->where(['user_id' => $userId])->one();
        if ($order['extension_code'] == 'integral_exchange') {
            $balance = Integral::getBalance($userId);
            if ($balance >= 0) {
                $userModel->setAttribute('int_balance', $balance);
                $userModel->save();
            }
            if ($userModel->int_balance < $goodsAmount) {
                Yii::error($userId.'订单入库失败');
                throw new ServerErrorHttpException('抱歉，您的积分不足以兑换当前商品', 15);
            }
            $order['order_amount'] = 0;
        }

        if($order_type == 1) {
            $order['supplier_user_id'] = $brand_id_or_supplier_user_id;
            $order['brand_id'] = 0;
        }
        else {
            $order['brand_id'] = $brand_id_or_supplier_user_id;
            $order['supplier_user_id'] = 0;
        }

        //  检查积分 红包 优惠券 满减  当前不支持余额支付

        //  如果订单金额为0（使用余额或积分或红包支付），修改订单状态为已确认、已付款
        if ($order['order_amount'] <= 0) {
            $order['order_status'] = OrderInfo::ORDER_STATUS_CONFIRMED;
            $order['confirm_time'] = $time;
            $order['pay_status']   = OrderInfo::PAY_STATUS_PAYED;
            $order['pay_time']     = $time;
            $order['order_amount'] = 0.00;
        }

        $order['integral_money']   = 0.00;
        $order['integral']         = 0;

        //  获取有效的order_sn（不能重复）
        $order['order_sn'] = OrderHelper::getUniqidOrderSn(); //获取新订单号
        foreach ($order as $key => $value) {
            if (in_array($key, ['order_id', 'need_inv', 'need_insure', 'cod_fee'])) {
                unset($order[$key]);
            }
        }

        //  订单入库前判断库存是否充足
        if ($changeGoodsNumber) {
            foreach ($goods_list as $goods) {
                //  如果赠品与实际购买的商品一致，则库存需要累减
                if (!isset($_goods_number_max[$goods['goods_id']]) && isset($goods['goods_number_max'])) {
                    $_goods_number_max[$goods['goods_id']] = $goods['goods_number_max'];
                }

                if (
                    isset($goods['goods_number_max']) &&
                    $_goods_number_max[$goods['goods_id']] - $goods['goods_number'] < 0
                ) {
                    Yii::error($goods['goods_name'].'库存不足'.$goods['goods_number']);
                    throw new BadRequestHttpException($goods['goods_name'].'库存不足'.$goods['goods_number'], 7);
                }
            }
        }


        //  【4.2】   使用事务处理订单入库 order_info order_goods
        $cartDelete = [];
        $transaction = ActiveRecord::getDb()->beginTransaction();
        try {
            $orderGroup = new OrderGroup();
            $orderGroup->group_id = $order_uniq_id;
            $orderGroup->user_id = $userId;
            $orderGroup->create_time = DateTimeHelper::getFormatGMTTimesTimestamp();
            $orderGroup->group_status = OrderGroup::ORDER_GROUP_STATUS_UNPAY;
            $orderGroup->consignee = $order['consignee'];
            $orderGroup->mobile = $order['mobile'];
            $orderGroup->country = $order['country'];
            $orderGroup->province = $order['province'];
            $orderGroup->city = $order['city'];
            $orderGroup->district = $order['district'];
            $orderGroup->address = $order['address'];
            $orderGroup->pay_id = 0;
            $orderGroup->pay_name = '未支付';
            $orderGroup->pay_time = 0;
            $orderGroup->shipping_time = 0;
            $orderGroup->recv_time = 0;
            if (!$orderGroup->save()) {
                Yii::warning('总单入库失败 $orderGroup->errors = '.TextHelper::getErrorsMsg($orderGroup->errors), __METHOD__);
                throw new ServerErrorHttpException('总单入库失败', 12);
            }

            Yii::warning($userId.'订单商品入库开始。goodsList:'.json_encode($goods_list));
            $order['group_identity'] = $orderGroup->id;

            //  积分兑换的 配送方式为到付
            if ($order['extension_code'] == 'integral_exchange') {
                $order['shipping_id']   = 3;
                $order['shipping_name'] = '运费到付';
            }

            $orderModel = new OrderInfo();
            $orderModel->setAttributes($order);
            if (!$orderModel->save()) {
                Yii::warning('总单入库失败 $orderModel->errors = '.TextHelper::getErrorsMsg($orderModel->errors), __METHOD__);
                throw new Exception('订单入库失败');
            }

            //  订单商品入库
            foreach ($goods_list as $goods) {
                //  stock_dec_time  0 发货减库存，1下单减库存
                if ($changeGoodsNumber) {
                    //  减库存
                    if (!isset($goods_number_max[$goods['goods_id']])) {
                        $goods_number_max[$goods['goods_id']] = $goods['goods_number_max'];
                    }
                    //  赠品可能和实际购买商品相同，商品库存需要累减
                    $goods_number_max[$goods['goods_id']] -= $goods['goods_number'];
                    if ($goods_number_max[$goods['goods_id']] < 0) {
                        throw new BadRequestHttpException($goods['goods_name'].'库存不足'.$goods['goods_number'], 7);
                    } else {
                        $rs = Goods::updateAll(
                            ['goods_number' => $goods_number_max[$goods['goods_id']]],
                            ['goods_id' => $goods['goods_id']]
                        );

                        if (!$rs) {
                            Yii::warning('修改商品库存失败', __METHOD__);
                            throw new Exception('修改商品库存失败', 8);
                        }
                    }
                }

                $goods['order_id'] = $orderModel->order_id;
                $goods['product_id'] = 0;   //  用途不明确
                $goods['goods_attr'] = '';
                $goods['goods_attr_id'] = '';
                if ($order['extension_code'] == 'integral_exchange') {
                    $goods['extension_code'] = 'integral_exchange';
                } else {
                    $goods['extension_code'] = '';
                }

                $goods['parent_id'] = 0;
                if (!empty($goods['gift'])) {
                    $gift = $goods['gift'];
                    unset($goods['gift']);
                } else {
                    $gift = [];
                }

                //  考虑过滤 非model字段的方法
                $orderGoods = new OrderGoods();
                $orderGoodsAttributes = array_keys($orderGoods->attributeLabels());
                foreach ($goods as $key => $value) {
                    if (in_array($key, $orderGoodsAttributes)) {
                        $orderGoods->setAttribute($key, $value);
                    }
                }
                if ($orderGoods->save()) {
                    $cartDelete[] = $goods['goods_id'];
                    //  赠品入库
                    if ($gift) {
                        if ($gift['goods_number'] < 1) {
                            continue;
                        }
                        //  stock_dec_time  0 发货减库存，1下单减库存
                        if ($changeGoodsNumber) {
                            if (!isset($goods_number_max[$gift['goods_id']])) {
                                $goods_number_max[$gift['goods_id']] = $gift['gift_number_max'];
                            }
                            $goods_number_max[$gift['goods_id']] -= $gift['goods_number']; //  当前库存
                            if ($goods_number_max[$gift['goods_id']] < 0) {
                                Yii::error($gift['goods_name'].'库存不足'.$gift['goods_number']);
                                throw new BadRequestHttpException($gift['goods_name'].'库存不足'.$gift['goods_number'], 8);
                            } else {
                                Goods::updateAll(
                                    ['goods_number' => $goods_number_max[$gift['goods_id']]],
                                    ['goods_id' => $gift['goods_id']]
                                );
                            }
                        }

                        $gift['order_id'] = $orderModel->order_id;
                        $gift['parent_id'] = $goods['goods_id'];
                        $gift['product_id'] = 0;    //  用途不明确
                        $gift['goods_attr'] = '';
                        $gift['goods_attr_id'] = '';
                        $gift['extension_code'] = '';
                        $gift['market_price'] = $gift['gift_show_peice'];
                        $gift['goods_price'] = $gift['gift_need_pay'];

                        $orderGoodsGift = new OrderGoods();
                        foreach ($gift as $key => $value) {
                            if (in_array($key, $orderGoodsAttributes)) {
                                $orderGoodsGift->setAttribute($key, $value);
                            }
                        }
                        if (!$orderGoodsGift->save()) {
                            Yii::error($userId.'订单商品入库失败'.date('Y-m-d H:i:s').'goods_id:'.$goods['goods_id'].
                                ' $orderGoodsGift->errors = '.TextHelper::getErrorsMsg($orderGoodsGift->errors),
                                __METHOD__
                            );
                            throw new ServerErrorHttpException('订单赠品入库失败', 5);
                        }
                    }
                }
                else {
                    $errorInfo = $userId.'订单商品入库失败'.date('Y-m-d H:i:s').'goods_id:'.$goods['goods_id'];
                    $errorInfo .= ' $orderGoods = '.json_encode($orderGoods);
                    if (!empty($orderGoods->errors)) {
                        $errorInfo .= ' $orderGoods->errors = '.TextHelper::getErrorsMsg($orderGoods->errors);
                    }
                    Yii::error($errorInfo , __METHOD__);
                    throw new ServerErrorHttpException('订单商品入库失败', 6);
                }
            }

            //  插入支付日志
            if ($order['extension_code'] == 'integral_exchange') {
                //  积分兑换的商品扣除积分
                $integral = (int)$order['goods_amount'];
                $integralData = [
                    'integral' => -$integral,
                    'user_id' => $userId,
                    'pay_code' => Integral::PAY_CODE_INTEGRAL,
                    'out_trade_no' => ''.$orderModel->order_id,
                    'note' => $orderModel->order_id,
                    'created_at' => $time,
                    'updated_at' => $time,
                    'status' => 1,
                ];

                $integralModel = new Integral();
                $integralModel->setAttributes($integralData);
                Yii::warning('积分流水 $integralModel = '.json_encode($integralModel), __METHOD__);
                if (!$integralModel->save()) {
                    Yii::warning('积分流水入库失败 $integralModel->errors = '.TextHelper::getErrorsMsg($integralModel->errors), __METHOD__);
                    throw new ServerErrorHttpException('积分流水入库失败', 7);
                }

                $userModel->setAttribute('int_balance', 0);
                if (!$userModel->save()) {
                    Yii::warning('修改用户积分可用余额失败 $userModel->errors = '.TextHelper::getErrorsMsg($userModel->errors), __METHOD__);
                    throw new ServerErrorHttpException('修改用户积分可用余额失败', 6);
                }

                //  积分商品 只有立即购买，直接入库为已支付状态
                $payLog = [
                    'order_id' => $orderModel->order_id,
                    'order_amount' => $order['order_amount'],
                    'order_type' => PayLog::ORDER_TYPE_INTEGRAL,  //  0：支付， 1：预付; 2:积分兑换
                    'is_paid' => 1,  //  0：未支付， 1：已支付
                ];
            }
            else {
                $payLog = [
                    'order_id' => $orderModel->order_id,
                    'order_amount' => $order['order_amount'],
                    'order_type' => PayLog::ORDER_TYPE_GENERAL,  //  0：支付， 1：预付; 2:积分兑换
                    'is_paid' => 0,  //  0：未支付， 1：已支付
                ];
            }

            $payLogModel = new PayLog();
            $payLogModel->setAttributes($payLog);
            if (!$payLogModel->save()) {
                Yii::warning('支付记录入库失败 $userModel->errors = '.VarDumper::export($userModel->errors), __METHOD__);
                throw new ServerErrorHttpException('支付记录入库失败', 6);
            }
            $logId = $payLogModel->log_id;

            //  清除购物车中已生成订单的商品
            if ($cartDelete && $fromCart) {
                Yii::warning('清除购物车 user_id = '. $userId. ', delete goods = '. json_encode($cartDelete), __METHOD__);
                Cart::deleteAll([
                    'user_id' => $userId,
                    'goods_id' => $cartDelete,
                ]);
            }

            if ($order['extension_code'] == 'integral_exchange') {
                $goods_amount = (int)$order['goods_amount'];
            } else {
                $goods_amount = NumberHelper::price_format($order['goods_amount']);
            }

            $order_done[] = [
                'order_id'      => (int)$orderModel->order_id,
                'order_sn'      => $order['order_sn'],
                'order_amount'  => NumberHelper::price_format($order['order_amount']),
                'goods_amount'  => $goods_amount,
                'log_id'        => (int)$logId,
                'group_id'      => $order_uniq_id,
            ];

            $orderGroup->syncFeeInfo();
            $orderGroup->setupOrderStatus();
            if (!$orderGroup->save()) {
                Yii::warning('支付记录入库失败 $userModel->errors = '.VarDumper::export($userModel->errors), __METHOD__);
                throw new ServerErrorHttpException('支付记录入库失败', 6);
            }

            $transaction->commit();
        }
        catch (Exception $e) {
            $transaction->rollBack();
            throw new ServerErrorHttpException('创建订单失败', 12);
        }

        Yii::warning('$order_done = '.VarDumper::export($order_done));

//        //  修改拍卖活动状态
//        if ($order['extension_code']=='auction') {
//
//        }
        //  【4.3】 给商家发邮件短信等通知
        //  增加是否给客服发送邮件选项
        /*if ($shopConfigParams['send_service_email']['value'] && $shopConfigParams['service_email']['value'] != '') {
            $tpl = get_mail_template('remind_of_new_order');
            $smarty->assign('order', $order);
            $smarty->assign('goods_list', $goods_list);
            $smarty->assign('shop_name', $_CFG['shop_name']);
            $smarty->assign('send_date', date($_CFG['time_format']));
            $content = $smarty->fetch('str:' . $tpl['template_content']);
            send_mail($_CFG['shop_name'], $_CFG['service_email'], $tpl['template_subject'], $content, $tpl['is_html']);
        }*/

        //  如果订单金额为0 处理虚拟卡  可用于 优惠券 和积分兑换
        /*if ($order['order_amount'] <= 0)
        {
            $sql = "SELECT goods_id, goods_name, goods_number AS num FROM ".
                $GLOBALS['ecs']->table('cart') .
                " WHERE is_real = 0 AND extension_code = 'virtual_card'".
                " AND session_id = '".SESS_ID."' AND rec_type = '$flow_type'";

            $res = $GLOBALS['db']->getAll($sql);

            $virtual_goods = array();
            foreach ($res AS $row)
            {
                $virtual_goods['virtual_card'][] = array('goods_id' => $row['goods_id'], 'goods_name' => $row['goods_name'], 'num' => $row['num']);
            }

            if ($virtual_goods AND $flow_type != CART_GROUP_BUY_GOODS)
            {
                //  虚拟卡发货
                if (virtual_goods_ship($virtual_goods,$msg, $order['order_sn'], true))
                {
                    //  如果没有实体商品，修改发货状态，送积分和红包
                    $sql = "SELECT COUNT(*)" .
                        " FROM " . $ecs->table('order_goods') .
                        " WHERE order_id = '$order[order_id]' " .
                        " AND is_real = 1";
                    if ($db->getOne($sql) <= 0)
                    {
                        //  修改订单状态
                        update_order($order['order_id'], array('shipping_status' => SS_SHIPPED, 'shipping_time' => gmtime()));

                        //  如果订单用户不为空，计算积分，并发给用户；发红包
                        if ($order['user_id'] > 0)
                        {
                            //  取得用户信息
                            $user = user_info($order['user_id']);

                            //  计算并发放积分
                            $integral = integral_to_give($order);
                            log_account_change($order['user_id'], 0, 0, intval($integral['rank_points']), intval($integral['custom_points']), sprintf($_LANG['order_gift_integral'], $order['order_sn']));

                        }
                    }
                }
            }

        }*/
    }

    private function processMutiOrder(
        $grouped_goods_list,
        $userId,
        $order_uniq_id,
        $order,
        $changeGoodsNumber,
        $fromCart,
        &$order_done
    ) {
        $transaction = ActiveRecord::getDb()->beginTransaction();
        try {
            $orderGroup = new OrderGroup();
            $orderGroup->group_id = $order_uniq_id;
            $orderGroup->user_id = $userId;
            $orderGroup->create_time = DateTimeHelper::getFormatGMTTimesTimestamp();
            $orderGroup->group_status = OrderGroup::ORDER_GROUP_STATUS_UNPAY;
            $orderGroup->consignee = $order['consignee'];
            $orderGroup->mobile = $order['mobile'];
            $orderGroup->country = $order['country'];
            $orderGroup->province = $order['province'];
            $orderGroup->city = $order['city'];
            $orderGroup->district = $order['district'];
            $orderGroup->address = $order['address'];
            $orderGroup->pay_id = 0;
            $orderGroup->pay_name = '未支付';
            $orderGroup->pay_time = 0;
            $orderGroup->shipping_time = 0;
            $orderGroup->recv_time = 0;
            if (!$orderGroup->save()) {
                Yii::warning('订单创建失败 $orderGroup->errors = '.TextHelper::getErrorsMsg($orderGroup->errors), __METHOD__);
                throw new ServerErrorHttpException('订单创建失败', 6);
            }

            foreach ($grouped_goods_list as $brand_id_or_supplier_user_id => $goods_list) {
                if (isset($goods_list['is_supplier'])) {
                    $order_type = $goods_list['is_supplier'];
                    unset($goods_list['is_supplier']);
                } else {
                    $order_type = 0;
                }

                if (!$goods_list || !is_array($goods_list)) {
                    continue;
                }

                //  修正订单金额
                $order['discount'] = 0.00;  //  计算折扣前 重置，避免重复计算
                if (!empty($fullCutMap) && $fullCutMap[$brand_id_or_supplier_user_id]['discount'] > 0) {
                    $order['discount'] = NumberHelper::price_format($order['discount'] + $fullCutMap[$brand_id_or_supplier_user_id]['discount']);
                }

                Yii::warning('订单处理开始'. date('Y-m-d H:i:s').
                    ' $userId = '.$userId.
                    ' $order_type = '.$order_type.
                    ' $order = '.json_encode($order));

                $time = DateTimeHelper::getFormatGMTTimesTimestamp();
                $item = current($goods_list);
                $brand_ids[$item['brand_id']] = $item['brand_name'];
                $shipping_fee_format = $item['shipping_fee_format'];

                $goodsAmount = 0;
                $goodsAmountMsg = '';
                foreach ($goods_list as $goods) {
                    $goodsAmount += bcmul($goods['goods_number'], $goods['goods_price'], 2);
                    $goodsAmountMsg .= ' goods_number = '.$goods['goods_number'].' * goods_price = '.$goods['goods_price'].' + ';
                }
                $goodsAmount = NumberHelper::price_format($goodsAmount);
                Yii::error('费用计算 '.$goodsAmountMsg.' $goodsAmount = '.$goodsAmount);


                //  【4.1】   组合订单信息
                $order['goods_amount'] = $goodsAmount;
                $order['order_amount'] = bcsub($goodsAmount, $order['discount'], 2);   //  后续修正 + 运费 - 优惠券
                $order['shipping_name'] = $shipping_fee_format;

                //  如果是积分兑换，则判断用户积分是否足够,如果足够，订单置为已支付
                $userModel = Users::find()->where(['user_id' => $userId])->one();
                if ($order['extension_code'] == 'integral_exchange') {
                    $balance = Integral::getBalance($userId);
                    if ($balance >= 0) {
                        $userModel->setAttribute('int_balance', $balance);
                        if (!$userModel->save()) {
                            Yii::error($userId.'修改用户积分可用余额失败');
                            throw new ServerErrorHttpException('抱歉，修改用户积分可用余额失败', 16);
                        }
                    }
                    if ($userModel->int_balance < $goodsAmount) {
                        Yii::error($userId.'订单入库失败');
                        throw new ServerErrorHttpException('抱歉，您的积分不足以兑换当前商品', 15);
                    }
                    $order['order_amount'] = 0;
                }

                if($order_type == 1) {
                    $order['supplier_user_id'] = $brand_id_or_supplier_user_id;
                    $order['brand_id'] = 0;
                }
                else {
                    $order['brand_id'] = $brand_id_or_supplier_user_id;
                    $order['supplier_user_id'] = 0;
                }

                //  检查积分 红包 优惠券 满减  当前不支持余额支付

                //  如果订单金额为0（使用余额或积分或红包支付），修改订单状态为已确认、已付款
                if ($order['order_amount'] <= 0) {
                    $order['order_status'] = OrderInfo::ORDER_STATUS_CONFIRMED;
                    $order['confirm_time'] = $time;
                    $order['pay_status']   = OrderInfo::PAY_STATUS_PAYED;
                    $order['pay_time']     = $time;
                    $order['order_amount'] = 0.00;
                }

                $order['integral_money']   = 0.00;
                $order['integral']         = 0;

                //  积分兑换的 配送方式为到付
                if ($order['extension_code'] == 'exchange_goods') {
                    $order['shipping_id']   = 3;
                    $order['shipping_name'] = '运费到付';
                }

                //  获取有效的order_sn（不能重复）
                $order['order_sn'] = OrderHelper::getUniqidOrderSn(); //获取新订单号
                foreach ($order as $key => $value) {
                    if (in_array($key, ['order_id', 'need_inv', 'need_insure', 'cod_fee'])) {
                        unset($order[$key]);
                    }
                }

                //  订单入库前判断库存是否充足
                if ($changeGoodsNumber) {
                    foreach ($goods_list as $goods) {
                        //  如果赠品与实际购买的商品一致，则库存需要累减
                        if (!isset($_goods_number_max[$goods['goods_id']]) && isset($goods['goods_number_max'])) {
                            $_goods_number_max[$goods['goods_id']] = $goods['goods_number_max'];
                        }

                        if (
                            isset($goods['goods_number_max']) &&
                            $_goods_number_max[$goods['goods_id']] - $goods['goods_number'] < 0
                        ) {
                            Yii::error($goods['goods_name'].'库存不足'.$goods['goods_number']);
                            throw new BadRequestHttpException($goods['goods_name'].'库存不足'.$goods['goods_number'], 7);
                        }
                    }
                }


                //  【4.2】   使用事务处理订单入库 order_info order_goods
                $cartDelete = [];
                $transaction = ActiveRecord::getDb()->beginTransaction();
                Yii::warning($userId.'订单商品入库开始。goodsList:'.json_encode($goods_list));
                $order['group_identity'] = $orderGroup->id;
                $orderModel = new OrderInfo();
                $orderModel->setAttributes($order);
                if (!$orderModel->save()) {
                    Yii::warning('总单入库失败 $orderModel->errors = '.TextHelper::getErrorsMsg($orderModel->errors), __METHOD__);
                    throw new Exception('订单入库失败');
                }

                //  订单商品入库
                foreach ($goods_list as $goods) {
                    //  stock_dec_time  0 发货减库存，1下单减库存
                    if ($changeGoodsNumber) {
                        //  减库存
                        if (!isset($goods_number_max[$goods['goods_id']])) {
                            $goods_number_max[$goods['goods_id']] = $goods['goods_number_max'];
                        }
                        //  赠品可能和实际购买商品相同，商品库存需要累减
                        $goods_number_max[$goods['goods_id']] -= $goods['goods_number'];
                        if ($goods_number_max[$goods['goods_id']] < 0) {
                            throw new BadRequestHttpException($goods['goods_name'].'库存不足'.$goods['goods_number'], 7);
                        } else {
                            $rs = Goods::updateAll(
                                ['goods_number' => $goods_number_max[$goods['goods_id']]],
                                ['goods_id' => $goods['goods_id']]
                            );

                            if (!$rs) {
                                Yii::warning('修改商品库存失败', __METHOD__);
                                throw new Exception('修改商品库存失败', 8);
                            }
                        }
                    }

                    $goods['order_id'] = $orderModel->order_id;
                    $goods['product_id'] = 0;   //  用途不明确
                    $goods['goods_attr'] = '';
                    $goods['goods_attr_id'] = '';
                    if ($order['extension_code'] == 'integral_exchange') {
                        $goods['extension_code'] = 'integral_exchange';
                    } else {
                        $goods['extension_code'] = '';
                    }

                    $goods['parent_id'] = 0;
                    if (!empty($goods['gift'])) {
                        $gift = $goods['gift'];
                        unset($goods['gift']);
                    } else {
                        $gift = [];
                    }

                    $orderGoods = new OrderGoods();
                    $orderGoodsAttributes = array_keys($orderGoods->attributeLabels());
                    foreach ($goods as $key => $value) {
                        if (in_array($key, $orderGoodsAttributes)) {
                            $orderGoods->setAttribute($key, $value);
                        }
                    }
                    if ($orderGoods->save()) {
                        $cartDelete[] = $goods['goods_id'];
                        //  赠品入库
                        if ($gift) {
                            if ($gift['goods_number'] < 1) {
                                continue;
                            }
                            //  stock_dec_time  0 发货减库存，1下单减库存
                            if ($changeGoodsNumber) {
                                if (!isset($goods_number_max[$gift['goods_id']])) {
                                    $goods_number_max[$gift['goods_id']] = $gift['gift_number_max'];
                                }
                                $goods_number_max[$gift['goods_id']] -= $gift['goods_number']; //  当前库存
                                if ($goods_number_max[$gift['goods_id']] < 0) {
                                    Yii::error($gift['goods_name'].'库存不足'.$gift['goods_number']);
                                    throw new BadRequestHttpException($gift['goods_name'].'库存不足'.$gift['goods_number'], 8);
                                } else {
                                    Goods::updateAll(
                                        ['goods_number' => $goods_number_max[$gift['goods_id']]],
                                        ['goods_id' => $gift['goods_id']]
                                    );
                                }
                            }

                            $gift['order_id'] = $orderModel->order_id;
                            $gift['parent_id'] = $goods['goods_id'];
                            $gift['product_id'] = 0;    //  用途不明确
                            $gift['goods_attr'] = '';
                            $gift['goods_attr_id'] = '';
                            $gift['extension_code'] = '';
                            $gift['market_price'] = $gift['gift_show_peice'];
                            $gift['goods_price'] = $gift['gift_need_pay'];

                            $orderGoodsGift = new OrderGoods();
                            $orderGoodsAttributes = array_keys($orderGoods->attributeLabels());
                            foreach ($orderGoodsGift as $key => $value) {
                                if (in_array($key, $orderGoodsAttributes)) {
                                    $orderGoodsGift->setAttribute($key, $value);
                                }
                            }
                            if (!$orderGoodsGift->save()) {
                                Yii::error($userId.'订单商品入库失败'.date('Y-m-d H:i:s').'goods_id:'.$goods['goods_id'].
                                    ' $orderGoodsGift->errors = '.TextHelper::getErrorsMsg($orderGoodsGift->errors),
                                    __METHOD__
                                );
                                throw new ServerErrorHttpException('订单赠品入库失败', 5);
                            }
                        }
                    } else {
                        Yii::error($userId.'订单商品入库失败'.date('Y-m-d H:i:s').'goods_id:'.$goods['goods_id'].
                            ' $orderGoodsGift->errors = '.json_encode($orderGoodsGift->errors),
                            __METHOD__
                        );
                        throw new ServerErrorHttpException('订单商品入库失败', 6);
                    }
                }

                //  插入支付日志
                if ($order['extension_code'] == 'integral_exchange') {
                    //  积分兑换的商品扣除积分
                    $integral = (int)$order['goods_amount'];
                    $integralData = [
                        'integral' => -$integral,
                        'user_id' => $userId,
                        'pay_code' => Integral::PAY_CODE_INTEGRAL,
                        'out_trade_no' => ''.$orderModel->order_id,
                        'note' => $orderModel->order_id,
                        'created_at' => $time,
                        'updated_at' => $time,
                        'status' => 1,
                    ];

                    $integralModel = new Integral();
                    $integralModel->setAttributes($integralData);
                    if (!$integralModel->save()) {
                        Yii::warning('积分流水入库失败 $integralModel->errors = '.TextHelper::getErrorsMsg($integralModel->errors), __METHOD__);
                        throw new ServerErrorHttpException('积分流水入库失败', 7);
                    }

                    $userModel->setAttribute('int_balance', 0);
                    if (!$userModel->save()) {
                        Yii::warning('修改用户积分可用余额失败 $userModel->errors = '.TextHelper::getErrorsMsg($userModel->errors), __METHOD__);
                        throw new ServerErrorHttpException('积分流水入库失败', 6);
                    }

                    //  积分商品 只有立即购买，直接入库为已支付状态
                    $payLog = [
                        'order_id' => $orderModel->order_id,
                        'order_amount' => $order['order_amount'],
                        'order_type' => PayLog::ORDER_TYPE_INTEGRAL,  //  0：支付， 1：预付; 2:积分兑换
                        'is_paid' => 1,  //  0：未支付， 1：已支付
                    ];
                }
                else {
                    $payLog = [
                        'order_id' => $orderModel->order_id,
                        'order_amount' => $order['order_amount'],
                        'order_type' => PayLog::ORDER_TYPE_GENERAL,  //  0：支付， 1：预付; 2:积分兑换
                        'is_paid' => 0,  //  0：未支付， 1：已支付
                    ];
                }

                $payLogModel = new PayLog();
                $payLogModel->setAttributes($payLog);
                if (!$payLogModel->save()) {
                    Yii::warning('支付记录入库失败 $userModel->errors = '.TextHelper::getErrorsMsg($userModel->errors), __METHOD__);
                    throw new ServerErrorHttpException('支付记录入库失败', 6);
                }
                $logId = $payLogModel->log_id;

                //  清除购物车中已生成订单的商品
                if ($cartDelete && $fromCart) {
                    Yii::warning('清除购物车 user_id = '. $userId. ', delete goods = '. json_encode($cartDelete), __METHOD__);
                    Cart::deleteAll([
                        'user_id' => $userId,
                        'goods_id' => $cartDelete,
                    ]);
                }

                if ($order['extension_code'] == 'integral_exchange') {
                    $goods_amount = (int)$order['goods_amount'];
                } else {
                    $goods_amount = NumberHelper::price_format($order['goods_amount']);
                }

                $order_done[] = [
                    'order_id'      => (int)$orderModel->order_id,
                    'order_sn'      => $order['order_sn'],
                    'order_amount'  => NumberHelper::price_format($order['order_amount']),
                    'goods_amount'  => $goods_amount,
                    'log_id'        => (int)$logId,
                    'group_id'      => $order_uniq_id,
                ];

                $orderGroup->syncFeeInfo();
                $orderGroup->setupOrderStatus();
                if (!$orderGroup->save()) {
                    Yii::warning('支付记录入库失败 $userModel->errors = '.TextHelper::getErrorsMsg($userModel->errors), __METHOD__);
                    throw new ServerErrorHttpException('支付记录入库失败', 6);
                }

                $transaction->commit();

                Yii::warning('$order_done = '.VarDumper::export($order_done));
            }

            $transaction->commit();
        } catch (\Exception $exception) {
            $transaction->rollBack();
            throw new ServerErrorHttpException('创建订单失败', 12);
        }
    }
}