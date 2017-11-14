<?php

namespace backend\controllers;

use backend\models\BackGoods;
use backend\models\BackOrder;
use backend\models\DeliveryOrder;
use backend\models\Goods;
use backend\models\OrderGoods;
use backend\models\OrderGroupImportForm;
use backend\models\OrderInfo;
use backend\models\Region;
use backend\models\Users;
use common\helper\CacheHelper;
use common\helper\DateTimeHelper;
use common\helper\EventHelper;
use common\helper\GoodsHelper;
use common\helper\ImageHelper;
use common\helper\NumberHelper;
use common\helper\OrderGroupHelper;
use common\helper\PriceHelper;
use common\helper\ShippingHelper;
use common\helper\SMSHelper;
use common\models\CashRecord;
use common\models\PayLog;
use console\controllers\CronController;
use kartik\grid\EditableColumnAction;
use Yii;
use backend\models\OrderGroup;
use backend\models\OrderGroupSearch;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use common\controllers\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\web\ServerErrorHttpException;
use yii\web\UploadedFile;

/**
 * OrderGroupController implements the CRUD actions for OrderGroup model.
 */
class OrderGroupController extends Controller
{

    /**
     * @return mixed
     */
    public  function actions()
    {
        return ArrayHelper::merge(parent::actions(), [
            'edit-offline' => [
                'class' => EditableColumnAction::className(),
                'modelClass' => OrderGroup::className(),
                'outputValue' => function($model, $attribute, $key, $index) {
                    $orderList = $model->subOrders;
                    foreach ($orderList as $order) {
                        $order->$attribute = $model->$attribute;
                        $order->save();
                    }
                    return $model->$attribute;
                },
                'outputMessage' => function($model, $attribute, $key, $index) {
                    return '';
                },
                'showModelErrors' => true,
                'errorOptions' => ['header' => '']
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all OrderGroup models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrderGroupSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $importForm = new OrderGroupImportForm();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'importForm' => $importForm,
        ]);
    }

    /**
     * Displays a single OrderGroup model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        ini_set('memory_limit', '1G');
        $this->layout = 'new.php';
        $model = $this->findModel($id);

        //  判定订单是不是积分兑换
        $integralOrder = false;
        if (!empty($model->orders) && count($model->orders) == 1) {
            if ($model->orders[0]['extension_code'] == 'integral_exchange') {
                $integralOrder = true;
            }
        }

        $query = Goods::find()->where([
            'is_delete' => 0,
        ])->andWhere([
            '>',
            'goods_number',
            0,
        ]);

        $allGoodsList = [];
        foreach ($query->each(100) as $goods) {
            $allGoodsList[] = [
                'goodsId' => $goods->goods_id,
                'goodsName' => $goods->goods_name. '('. $goods->goods_sn. ')',
                'minNumber' => $goods->start_num,
                'goodsSn' => $goods->goods_sn,
            ];
        }

        $userList = Users::find()->select([
            'user_id',
            'user_name',
            'nickname',
            'mobile_phone',
        ])->where([
            'not',
            [
                'mobile_phone' => '',
            ]
        ])->asArray()->all();

        $userData = [];
        foreach ($userList as $user) {
            $item = [
                'user_id' => $user['user_id'],
                'user_name' => '(id:'. $user['user_id']. ')'. $user['user_name']. '(昵称:'. $user['nickname']. ')(手机号码：'. $user['mobile_phone']. ')',
            ];
            $userData[] = $item;
        }

        return $this->render('view', [
            'model' => $model,
            'goodsData' => $allGoodsList,
            'integralOrder' => $integralOrder,
            'provinceMap' => Region::getProvinceMap(),
            'userData' => $userData,
        ]);
    }

    /**
     * Creates a new OrderGroup model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new OrderGroup();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing OrderGroup model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing OrderGroup model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the OrderGroup model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return OrderGroup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OrderGroup::find()->with([
                'orders',
                'orders.ordergoods',
                'orders.deliveryOrder',
                'orders.deliveryOrder.deliveryGoods'
            ])->where([
                'id' => $id
            ])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * 取消订单
     */
    public function actionCancel() {
        $note = Yii::$app->request->post('note');
        if (empty($note)) {
            throw new BadRequestHttpException('请填写备注信息', 1);
        }

        $id = Yii::$app->request->post('id');
        if (empty($id)) {
            throw new BadRequestHttpException('缺少总单号', 2);
        }

        $orderGroup = OrderGroup::findOne([
            'id' => $id,
        ]);

        if (empty($orderGroup)) {
            throw new BadRequestHttpException('未找到订单', 3);
        }

        $orderList = $orderGroup->orders;
        if (empty($orderList)) {
            throw new BadRequestHttpException('总单下没有子单', 4);
        }

        foreach ($orderList as $order) {
            $order->cancel($note);
        }

        $orderGroup->setupOrderStatus();
        $orderGroup->save();

        $this->redirect([
            'view',
            'id' => $orderGroup->id,
        ]);
    }

    /**
     * 总单支付
     */
    public function actionPay() {
        $note = Yii::$app->request->post('note');
        $id = Yii::$app->request->post('id');

        if (empty($note)) {
            $this->flashErrorMessage('缺少备注');
            return $this->gotoView($id);
        }

        if (empty($id)) {
            $this->flashErrorMessage('缺少总单号');
            return $this->gotoView($id);
        }

        $orderGroup = OrderGroup::findOne([
            'id' => $id,
        ]);

        if (empty($orderGroup)) {
            $this->flashErrorMessage('未找到总单');
            return $this->gotoView($id);
        }

        $originGroupStatus = $orderGroup['group_status'];

        $success = $orderGroup->pay($note);

        if ($originGroupStatus != OrderGroup::ORDER_GROUP_STATUS_PAID && $orderGroup['group_status'] == OrderGroup::ORDER_GROUP_STATUS_PAID) {
            //  订单支付成功后 发送短信的给采购和跟单
            $sms_paid_receiver = CacheHelper::getShopConfigValueByCode('sms_paid_receiver');
            if ($sms_paid_receiver) {
                $sms_paid_receiver = str_replace(',', ',', $sms_paid_receiver);
                $receivers = explode(',', $sms_paid_receiver);

                $content = CacheHelper::getShopConfigValueByCode('sms_order_paid');
                $content_to_servicer = str_replace(
                    ['#order_sn#', '#consignee#', '#mobile#'],
                    [$orderGroup['group_id'], $orderGroup['consignee'], $orderGroup['mobile']],
                    $content
                );
                foreach ($receivers as $receiver) {
                    SMSHelper::sendSms($receiver, $content_to_servicer);
                }
            }

            OrderGroupHelper::sendCouponAfterPaid($orderGroup['group_id']. 'O'. '0'. 'O'. '0');
        }


        if ($success) {
            $this->flashSuccess('订单已付款');
            return $this->gotoView($id);
        }
        else {
            return $this->gotoView($id);
        }
    }

    public function actionShipping() {
        $id = Yii::$app->request->post('id');
        $data = Yii::$app->request->post('data');

        if (empty($id)) {
            $this->flashErrorMessage('缺少订单ID');
            return $this->gotoView($id);
        }

        if (empty($data)) {
            $this->flashErrorMessage('缺少数据');
            return $this->gotoView($id);
        }

        $orderGroup = OrderGroup::findOne([
            'id' => $id,
        ]);

        if (empty($orderGroup)) {
            $this->flashErrorMessage('未找到订单');
            return $this->gotoView($id);
        }

        $orderGroup->shipping($data);

        return $this->gotoView($id);
    }

    public function actionAdvanceShipping() {
        $id = Yii::$app->request->post('id');
        $data = Yii::$app->request->post('data');
        $note = Yii::$app->request->post('note');

        if (empty($data)) {
            return $this->gotoView($id);
        }

        foreach ($data as $orderId => $orderShippingInfo) {
            //订单商品的列表
            $orderGoodsList = $orderShippingInfo['orderGoodsList'];
            //物流信息
            $shippingInfo = $orderShippingInfo['shippingInfo'];
            //运费信息
            $shippingFee = $orderShippingInfo['shippingFee'];

            if (empty($orderGoodsList)) {
                continue;
            }

            $totalGoodsNumber = 0;
            foreach ($orderGoodsList as $goodsNumber) {
                $totalGoodsNumber += $goodsNumber;
            }
            if ($totalGoodsNumber == 0) {
                continue;
            }

            $orderInfo = OrderInfo::findOne([
                'order_id' => $orderId,
            ]);

            $orderInfo->note = $note;

            $orderInfo->advanceShipping($orderGoodsList, $shippingInfo, $shippingFee);
        }

        $this->gotoView($id);
    }

    public function actionShipped() {
        $id = Yii::$app->request->post('id');
        $note = Yii::$app->request->post('note');

        if (empty($id)) {
            throw new BadRequestHttpException('缺少总单ID');
        }

        $orderGroup = OrderGroup::find()->where([
            'id' => $id,
        ])->one();
        $orderGroup->shipped($note);

        return $this->gotoView($id);
    }

    private function modify($force = false) {
        $id = Yii::$app->request->post('id');
        $recIdList = Yii::$app->request->post('recIdList');
        $goodsIdList = Yii::$app->request->post('goodsIdList');
        $note = Yii::$app->request->post('note');

        if (empty($id)) {
            return [
                'code' => 1,
                'msg' => '缺少总单ID',
            ];
        }

        $percent = 0.96;

        $orderGroup = OrderGroup::find()->where([
            'id' => $id,
        ])->one();

        $transaction = OrderGroup::getDb()->beginTransaction();

        try {
            foreach ($orderGroup->orders as $order) {
                $order->note = $note ?: '';
                //处理订单中已经存在的商品
                foreach ($order->ordergoods as $orderGoods) {
                    //不在里面说明已经删掉了
                    if (!empty($recIdList[$orderGoods->rec_id])) {
                        $goodsNumber = $recIdList[$orderGoods->rec_id]['goods_number'];
                        $orderGoods->goods_number = $goodsNumber;
                        if ($orderGoods->is_gift == OrderGoods::IS_GIFT_NO) {
                            if (!isset($recIdList[$orderGoods->rec_id]['goods_price']) || !is_numeric($recIdList[$orderGoods->rec_id]['goods_price'])) {
                                $newPrice = GoodsHelper::getFinalPrice($orderGoods->goods, $orderGoods->goods_number, $orderGroup->users->user_rank);
                            }
                            else {
                                $newPrice = $recIdList[$orderGoods->rec_id]['goods_price'];
                                if (!$force) {
                                    $orgPrice = GoodsHelper::getFinalPrice($orderGoods->goods, $orderGoods->goods_number, $orderGroup->users->user_rank);
                                    $minPrice = NumberHelper::price_format($orgPrice * $percent);
                                    if ($newPrice < $minPrice) {
                                        throw new BadRequestHttpException('商品定价过低 goodsSn = '. $orderGoods['goods_sn']. ', minPrice = '. $minPrice. ', newPrice = '. $newPrice);
                                    }
                                }
                            }

                            $orderGoods->goods_price = $newPrice;
                            $orderGoods->pay_price = $newPrice;
                        }

                        if (!$orderGoods->save()) {
                            throw new \Exception(json_encode($orderGoods->errors));
                        }
                    }
                    else {
                        $order->unlink('ordergoods', $orderGoods, true);
                    }
                }
                if (empty($order->ordergoods)) {
                    PayLog::deleteAll([
                        'order_id' => $order->order_id,
                    ]);
                    $order->delete();
                }
            }

            //这里是需要新增的商品
            if (!empty($goodsIdList)) {
                $goodsList = Goods::find()->with([
                    'supplierUser',
                    'brand',
                    'brand.supplierUser',
                ])->where([
                    'goods_id' => array_keys($goodsIdList),
                ])->all();
                $goodsBySupplier = [];
                $goodsByBrand = [];
                foreach ($goodsList as $goods) {
                    if (!empty($goods->supplier_user_id)) {
                        $goodsBySupplier[$goods->supplier_user_id][] = $goods;
                    }
                    elseif (!empty($goods->brand->supplier_user_id)) {
                        $goodsBySupplier[$goods->brand->supplier_user_id][] = $goods;
                    }
                    else {
                        $goodsByBrand[$goods->brand_id][] = $goods;
                    }
                }

                $orderBySupplier = OrderInfo::find()->where([
                    'group_id' => $orderGroup->group_id,
                ])->andWhere([
                    'supplier_user_id' => array_keys($goodsBySupplier),
                ])->indexBy('supplier_user_id')->all();

                foreach ($goodsBySupplier as $supplierUserId => $goodsList) {
                    //有相应的订单了，那就创建订单商品并且跟订单做关联
                    if (!empty($orderBySupplier[$supplierUserId])) {
                        $orderInfo = $orderBySupplier[$supplierUserId];
                    }
                    //没有这个供应商的订单，先创建订单，再关联商品和总单
                    else {
                        $orderInfo = OrderInfo::createFromOrderGroup($orderGroup);
                        $orderInfo->supplier_user_id = $supplierUserId;
                        if (!$orderInfo->save()) {
                            throw new \Exception(json_encode($orderInfo->errors));
                        }
                    }

                    foreach ($goodsList as $goods) {
                        $newOrderGoods = OrderGoods::createFromGoods($goods);
                        $newOrderGoods->goods_number = $goodsIdList[$goods->goods_id]['goods_number'];
                        if (!isset($goodsIdList[$goods->goods_id]['goods_price']) || !is_numeric($goodsIdList[$goods->goods_id]['goods_price'])) {
                            $goodsPrice = GoodsHelper::getFinalPrice($goods, $newOrderGoods->goods_number, $orderGroup->users->user_rank);
                        }
                        else {
                            $goodsPrice = $goodsIdList[$goods->goods_id]['goods_price'];
                            if (!$force) {
                                $orgPrice = GoodsHelper::getFinalPrice($goods, $newOrderGoods->goods_number, $orderGroup->users->user_rank);
                                $minPrice = NumberHelper::price_format($orgPrice * $percent);
                                if ($goodsPrice < $minPrice) {
                                    throw new BadRequestHttpException('商品定价过低 goodsSn = '. $goods['goods_sn']. ', minPrice = '. $minPrice. ', goodsPrice = '. $goodsPrice);
                                }
                            }
                        }

                        $newOrderGoods->goods_price = $goodsPrice;
                        $newOrderGoods->pay_price = $goodsPrice;
                        $orderInfo->link('ordergoods', $newOrderGoods);
                        if ($newOrderGoods->hasErrors()) {
                            throw new \Exception(json_encode($newOrderGoods->errors));
                        }
                    }

                    $orderInfo->recalcGoodsAmount();
                    if (!$orderInfo->save()) {
                        throw new \Exception(json_encode($orderInfo->errors));
                    }

                    $payLog = new PayLog();
                    $payLog->order_id = $orderInfo->order_id;
                    $payLog->order_amount = $orderInfo->order_amount;
                    $payLog->order_type = 0;
                    $payLog->is_paid = 0;

                    $orderInfo->link('payLog', $payLog);
                    if ($payLog->hasErrors()) {
                        throw new \Exception(json_encode($payLog->errors));
                    }
                }

                $orderByBrand = OrderInfo::find()->where([
                    'group_id' => $orderGroup->group_id,
                ])->andWhere([
                    'brand_id' => array_keys($goodsByBrand),
                ])->indexBy('brand_id')->all();
                foreach ($goodsByBrand as $brandId => $goodsList) {
                    //有相应的订单了，那就创建订单商品并且跟订单做关联
                    if (!empty($orderByBrand[$brandId])) {
                        $orderInfo = $orderByBrand[$brandId];
                    }
                    //没有这个供应商的订单，先创建订单，再关联商品和总单
                    else {
                        $orderInfo = OrderInfo::createFromOrderGroup($orderGroup);
                        $orderInfo->brand_id = $brandId;
                        if (!$orderInfo->save()) {
                            throw new \Exception(json_encode($orderInfo->errors));
                        }
                    }

                    foreach ($goodsList as $goods) {
                        $newOrderGoods = OrderGoods::createFromGoods($goods);
                        $newOrderGoods->goods_number = $goodsIdList[$goods->goods_id]['goods_number'];

                        if (!isset($goodsIdList[$goods->goods_id]['goods_price']) || !is_numeric($goodsIdList[$goods->goods_id]['goods_price'])) {
                            $goodsPrice = GoodsHelper::getFinalPrice($goods, $newOrderGoods->goods_number, $orderGroup->users->user_rank);
                        }
                        else {
                            $goodsPrice = $goodsIdList[$goods->goods_id]['goods_price'];
                            if (!$force) {
                                $orgPrice = GoodsHelper::getFinalPrice($goods, $newOrderGoods->goods_number, $orderGroup->users->user_rank);
                                $minPrice = NumberHelper::price_format($orgPrice * $percent);
                                if ($goodsPrice < $minPrice) {
                                    throw new BadRequestHttpException('商品定价过低 goodsSn = '. $goods['goods_sn']. ', minPrice = '. $minPrice. ', goodsPrice = '. $goodsPrice);
                                }
                            }
                        }

                        $newOrderGoods->goods_price = $goodsPrice;
                        $newOrderGoods->pay_price = $goodsPrice;
                        $orderInfo->link('ordergoods', $newOrderGoods);
                        if ($newOrderGoods->hasErrors()) {
                            throw new \Exception(json_encode($newOrderGoods->errors));
                        }
                    }

                    $orderInfo->recalcGoodsAmount();
                    if (!$orderInfo->save()) {
                        throw new \Exception(json_encode($orderInfo->errors));
                    }

                    $payLog = new PayLog();
                    $payLog->order_id = $orderInfo->order_id;
                    $payLog->order_amount = $orderInfo->order_amount;
                    $payLog->order_type = 0;
                    $payLog->is_paid = 0;

                    $orderInfo->link('payLog', $payLog);
                    if ($payLog->hasErrors()) {
                        throw new \Exception(json_encode($payLog->errors));
                    }
                }
            }

            $orderGroup = OrderGroup::find()->where([
                'id' => $id,
            ])->joinWith([
                'orders',
            ])->one();

            foreach ($orderGroup->orders as $order) {
                $order->recalcGoodsAmount();
                if (!$order->save()) {
                    throw new \Exception(json_encode($order->errors));
                }
            }

            $orderGroup->setupOrderStatus();
            $orderGroup->syncFeeInfo();
//            $orderGroup->recalcDiscount(false);
            if (!$orderGroup->save()) {
                throw new \Exception(json_encode($orderGroup->errors));
            }

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw new ServerErrorHttpException('e = '. $e->getMessage(), 0, $e);
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw new ServerErrorHttpException('e = '. $e->getMessage(), 0, $e);
        }

        $this->gotoView($id);
    }

    public function actionModify() {
        $this->modify();
    }

    public function actionForceModify() {
        $this->modify(true);
    }

    public function actionRefund() {
        $id = Yii::$app->request->post('id');
        $recIdList = Yii::$app->request->post('recIdList')  ;
        $note = Yii::$app->request->post('note');

        if (empty($id)) {
            throw new BadRequestHttpException('缺少订单ID');
        }

        $orderGroup = OrderGroup::find()->where([
            'id' => $id,
        ])->one();

        if (empty($orderGroup)) {
            $this->flashErrorMessage('未找到订单');
            return $this->gotoView($id);
        }

        if (empty($recIdList)) {
            $this->flashErrorMessage('缺少数据');
            return $this->gotoView($id);
        }

        $orderIds = array_keys($recIdList);
        $orderInfos = OrderInfo::find()->where([
            'order_id' => $orderIds,
        ])->all();

        foreach ($orderInfos as $orderInfo) {
            if (empty($recIdList[$orderInfo->order_id])) {
                continue;
            }

            if (($orderInfo->order_status == OrderInfo::ORDER_STATUS_UNCONFIRMED ||
                $orderInfo->order_status == OrderInfo::ORDER_STATUS_CONFIRMED)
                && $orderInfo->pay_status == OrderInfo::PAY_STATUS_UNPAYED
                && $orderInfo->shipping_status == OrderInfo::SHIPPING_STATUS_UNSHIPPED) {
                $this->flashErrorMessage('未付款的订单不能退款/退货');
                continue;
            }

            $backOrder = BackOrder::createByOrderInfo($orderInfo);
            $backOrder->reason = $note;
            if (!$backOrder->save()) {
                continue;
            }

            $recIds = $recIdList[$orderInfo->order_id];

            $orderGoodsList = OrderGoods::find()->where([
                'rec_id' => array_keys($recIds),
            ])->all();

            foreach ($orderGoodsList as $orderGoods) {
                if (!empty($recIds[$orderGoods->rec_id])) {

                    $backNumber = $recIds[$orderGoods->rec_id];
                    $oldBackNumber = $orderGoods->back_number;

                    //保证退货数量不超过商品总数或者已发货总数
                    if ($orderGoods->send_number > 0) {
                        if ($oldBackNumber + $backNumber > $orderGoods->send_number) {
                            $backNumber = $orderGoods->send_number - $oldBackNumber;
                        }
                    }
                    if ($oldBackNumber + $backNumber > $orderGoods->goods_number) {
                        $backNumber = $orderGoods->goods_number - $oldBackNumber;
                    }
                    if ($backNumber < 0) {
                        $backNumber = 0;
                    }

                    if ($backNumber == 0) {
                        continue;
                    }

                    $orderGoods->back_number += $backNumber;
                    $orderGoods->save();

                    $backGoods = BackGoods::createFromOrderGoods($orderGoods);
                    $backGoods->send_number = $backNumber;
                    $backOrder->link('backGoods', $backGoods);
                }
            }
        }

//        $orderGroup->recalcDiscount();
        $orderGroup->save();

        return $this->gotoView($id);
    }

    public function actionRefundAll() {
        $id = Yii::$app->request->post('id');
        $note = Yii::$app->request->post('note');
        $orderGroup = OrderGroup::find()->with([
            'users',
        ])->where([
            'id' => $id,
        ])->one();

        if (empty($id) || empty($orderGroup)) {
            throw new BadRequestHttpException('未找到订单');
        }

        foreach ($orderGroup->orders as $order) {
            $order->refund($note);
        }

        $orderGroup->recalcDiscount();
        $orderGroup->save();

        return $this->gotoView($id);
    }

    public function actionDeliveryInfo() {
        $this->layout = false;
        Yii::$app->response->format = Response::FORMAT_JSON;
        $deliveryId = Yii::$app->request->get('id');

        if (empty($deliveryId)) {
            return [
                'code' => 1,
                'msg' => '缺少发货单ID',
            ];
        }

        $deliveryOrder = DeliveryOrder::find()->with([
            'deliveryGoods',
            'deliveryGoods.goods',
        ])->where([
            'delivery_id' => $deliveryId,
        ])->one();

        if (empty($deliveryOrder)) {
            die(json_encode([
                'code' => 2,
                'msg' => '未找到订单',
            ]));
        }

        //分割成快递名和单号的数组
        $invoiceNoArray = explode(':', str_replace('：', ':', trim($deliveryOrder->invoice_no)));
        if (count($invoiceNoArray) >= 2) {
            $shippingName = $invoiceNoArray[0];
            $shippingInvoiceNo = $invoiceNoArray[1];
            $expressCompany = 'auto';
            if (strstr($shippingName, '申通')) {
                $expressCompany = 'sto';
            }
            elseif (strstr($shippingName, '圆通')) {
                $expressCompany = 'yto';
            }
            elseif (strstr($shippingName, '德邦')) {
                $expressCompany = 'deppon';
            }
            elseif (strstr($shippingName, '天地华宇')) {
                $expressCompany = 'hoau';
            }

            $shippingInfo = ShippingHelper::queryShippingInfo($shippingInvoiceNo, $expressCompany);
            if ($shippingInfo['status'] === '0') {
                if (!empty($shippingInfo['result']['deliverystatus'])) {
                    if ($shippingInfo['result']['deliverystatus'] == '1') {
                        $shippingInfo['result']['format_delivery_status'] = '在途中';
                    }
                    else if ($shippingInfo['result']['deliverystatus'] == '2') {
                        $shippingInfo['result']['format_delivery_status'] = '派建中';
                    }
                    else if ($shippingInfo['result']['deliverystatus'] == '3') {
                        $shippingInfo['result']['format_delivery_status'] = '已签收';
                    }
                    else if ($shippingInfo['result']['deliverystatus'] == '4') {
                        $shippingInfo['result']['format_delivery_status'] = '派送失败(拒签等)';
                    }
                }
            }
            else {
                return [
                    'code' => 3,
                    'msg' => '查询不到物流信息',
                ];
            }

            $deliveryItem = [
                'shipping_name' => $shippingName,
                'shipping_sn' => $shippingInvoiceNo,
                'shipping_info' => $shippingInfo,
            ];



            foreach ($deliveryOrder->deliveryGoods as $goods) {
                $deliveryItem['goods_list'][] = [
                    'goods_thumb' => ImageHelper::get_image_path(empty($goods->goods) ? '' : $goods->goods->goods_thumb),
                    'send_number' => $goods->send_number,
                ];
            }

            $data = $this->render('_delivery-info.php', [
                'deliveryItem' => $deliveryItem,
            ]);

            return [
                'code' => 0,
                'data' => $data,
            ];
        }
        return [
            'code' => 4,
            'msg' => '无冒号分割',
        ];
    }

    public function actionCalcDiscount() {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $id = Yii::$app->request->post('id');
        $recIdList = Yii::$app->request->post('recIdList');
        $goodsIdList = Yii::$app->request->post('goodsIdList');

        if (empty($id)) {
            return [
                'code' => 1,
                'msg' => '缺少总单ID',
            ];
        }

        $orderGroup = OrderGroup::find()->with([
            'users',
        ])->where([
            'id' => $id,
        ])->one();

        $orderGoodsList = [];
        if (!empty($recIdList)) {
            $orderGoodsList = OrderGoods::find()->with([
                'goods',
            ])->where([
                'rec_id' => array_keys($recIdList),
            ])->all();
        }

        $goodsList = [];
        if (!empty($goodsIdList)) {
            $goodsList = Goods::find()->where([
                'goods_id' => array_keys($goodsIdList),
            ])->all();
        }

        $resultGoodsData = [];
        $goodsListForCalc = [];

        foreach ($orderGoodsList as $orderGoods) {
            if (!empty($orderGoods['rec_id'])) {
                $goodsNumber = $recIdList[$orderGoods['rec_id']]['goods_number'];

                if (!isset($recIdList[$orderGoods->rec_id]['goods_price']) || !is_numeric($recIdList[$orderGoods->rec_id]['goods_price'])) {
                    $goodsPrice = NumberHelper::price_format(GoodsHelper::getFinalPrice($orderGoods->goods, $goodsNumber, empty($orderGroup->users)? 1:$orderGroup->users->user_rank));
                }
                else {
                    $goodsPrice = $recIdList[$orderGoods['rec_id']]['goods_price'];
                }


                $item = [
                    'goods_id' => $orderGoods['goods_id'],
                    'goods_number' => $goodsNumber,
                    'selected' => 1,
                    'goods_price' => $goodsPrice,
                ];
                $goodsListForCalc[] = $item;

                $orderKeyGoodsMap[$orderGoods->order_id][] = $item;

                $resultGoodsData['recList'][] = [
                    'rec_id' => $orderGoods['rec_id'],
                    'goods_id' => $orderGoods['goods_id'],
                    'goods_name' => $orderGoods['goods_name'],
                    'goods_number' => $goodsNumber,
                    'goods_price' => $goodsPrice,
                    'goods_thumb' => ImageHelper::get_image_path($orderGoods['goods']['goods_thumb']),
                    'goods_total' => NumberHelper::price_format($goodsNumber * $goodsPrice),
                    'buy_by_box' => $orderGoods['goods']['buy_by_box'],
                    'number_per_box' => $orderGoods['goods']['number_per_box'],
                    'store_number' => $orderGoods['goods']['goods_number'],
                ];
            }
        }

        foreach ($goodsList as $goods) {

            if (!isset($goodsIdList[$goods->goods_id]['goods_price']) || !is_numeric($goodsIdList[$goods->goods_id]['goods_price'])) {
                $goodsPrice = GoodsHelper::getFinalPrice($goods, $goodsIdList[$goods['goods_id']], isset($orderGroup->users) ? $orderGroup->users->user_rank: 1);
            }
            else {
                $goodsPrice = $goodsIdList[$goods->goods_id]['goods_price'];
            }


            $orderId = $orderGroup->orders[0]->order_id;

            if (isset($goodsIdList[$goods['goods_id']]['goods_number'])) {
                $goodsNumber = $goodsIdList[$goods['goods_id']]['goods_number'];
            }
            else {
                $goodsNumber = $goods['start_num'];
            }

            $item = [
                'goods_id' => $goods['goods_id'],
                'goods_number' => $goodsNumber,
                'selected' => 1,
                'goods_price' => $goodsPrice,
            ];
            $goodsListForCalc[] = $item;

            $orderKeyGoodsMap[$orderId][] = $item;

            $resultGoodsData['goodsList'][] = [
                'goods_id' => $goods['goods_id'],
                'goods_name' => $goods['goods_name'],
                'goods_number' => $goodsNumber,
                'goods_price' => $goodsPrice,
                'goods_thumb' => ImageHelper::get_image_path($goods['goods_thumb']),
                'goods_total' => NumberHelper::price_format($goodsNumber * $goodsPrice),
                'buy_by_box' => $goods['buy_by_box'],
                'number_per_box' => $goods['number_per_box'],
                'store_number' => $goods['goods_number'],
            ];
        }

        $goodsAmount = 0;
        if (!empty($resultGoodsData['recList'])) {
            foreach ($resultGoodsData['recList'] as $item) {
                $goodsAmount += $item['goods_total'];
            }
        }

        if (!empty($resultGoodsData['goodsList'])) {
            foreach ($resultGoodsData['goodsList'] as $item) {
                $goodsAmount += $item['goods_total'];
            }
        }

        Yii::info('goodsListForCalc = '. VarDumper::export($goodsListForCalc), __METHOD__);

        $orderAmount = $goodsAmount + $orderGroup->shipping_fee - $orderGroup->discount - $orderGroup->money_paid;
        return [
            'code' => 0,
            'msg' => '有减价',
            'data' => [
                'moneyPaid' => $orderGroup->money_paid,
                'totalFee' => $goodsAmount + $orderGroup->shipping_fee - $orderGroup->discount,
                'discount' => $orderGroup->discount,
                'orderAmount' => $orderAmount,
                'goodsList' => $resultGoodsData ?: [],
            ]
        ];
    }

    public function actionExport() {
        ini_set('max_execution_time', 120);

        $searchModel = new OrderGroupSearch();
        $searchModel->export(Yii::$app->request->queryParams);

        $this->redirect(['index']);
    }

    public function actionExportDivide() {
        ini_set('max_execution_time', 120);
        ini_set('memory_limit', '1G');

        $searchModel = new OrderGroupSearch();
        $searchModel->exportDivide(Yii::$app->request->queryParams);

        $this->redirect(['index']);
    }

    public function actionImport() {
        ini_set('max_execution_time', 120);
        ini_set('memory_limit', '1G');

        $importForm = new OrderGroupImportForm();

        if (Yii::$app->request->isPost) {
            $importForm->file = UploadedFile::getInstance($importForm, 'file');
            $importForm->import();
        }

        return $this->redirect(['index']);
    }

    public function actionExportRegion() {
        ini_set('max_execution_time', 120);
        ini_set('memory_limit', '1G');
        $regionList = Region::find()->all();
        \moonland\phpexcel\Excel::export([
            'format' => 'Excel5',
            'fileName' => '区域导出',
            'models' => $regionList,
            'columns' => [
                'region_id',
                'region_name',
            ], //without header working, because the header will be get label from attribute label.
            'headers' => [
                'region_id' => '区域ID',
                'region_name' => '区域名',
            ],
        ]);
    }

    public function actionExportGoods() {
        ini_set('max_execution_time', 120);
        ini_set('memory_limit', '1G');
        $goodsList = Goods::find()->all();
        \moonland\phpexcel\Excel::export([
            'format' => 'Excel5',
            'fileName' => '商品ID导出',
            'models' => $goodsList,
            'columns' => [
                'goods_id',
                'goods_name',
                'goods_sn',
            ], //without header working, because the header will be get label from attribute label.
            'headers' => [
                'goods_id' => '商品ID',
                'goods_name' => '商品名',
                'goods_sn' => '货号(条码)',
            ],
        ]);
    }

    public function actionModifyUser() {

        Yii::$app->response->format = Response::FORMAT_JSON;

        $id = Yii::$app->request->post('id');
        if (empty($id)) {
            throw new BadRequestHttpException('缺少id');
        }

        $orderGroup = OrderGroup::find()->joinWith([
            'orderList orderList',
            'orders orders',
        ])->where([
            OrderGroup::tableName().'.id' => $id,
        ])->one();

        if (empty($orderGroup)) {
            throw new BadRequestHttpException('订单未找到');
        }

        if (!empty(Yii::$app->request->post('user_id'))) {
            $userId = Yii::$app->request->post('user_id');
            $userModel = Users::find()->where([
                'user_id' => $userId,
            ])->one();

            if (empty($userModel)) {
                Yii::$app->session->setFlash('failed', '找不到用户');
                return $this->redirect(Url::to([
                    '/order-group/view',
                    'id' => $orderGroup->id,
                ]));
            }
        }

        //收件人
        if (!empty(trim(Yii::$app->request->post('consignee')))) {
            $consignee = trim(Yii::$app->request->post('consignee'));
        }

        //收件人手机号码
        if (!empty(trim(Yii::$app->request->post('mobile')))) {
            $mobile = trim(Yii::$app->request->post('mobile'));
        }

        //省
        if (!empty(Yii::$app->request->post('province'))) {
            $province = Yii::$app->request->post('province');
            if (!is_numeric($province)) {
                unset($province);
            }
        }

        //市
        if (!empty(Yii::$app->request->post('city'))) {
            $city = Yii::$app->request->post('city');
            if (!is_numeric($city)) {
                unset($city);
            }
        }

        //区
        if (null !== Yii::$app->request->post('district')) {
            $district = Yii::$app->request->post('district');
            if (!is_numeric($district)) {
                unset($district);
            }
        }

        //地址
        if (!empty(trim(Yii::$app->request->post('address')))) {
            $address = trim(Yii::$app->request->post('address'));
        }

        //备注
        if (!empty(trim(Yii::$app->request->post('note')))) {
            $note = trim(Yii::$app->request->post('note'));
            if (empty($note)) {
                Yii::$app->session->setFlash('failed', '请输入备注');
                return $this->redirect(Url::to([
                    '/order-group/view',
                    'id' => $orderGroup->id,
                ]));
            }
        }

        $transaction = OrderGroup::getDb()->beginTransaction();
        try {

            //修改用户
            if (isset($userId)) {
                $orderGroup->user_id = $userId;
            }

            //收件人
            if (!empty($consignee)) {
                $orderGroup->consignee = $consignee;
            }

            //收件人手机号码
            if (!empty($mobile)) {
                $orderGroup->mobile = $mobile;
            }

            //省
            if (!empty($province)) {
                $orderGroup->province = $province;
            }

            //市
            if (!empty($city)) {
                $orderGroup->city = $city;
            }

            //区
            if (isset($district)) {
                $orderGroup->district = $district;
            }

            //地址
            if (!empty($address)) {
                $orderGroup->address = $address;
            }

            //备注
            if (!empty($note)) {
                $orderGroup->note = $note;
            }

            if (!$orderGroup->save()) {
                throw new Exception('总单保存失败 group_id = '. $orderGroup->group_id);
            }

            foreach ($orderGroup->getSubOrders() as $orderInfo) {

                if (isset($note)) {
                    $orderInfo->note = $note;
                }

                //修改用户
                if (isset($userId)) {
                    $orderInfo->user_id = $userId;
                }

                //收件人
                if (!empty($consignee)) {
                    $orderInfo->consignee = $consignee;
                }

                //收件人手机号码
                if (!empty($mobile)) {
                    $orderInfo->mobile = $mobile;
                }

                //省
                if (!empty($province)) {
                    $orderInfo->province = $province;
                }

                //市
                if (!empty($city)) {
                    $orderInfo->city = $city;
                }

                //区
                if (isset($district)) {
                    $orderInfo->district = $district;
                }

                //地址
                if (!empty($address)) {
                    $orderInfo->address = $address;
                }

                if (!$orderInfo->save()) {
                    throw new Exception('订单保存失败 order_id = '. $orderInfo->order_id);
                }
            }

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('failed', $e->getMessage());
        } catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('failed', $e->getMessage());
        }

        $this->redirect(Url::to([
            '/order-group/view',
            'id' => $orderGroup->id,
        ]));
    }
}
