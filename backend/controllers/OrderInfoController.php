<?php

namespace backend\controllers;

use backend\models\DeliveryGoods;
use backend\models\DeliveryOrder;
use backend\models\Integral;
use backend\models\OrderGroupSearch;
use backend\models\ServicerDivideRecord;
use backend\models\ServicerStrategy;
use backend\models\Users;
use common\helper\CacheHelper;
use common\helper\DateTimeHelper;
use common\helper\NumberHelper;
use common\helper\PaymentHelper;
use backend\models\BackOrder;
use backend\models\BackGoods;
use common\models\Brand;
use common\models\Goods;
use backend\models\OrderAction;
use backend\models\OrderGoods;
use common\models\Region;
use common\models\ServicerSpecStrategy;
use common\models\ShopConfig;
use Yii;
use backend\models\OrderInfo;
use common\models\OrderInfoSearch;
use yii\base\Exception;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\helper\OfficeHelper;
use yii\web\ServerErrorHttpException;

/**
 * OrderInfoController implements the CRUD actions for OrderInfo model.
 */
class OrderInfoController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => [
                    'index',
                    'view',
                    'create',
                    'update',
                    'delete',
                    'export',
                    'pay',
                    'shipping',
                    'done',
                    'ask-for-refund',
                    'refund-done',
                    'ask-for-return',
                    'return',
                    'return-done',
                    'advance-shipping',
                ],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    'pay' => ['POST'],
                    'shipping' => ['POST'],
                    'done' => ['POST'],
                    'ask-for-refund' => ['POST'],
                    'refund-done' => ['POST'],
                    'ask-for-return' => ['POST'],
                    'return' => ['POST'],
                    'return-done' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all OrderInfo models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrderInfoSearch();
        $params = Yii::$app->request->queryParams;
        $dataProvider = $searchModel->searchForExport($params);
        $paymentMap = PaymentHelper::$paymentMap;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'paymentMap' => $paymentMap,
            'isGiftStyleMap' => OrderGoods::$isGiftStyleMap,
            'params' => $params,
        ]);
    }

    /**
     * Displays a single OrderInfo model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $actions = [];
        $orderCsStatus = OrderInfo::getOrderCsStatusNo([
            'order_status' => $model->order_status,
            'pay_status' => $model->pay_status,
            'shipping_status' => $model->shipping_status,
        ]);
        if ($orderCsStatus == OrderInfo::ORDER_CS_STATUS_TO_BE_PAID) {
            $actions['cancel'] = '取消订单';
            $actions['pay'] = '支付';
        }
        if ($orderCsStatus == OrderInfo::ORDER_CS_STATUS_TO_BE_SHIPPED) {
            $actions['shipping'] = '快速发货(发所有待发的货)';
            $actions['ask-for-refund'] = '申请退款';
            if (Yii::$app->user->can('/order-info/cancel')) {
                $actions['cancel'] = '取消订单';
            }
        }
        if ($orderCsStatus == OrderInfo::ORDER_CS_STATUS_TO_BE_REFUNDED) {
            $actions['disagree-refund'] = '驳回（回到已付款状态）';
            $actions['refund-done'] = '退款完成';
        }
        if ($orderCsStatus == OrderInfo::ORDER_CS_STATUS_SHIPPED) {
            $actions['done'] = '完成';
            $actions['ask-for-return'] = '申请退货';
        }
        if ($orderCsStatus == OrderInfo::ORDER_CS_STATUS_COMPLETED_OVER) {
            $actions['ask-for-return'] = '申请退货';
        }
        if ($orderCsStatus == OrderInfo::ORDER_CS_STATUS_ASK_4_RETURN) {
            $actions['agree-return'] = '同意退货';
            $actions['disagree-return'] = '驳回（回到已发货状态）';
        }
        if ($orderCsStatus == OrderInfo::ORDER_CS_STATUS_TO_BE_RETURNED) {
            $actions['return'] = '用户已退货';
            $actions['disagree-return'] = '驳回（回到已发货状态）';
        }
        if ($orderCsStatus == OrderInfo::ORDER_CS_STATUS_RETURNED) {
            $actions['return-done'] = '退货完成';
        }
        if ($orderCsStatus == OrderInfo::ORDER_CS_STATUS_SHIPPED_PART) {
            $actions['shipped'] = '发货完成(单纯修改订单状态，不生成发货单，不产生服务商分成)';
        }

        return $this->render('update', [
            'model' => $model,
            'actions' => $actions,
            'isGiftStyleMap' => OrderGoods::$isGiftStyleMap,
        ]);
    }

    public function actionCancel() {
        $note = Yii::$app->request->post('note');
        $orderId = Yii::$app->request->post('orderId');

        if (empty($note)) {
            throw new BadRequestHttpException('请填写操作日志');
        }

        if (empty($orderId)) {
            throw new BadRequestHttpException('请填写订单号');
        }

        $orderInfo = OrderInfo::findOne([
            'order_id' => $orderId,
        ]);

        //检查当前状态是否可以改为已付款
        if ($orderInfo->order_status != OrderInfo::ORDER_STATUS_CONFIRMED &&
            $orderInfo->order_status != OrderInfo::ORDER_STATUS_UNCONFIRMED &&
            $orderInfo->pay_status != OrderInfo::PAY_STATUS_UNPAYED &&
            $orderInfo->pay_status != OrderInfo::PAY_STATUS_PAYED &&
            $orderInfo->shipping_status != OrderInfo::SHIPPING_STATUS_UNSHIPPED
        ) {
            Yii::$app->session->setFlash('error', '只有未付款、申请退款的订单才能改为已付款');
            return $this->gotoView($orderId);
        }

        $orderInfo->note = $note;
        $orderInfo->order_status = OrderInfo::ORDER_STATUS_CANCELED;
        $orderInfo->pay_status = OrderInfo::PAY_STATUS_UNPAYED;
        $orderInfo->shipping_status = OrderInfo::SHIPPING_STATUS_UNSHIPPED;

        if (!$orderInfo->save()) {
            $this->flashError($orderInfo);
        }
        return $this->gotoView($orderId);
    }

    /**
     * Creates a new OrderInfo model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new OrderInfo();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->order_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing OrderInfo model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {

            $model->recalcOrderAmount();

            if ($model->save()) {

                $orderGroup = $model->orderGroup;
                if (!empty($orderGroup)) {
                    $orderGroup->setupOrderStatus();
                    $orderGroup->syncFeeInfo();
                    $orderGroup->save();
                }

                Yii::$app->session->setFlash('success', '更新成功');
                return $this->gotoView($id);
            }
            else {
                Yii::$app->session->setFlash('error', '更新失败 '. VarDumper::export($model->errors));
                return $this->gotoView($id);
            }
        }
        else {
            Yii::$app->session->setFlash('error', '请通过post方式提交数据');
            return $this->gotoView($id);
        }
//        else {
//
//            $actions = [];
//            $orderCsStatus = OrderInfo::getOrderCsStatusNo([
//                'order_status' => $model->order_status,
//                'pay_status' => $model->pay_status,
//                'shipping_status' => $model->shipping_status,
//            ]);
//            if ($orderCsStatus == OrderInfo::ORDER_CS_STATUS_TO_BE_PAID) {
//                $actions['pay'] = '支付';
//            }
//            if ($orderCsStatus == OrderInfo::ORDER_CS_STATUS_TO_BE_SHIPPED) {
//                $actions['shipping'] = '发货';
//                $actions['ask-for-refund'] = '申请退款';
//            }
//            if ($orderCsStatus == OrderInfo::ORDER_CS_STATUS_TO_BE_REFUNDED) {
//                $actions['refund-done'] = '退款完成';
//            }
//            if ($orderCsStatus == OrderInfo::ORDER_CS_STATUS_SHIPPED) {
//                $actions['done'] = '完成';
//                $actions['ask-for-return'] = '申请退货';
//                $actions['shipping'] = '修改运单号';
//            }
//            if ($orderCsStatus == OrderInfo::ORDER_CS_STATUS_ASK_4_RETURN) {
//                $actions['agree-return'] = '同意退货';
//                $actions['disagree-return'] = '驳回（回到已发货状态）';
//            }
//            if ($orderCsStatus == OrderInfo::ORDER_CS_STATUS_TO_BE_RETURNED) {
//                $actions['return'] = '用户已退货';
//                $actions['disagree-return'] = '驳回（回到已发货状态）';
//            }
//            if ($orderCsStatus == OrderInfo::ORDER_CS_STATUS_RETURNED) {
//                $actions['return-done'] = '退货完成';
//            }
//
//            return $this->render('update', [
//                'model' => $model,
//                'actions' => $actions,
//            ]);
//        }
    }

    /**
     * Deletes an existing OrderInfo model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionExport()
    {
        ini_set('max_execution_time', 120);
        ini_set('memory_limit', '1G');
        //  定义表头，方便在调整各列显示顺序的时候 只调整表头数组就，不用调整各列具体值的数组顺序
        $data_header = [
            //  order_info
            'add_time' => '下单时间',
            'group_id' => '总单号',
            'order_sn' => '订单号',
            'managerName' => '客户归属人',
            'user_name' => '用户名',
            'consignee' => '收货人',
            'mobile' => '手机',
            'complete_address' => '地址',

            'brand_user_company_name' => '供应商名称',
            'brand_name' => '品牌名称',
            'brand_id' => '品牌ID',
            //  order_goods
            'goods_id' => '商品ID',
            'goods_name' => '商品名称',
            'goods_sn' => '货号',
            'goods_number' => '数量',
            'back_number' => '退款数量',
            'goods_price' => '应付单价',
            'pay_price' => '实付单价',
            'market_price' => '市场价',
            'categoryId' => '分类ID',
            'parentCategoryId' => '上级分类ID',
            'category' => '分类名',
            'parentCategory' => '上级分类名',
            'attrRegion' => '产地',

            'sku_amount' => '应付金额',
            'sku_pay_amount' => '实付金额',
            'order_status' => '订单状态',
            'pay_status' => '付款状态',
            'postscript' => '客户备注',
            'sample' => '小样配比',
            'shipping_name' => '快递名称',
            'shipping_fee' => '运费',
            'invoice_no' => '快递单号',
            'province' => '收货人所在省',
            'user_province' => '用户注册时填写的省',

            'extension_code' => '用户购买方式',

            'pay_name' => '支付名称',
            'alipay_out_trade_no' => '支付宝商户订单号',
            'wechat_out_trade_no' => '微信商户订单号',
            'yeepay_out_trade_no' => '易宝商户订单号',
            'pay_note' => '支付日志',
        ];
        $data_array[] = array_values($data_header);

        $searchModel = new OrderInfoSearch();
        $queryParams = Yii::$app->request->queryParams;
        $queryParams['page_size'] = 0;
        $dataProvider = $searchModel->searchForExport($queryParams, 'export');
        $model_list = $dataProvider->getModels();

        if ($model_list) {
            foreach ($model_list as $model) {

                $province = 0;
                $city = 0;

                if (!empty($model->users)) {
                    $province = $model->users->province;
                    $city = $model->users->city;
                }

                $managerName = '';

                if ($province > 0 || $city > 0) {
                    $regionList = [];
                    if ($province > 0) {
                        $regionList[] = $province;
                    }

                    if ($city > 0) {
                        $regionList[] = $city;
                    }

                    //看谁命中这个区域
                    $managerUser = \common\models\Users::find()->joinWith([
                        'userRegion userRegion',
                    ])->where([
                        'userRegion.region_id' => $regionList,
                    ])->andWhere([
                        'servicer_info_id' => 0,
                    ])->one();

                    if (!empty($managerUser)) {
                        $managerName = $managerUser->showName. '('. $managerUser->mobile_phone. ')';
                    }
                    else {
                        $managerName = '李丽(13510115932)';
                    }
                }


                $order_goods = OrderGoods::find()
                    ->joinWith('goods')
                    ->where([
                        'order_id' => $model->order_id
                    ])->all();

                foreach ($order_goods as $goods) {
                    if (!empty($goods['goods']) && !empty($goods['goods']['brand_id'])) {
                        $brand_id = $goods['goods']['brand_id'];
                    } else {
                        $brand_id = 0;
                    }

                    $formatExtensionCode = '';
                    if (!empty($model->extension_code)) {
                        $formatExtensionCode = OrderInfo::$extensionCodeMap[$model->extension_code];
                    }

                    $item = [
                        'add_time' => DateTimeHelper::getFormatCNDateTime($model->add_time),
                        'group_id' => $model->group_id,
                        'order_sn' => $model->order_sn,
                        'managerName' => $managerName,
                        'user_name' => empty($model->users) ? '' : $model->users->showName,
                        'consignee' => $model->consignee,
                        'mobile' => $model->mobile,
                        'complete_address' => Region::getUserAddress($model).' '.$model->address,

                        'brand_user_company_name' => ' ',
                        'brand_name' => '',
                        'brand_id' => $brand_id,
                        //  order_goods
                        'goods_id' => $goods['goods_id'],
                        'goods_name' => $goods->goods_name,
                        'goods_sn' => $goods->goods_sn ?: '',
                        'goods_number' => $goods->goods_number,
                        'back_number' => $goods->back_number,
                        'goods_price' => $goods->goods_price,
                        'pay_price' => $goods->pay_price,
                        'market_price' => $goods->market_price,
                        'categoryId' => $goods['goods']['category']['cat_id'],
                        'parentCategoryId' => $goods['goods']['category']['parent']['cat_id'],
                        'category' => $goods['goods']['category']['cat_name'],
                        'parentCategory' => $goods['goods']['category']['parent']['cat_name'],
                        'attrRegion' => $goods['goods']['goodsAttrRegionWithOutJoin']['attr_value'],

                        'sku_amount' => bcmul($goods->goods_price, $goods->goods_number, 2),
                        'sku_pay_amount' => bcmul($goods->pay_price, $goods->goods_number, 2),
                        'order_status' => OrderInfo::$order_status_map[$model->order_status],
                        'pay_status' => OrderInfo::$pay_status_map[$model->pay_status],
                        'postscript' => $model->postscript ?: '',
                        'sample' => $goods['sample'],
                        'shipping_name' => $model->shipping_name ?: '到付',
                        'shipping_fee' => $model->shipping_fee ?: 0.00,
                        'invoice_no' => $model->invoice_no ?: '',
                        'province' => isset($model->provinceRegion) ? $model->provinceRegion->region_name: '',
                        'user_province' => isset($model->users->provinceRegion) ? $model->users->provinceRegion->region_name: '',

                        'extension_code' => $formatExtensionCode,

                        'pay_name' => $model['pay_name'],
                        'alipay_out_trade_no' => empty($model['alipayInfo']) ? '' : $model['alipayInfo']['out_trade_no'],
                        'wechat_out_trade_no' => empty($model['wechatPayInfo']) ? '' : $model['wechatPayInfo']['out_trade_no'],
                        'yeepay_out_trade_no' => empty($model['yeePayInfo']) ? '' : $model['yeePayInfo']['out_trade_no'],
                        'pay_note' => $model->getPayNote(),
                    ];

                    $data_array[] = $item;
//                    $data_array[] = array_values($item);
                }

            }
        }

        $brandIdList = array_unique(array_column($data_array, 'brand_id'));
        $brandList = Brand::find()
            ->select(['brand_id', 'brand_name'])
            ->where(['brand_id' => $brandIdList])
            ->asArray()
            ->all();
        $brandMap = array_column($brandList, 'brand_name', 'brand_id');

        foreach ($data_array as &$data) {
            if (!empty($data['brand_id']) && isset($brandMap[$data['brand_id']])) {
                $data['brand_name'] = $brandMap[$data['brand_id']];
            }
            $data = array_values($data);
        }

        $file_name = '订单列表'.date('YmdHis');

        OfficeHelper::excelExport($file_name, $data_array);
    }

    protected function gotoView($id) {
        return $this->redirect(['view', 'id' => $id]);
    }

    protected function flash($key, $message) {
        Yii::$app->session->setFlash($key, $message);
    }

    protected function flashSuccess($message) {
        Yii::$app->session->setFlash('success', $message);
    }

    protected function flashError($model) {
        Yii::$app->session->setFlash('error', '操作失败 '. get_class($model). ', e = '. VarDumper::export($model->errors));
    }

    private function flashErrorMessage($message) {
        Yii::$app->session->setFlash('error', $message);
    }

    /**
     * 支付
     * @throws BadRequestHttpException
     */
    public function actionPay() {

        $note = Yii::$app->request->post('note');
        $orderId = Yii::$app->request->post('orderId');

        if (empty($note)) {
            throw new BadRequestHttpException('请填写操作日志');
        }

        if (empty($orderId)) {
            throw new BadRequestHttpException('请填写订单号');
        }

        $orderInfo = OrderInfo::findOne([
            'order_id' => $orderId,
        ]);

        //检查当前状态是否可以改为已付款
        if ($orderInfo->order_status != OrderInfo::ORDER_STATUS_UNCONFIRMED &&
            $orderInfo->order_status != OrderInfo::ORDER_STATUS_CONFIRMED && 
            $orderInfo->order_status != OrderInfo::ORDER_STATUS_ASK_4_REFUND
        ) {
            Yii::$app->session->setFlash('error', '只有未付款、申请退款的订单才能改为已付款');
            return $this->gotoView($orderId);
        }

        $orderInfo->note = $note;
        $orderInfo->order_status = OrderInfo::ORDER_STATUS_CONFIRMED;
        $orderInfo->pay_status = OrderInfo::PAY_STATUS_PAYED;
        $orderInfo->pay_time = DateTimeHelper::getFormatGMTTimesTimestamp();
        $orderInfo->shipping_status = OrderInfo::SHIPPING_STATUS_UNSHIPPED;

        $orderInfo->money_paid = $orderInfo['money_paid'] + $orderInfo['order_amount'];
        $orderInfo->order_amount = 0;
        $orderInfo->pay_id = 5;
        $orderInfo->pay_name = '线下支付';

        $payLog = $orderInfo->payLog;
        $payLog->is_paid = 1;
        $payLog->save();

        //先保存日志，这样可以保证每个操作都是有日志的
        if ($orderInfo->save()) {

            $orderGroup = $orderInfo->orderGroup;
            if (!empty($orderGroup)) {
                $orderGroup->setupOrderStatus();
                $orderGroup->syncFeeInfo();
                $orderGroup->syncTimeInfo();
                $orderGroup->save();
            }

            $message = '操作成功';

            //如果是支付减库存就在这里处理
            $config = CacheHelper::getShopConfigParams(['use_storage', 'stock_dec_time']);
            if ($config['use_storage']['value'] == '1' && $config['stock_dec_time']['value'] == ShopConfig::SDT_PAID) {
                foreach ($orderInfo->ordergoods as $goods) {
                    $goods->goods->goods_number -= $goods->goods_number;
                    if ($goods->goods->goods_number < 0) {
                        $goods->goods->goods_number = 0;
                    }
                    $goods->goods->save();
                }
            }

            //给用户分成积分
            if ($orderInfo->extension_code != 'integral_exchange' && $orderInfo->extension_code != 'group_buy') {
                $integral = floor(($orderInfo['goods_amount'] - $orderInfo['discount']) / 10);
                $time = DateTimeHelper::gmtime();
                $integralModel = new Integral();

                $integralModel['integral'] = $integral;
                $integralModel['user_id'] = $orderInfo['user_id'];
                $integralModel['pay_code'] = 'backend';
                $integralModel['out_trade_no'] = $note;
                $integralModel['note'] = $orderInfo['order_id'];
                $integralModel['status'] = 0;
                $integralModel['created_at'] = $time;
                $integralModel['updated_at'] = $time;

                if (!$integralModel->save()) {
                    $this->flashError($integralModel);
                }
                else {
                    $message .= ', 给用户增加冻结状态的积分：'. $integral. ', 积分流水ID = '. $integralModel->id;
                }
            }

            $this->flashSuccess($message);
        }
        //如果保存失败就删除日志
        else {
            $this->flashError($orderInfo);
        }

        return $this->gotoView($orderId);
    }

    /**
     * 发货
     * @throws BadRequestHttpException
     */
    public function actionShipping() {

        $note = Yii::$app->request->post('note');
        $orderId = Yii::$app->request->post('orderId');
        $shippingInfo = Yii::$app->request->post('shippingInfo');
        $shippingFee = Yii::$app->request->post('shippingFee');

        if (empty($note)) {
            throw new BadRequestHttpException('请返回填写操作日志');
        }

        if (empty($orderId)) {
            throw new BadRequestHttpException('请返回填写订单号');
        }

        if (empty($shippingInfo)) {
            throw new BadRequestHttpException('请返回填写物流信息');
        }

        $orderInfo = OrderInfo::findOne([
            'order_id' => $orderId,
        ]);
        if (empty($orderInfo)) {
            throw new BadRequestHttpException('没有找到订单');
        }

        //已确认，已分单，并且已经付款可以操作发货
        if (($orderInfo->order_status != OrderInfo::ORDER_STATUS_CONFIRMED
            && $orderInfo->order_status != OrderInfo::ORDER_STATUS_SPLITED)
        ) {
            Yii::$app->session->setFlash('error', '只有已确认，已分单的订单才能发货');
            return $this->gotoView($orderId);
        }

        //未付款订单不允许操作发货
        if ($orderInfo->pay_status != OrderInfo::PAY_STATUS_PAYED) {
            Yii::$app->session->setFlash('error', '未付款订单不允许操作发货');
            return $this->gotoView($orderId);
        }

        //已发货的订单不允许重复发货
        if ($orderInfo->shipping_status == OrderInfo::SHIPPING_STATUS_SHIPPED
            || $orderInfo->shipping_status == OrderInfo::SHIPPING_STATUS_SHIPPED_PART) {
            Yii::$app->session->setFlash('error', '已发货的订单不允许重复发货');
            return $this->gotoView($orderId);
        }

        $deliveryOrder = null;
        //未分单的订单，生成发货单
        if ($orderInfo->order_status == OrderInfo::ORDER_STATUS_CONFIRMED) {
            //创建一个已发货状态的发货单
            $deliveryOrder = DeliveryOrder::createShippedDeliveryOrderFromOrderInfo($orderInfo);
        }

        $deliveryOrder->shipping_fee = $shippingFee ?: 0.00;

        //没有发货单
        if (empty($deliveryOrder)) {
            Yii::$app->session->setFlash('error', '未找到发货单');
            return $this->gotoView($orderId);
        }

        $deliveryOrder->invoice_no = $orderInfo->invoice_no = $shippingInfo;
        $deliveryOrder->group_id = $orderInfo->group_id;
        //保存发货单
        if ($deliveryOrder->save()) {

            $orderInfo->note = $note;
            $orderInfo->order_status = OrderInfo::ORDER_STATUS_SPLITED;
            $orderInfo->shipping_status = OrderInfo::SHIPPING_STATUS_SHIPPED;
            $orderInfo->shipping_time = DateTimeHelper::getFormatGMTTimesTimestamp();
            //修改订单状态
            if ($orderInfo->save()) {

                $orderGroup = $orderInfo->orderGroup;
                if (!empty($orderGroup)) {
                    $orderGroup->setupOrderStatus();
                    $orderGroup->syncTimeInfo();
                    $orderGroup->save();
                }

                Yii::$app->session->setFlash('success', '成功发货，发货单流水号：'. $deliveryOrder->delivery_sn);

                if (empty($deliveryOrder->deliveryGoods)) {
                    foreach ($orderInfo->ordergoods as $goods) {
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
                        $deliveryGoods->send_number = $goods->goods_number;
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
                        }
                    }

                }

                return $this->gotoView($orderId);
            }
            else {
                Yii::$app->session->setFlash('error', '操作失败，'. OrderInfo::className(). ', '. VarDumper::export($orderInfo->errors));
            }
        }
        else {
            Yii::$app->session->setFlash('error', '操作失败：'. VarDumper::export($deliveryOrder->errors));
        }

        return $this->gotoView($orderId);
    }

    /**
     * 把订单状态改变为已发货状态，只有部分发货的订单才允许这个操作
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionShipped() {
        $note = Yii::$app->request->post('note');
        $orderId = Yii::$app->request->post('orderId');

        if (empty($note)) {
            $this->flashErrorMessage('请填写备注');
        }

        $orderInfo = $this->findModel($orderId);

        if ($orderInfo->order_status != OrderInfo::ORDER_STATUS_SPLITED
            || $orderInfo->pay_status != OrderInfo::PAY_STATUS_PAYED
            || $orderInfo->shipping_status != OrderInfo::SHIPPING_STATUS_SHIPPED_PART
        ) {
            $this->flashErrorMessage('只有部分发货的订单才能直接转为已发货状态');
        }

        $orderInfo->note = $note;
        $orderInfo->shipping_status = OrderInfo::SHIPPING_STATUS_SHIPPED;
        $orderInfo->shipping_time = DateTimeHelper::getFormatGMTTimesTimestamp();

        if (!$orderInfo->save()) {

            $this->flashError($orderInfo);
        }

        $orderGroup = $orderInfo->orderGroup;
        if (!empty($orderGroup)) {
            $orderGroup->setupOrderStatus();
            $orderGroup->syncTimeInfo();
            $orderGroup->save();
        }

        return $this->gotoView($orderId);
    }

    public function actionDone() {
        $note = Yii::$app->request->post('note');
        $orderId = Yii::$app->request->post('orderId');

        if (empty($note)) {
            throw new BadRequestHttpException('请返回填写操作日志');
        }

        if (empty($orderId)) {
            throw new BadRequestHttpException('请返回填写订单号');
        }

        $orderInfo = OrderInfo::findOne([
            'order_id' => $orderId,
        ]);
        if (empty($orderInfo)) {
            throw new BadRequestHttpException('没有找到订单');
        }

        //判断订单状态是不是已发货
        if ($orderInfo->order_status != OrderInfo::ORDER_STATUS_SPLITED
            || $orderInfo->pay_status != OrderInfo::PAY_STATUS_PAYED
            || $orderInfo->shipping_status != OrderInfo::SHIPPING_STATUS_SHIPPED) {
            Yii::$app->session->setFlash('error', '只有已发货状态才能修改为已完成状态, os = '. $orderInfo->order_status. ', ps = '. $orderInfo->pay_status. ', ss = '. $orderInfo->shipping_status);
            return $this->gotoView($orderId);
        }

        $orderInfo->note = $note;
        $orderInfo->order_status = OrderInfo::ORDER_STATUS_REALLY_DONE;
        $orderInfo->shipping_status = OrderInfo::SHIPPING_STATUS_RECEIVED;
        if ($orderInfo->save()) {

            $orderGroup = $orderInfo->orderGroup;
            if (!empty($orderGroup)) {
                $orderGroup->setupOrderStatus();
                $orderGroup->save();
            }

            $this->flashSuccess('操作成功');
        }
        else {
            $this->flashError($orderInfo);
        }
        return $this->gotoView($orderId);
    }

    /**
     * 申请退款
     * @throws BadRequestHttpException
     */
    public function actionAskForRefund() {
        $note = Yii::$app->request->post('note');
        $orderId = Yii::$app->request->post('orderId');
        $orderGoodsList = Yii::$app->request->post('orderGoods');

        if (empty($note)) {
            throw new BadRequestHttpException('请返回填写操作日志');
        }

        if (empty($orderId)) {
            throw new BadRequestHttpException('请返回填写订单号');
        }

        $orderInfo = OrderInfo::find()->where([
            OrderInfo::tableName().'.order_id' => $orderId,
        ])->joinWith('ordergoods ordergoods')->one();
        if (empty($orderInfo)) {
            throw new BadRequestHttpException('没有找到订单');
        }

        $backOrder = BackOrder::createByOrderInfo($orderInfo);
        if ($backOrder->save()) {
            //遍历需要退款的商品

            foreach ($orderInfo->ordergoods as $ordergood) {
                $backGoods = new BackGoods();
                $backGoods->goods_id = $ordergood->goods_id;
                $backGoods->product_id = $ordergood->goods_id;
                $backGoods->product_sn = '';
                $backGoods->goods_name = $ordergood->goods_name;
                $backGoods->brand_name = isset($ordergood->goods->brand) ? $ordergood->goods->brand->brand_name : '';
                $backGoods->goods_sn = $ordergood->goods_sn;
                $backGoods->send_number = $ordergood->goods_number;
                $backGoods->goods_attr = $ordergood->goods_attr;

                $backOrder->link('backGoods', $backGoods);
            }
        }

        //判断订单状态是不是已付款状态
        if ($orderInfo->order_status != OrderInfo::ORDER_STATUS_CONFIRMED
            || $orderInfo->pay_status != OrderInfo::PAY_STATUS_PAYED) {
            Yii::$app->session->setFlash('error', '只有已付款状态才能修改为申请退款状态, os = '. $orderInfo->order_status. ', ps = '. $orderInfo->pay_status. ', ss = '. $orderInfo->shipping_status);
            return $this->gotoView($orderId);
        }

        $orderInfo->note = $note;
        $orderInfo->order_status = OrderInfo::ORDER_STATUS_ASK_4_REFUND;
        if ($orderInfo->save()) {
            $orderGroup = $orderInfo->orderGroup;
            if (!empty($orderGroup)) {
                $orderGroup->setupOrderStatus();
                $orderGroup->save();
            }
            $this->flashSuccess('操作成功');
        }
        else {
            $this->flashError($orderInfo);
        }
        return $this->gotoView($orderId);
    }

    public function actionDisagreeRefund() {
        $note = Yii::$app->request->post('note');
        $orderId = Yii::$app->request->post('orderId');

        if (empty($note)) {
            throw new BadRequestHttpException('请返回填写操作日志');
        }

        if (empty($orderId)) {
            throw new BadRequestHttpException('请返回填写订单号');
        }

        $orderInfo = OrderInfo::findOne([
            'order_id' => $orderId,
        ]);
        if (empty($orderInfo)) {
            throw new BadRequestHttpException('没有找到订单');
        }

        //判断订单状态是不是已付款状态
        if ($orderInfo->order_status != OrderInfo::ORDER_STATUS_ASK_4_REFUND
            || $orderInfo->pay_status != OrderInfo::PAY_STATUS_PAYED) {
            Yii::$app->session->setFlash('error', '只有申请退款状态才能驳回退款, os = '. $orderInfo->order_status. ', ps = '. $orderInfo->pay_status. ', ss = '. $orderInfo->shipping_status);
            return $this->gotoView($orderId);
        }

        $orderInfo->note = $note;
        $orderInfo->order_status = OrderInfo::ORDER_STATUS_CONFIRMED;
        if ($orderInfo->save()) {
            $orderGroup = $orderInfo->orderGroup;
            if (!empty($orderGroup)) {
                $orderGroup->setupOrderStatus();
                $orderGroup->save();
            }
            $this->flashSuccess('操作成功');
        }
        else {
            $this->flashError($orderInfo);
        }
        return $this->gotoView($orderId);
    }

    /**
     * 退款完成
     * @throws BadRequestHttpException
     */
    public function actionRefundDone() {
        $note = Yii::$app->request->post('note');
        $orderId = Yii::$app->request->post('orderId');

        if (empty($note)) {
            throw new BadRequestHttpException('请返回填写操作日志');
        }

        if (empty($orderId)) {
            throw new BadRequestHttpException('请返回填写订单号');
        }

        $orderInfo = OrderInfo::findOne([
            'order_id' => $orderId,
        ]);
        if (empty($orderInfo)) {
            throw new BadRequestHttpException('没有找到订单');
        }

        //判断订单状态是不是申请退款
        if ($orderInfo->order_status != OrderInfo::ORDER_STATUS_ASK_4_REFUND
            || $orderInfo->pay_status != OrderInfo::PAY_STATUS_PAYED) {
            Yii::$app->session->setFlash('error', '只有已付款状态才能修改为申请退款状态, os = '. $orderInfo->order_status. ', ps = '. $orderInfo->pay_status. ', ss = '. $orderInfo->shipping_status);
            return $this->gotoView($orderId);
        }

        $orderInfo->note = $note;
        $orderInfo->order_status = OrderInfo::ORDER_STATUS_REFUNDED_DONE;
        $orderInfo->pay_status = OrderInfo::PAY_STATUS_REFUND;
        if ($orderInfo->save()) {
            $orderGroup = $orderInfo->orderGroup;
            if (!empty($orderGroup)) {
                $orderGroup->setupOrderStatus();
                $orderGroup->save();
            }
            $this->flashSuccess('操作成功');
        }
        else {
            $this->flashError($orderInfo);
        }
        return $this->gotoView($orderId);
    }

    /**
     * 申请退货
     * @throws BadRequestHttpException
     */
    public function actionAskForReturn() {
        $note = Yii::$app->request->post('note');
        $orderId = Yii::$app->request->post('orderId');

        if (empty($note)) {
            throw new BadRequestHttpException('请返回填写操作日志');
        }

        if (empty($orderId)) {
            throw new BadRequestHttpException('请返回填写订单号');
        }

        $orderInfo = OrderInfo::findOne([
            'order_id' => $orderId,
        ]);
        if (empty($orderInfo)) {
            throw new BadRequestHttpException('没有找到订单');
        }

        //判断订单状态是不是已发货状态
        if (($orderInfo->order_status != OrderInfo::ORDER_STATUS_SPLITED && $orderInfo->order_status != OrderInfo::ORDER_STATUS_REALLY_DONE)
            || $orderInfo->pay_status != OrderInfo::PAY_STATUS_PAYED
            || ($orderInfo->shipping_status != OrderInfo::SHIPPING_STATUS_SHIPPED && $orderInfo->shipping_status != OrderInfo::SHIPPING_STATUS_RECEIVED)) {
            Yii::$app->session->setFlash('error', '只有已发货和已完成状态才能修改为申请退货状态, os = '. $orderInfo->order_status. ', ps = '. $orderInfo->pay_status. ', ss = '. $orderInfo->shipping_status);
            return $this->gotoView($orderId);
        }

        $orderInfo->note = $note;
        $orderInfo->order_status = OrderInfo::ORDER_STATUS_ASK_4_RETURN;
        $orderInfo->shipping_status = OrderInfo::SHIPPING_STATUS_SHIPPED;
        if ($orderInfo->save()) {
            $orderGroup = $orderInfo->orderGroup;
            if (!empty($orderGroup)) {
                $orderGroup->setupOrderStatus();
                $orderGroup->save();
            }
            $this->flashSuccess('操作成功, os = '. $orderInfo->order_status. ', ps = '. $orderInfo->pay_status. ', ss = '. $orderInfo->shipping_status);
        }
        else {
            $this->flashError($orderInfo);
        }
        return $this->gotoView($orderId);
    }

    /**
     * 不同意退货（返回到已分单状态）
     * @return \yii\web\Response
     * @throws BadRequestHttpException
     */
    public function actionDisagreeReturn() {
        $note = Yii::$app->request->post('note');
        $orderId = Yii::$app->request->post('orderId');

        if (empty($note)) {
            throw new BadRequestHttpException('请返回填写操作日志');
        }

        if (empty($orderId)) {
            throw new BadRequestHttpException('请返回填写订单号');
        }

        $orderInfo = OrderInfo::findOne([
            'order_id' => $orderId,
        ]);
        if (empty($orderInfo)) {
            throw new BadRequestHttpException('没有找到订单');
        }

        //判断订单状态是不是申请退货
        if (($orderInfo->order_status != OrderInfo::ORDER_STATUS_ASK_4_RETURN
            && $orderInfo->order_status != OrderInfo::ORDER_STATUS_AGREE_RETURN)
            || $orderInfo->pay_status != OrderInfo::PAY_STATUS_PAYED
            || !in_array($orderInfo->shipping_status, [OrderInfo::SHIPPING_STATUS_SHIPPED, OrderInfo::SHIPPING_STATUS_RECEIVED])) {
            Yii::$app->session->setFlash('error', '只有申请退货或者同意退货状态才能驳回申请状态, os = '. $orderInfo->order_status. ', ps = '. $orderInfo->pay_status. ', ss = '. $orderInfo->shipping_status);
            return $this->gotoView($orderId);
        }

        $orderInfo->note = $note;
        $orderInfo->order_status = OrderInfo::ORDER_STATUS_SPLITED;
        if ($orderInfo->save()) {
            $orderGroup = $orderInfo->orderGroup;
            if (!empty($orderGroup)) {
                $orderGroup->setupOrderStatus();
                $orderGroup->save();
            }
            $this->flashSuccess('操作成功, os = '. $orderInfo->order_status. ', ps = '. $orderInfo->pay_status. ', ss = '. $orderInfo->shipping_status);
        }
        else {
            $this->flashError($orderInfo);
        }
        return $this->gotoView($orderId);
    }

    /**
     * 同意退货
     * @return \yii\web\Response
     * @throws BadRequestHttpException
     */
    public function actionAgreeReturn() {
        $note = Yii::$app->request->post('note');
        $orderId = Yii::$app->request->post('orderId');

        if (empty($note)) {
            throw new BadRequestHttpException('请返回填写操作日志');
        }

        if (empty($orderId)) {
            throw new BadRequestHttpException('请返回填写订单号');
        }

        $orderInfo = OrderInfo::findOne([
            'order_id' => $orderId,
        ]);
        if (empty($orderInfo)) {
            throw new BadRequestHttpException('没有找到订单');
        }

        //判断订单状态是不是申请退货
        if ($orderInfo->order_status != OrderInfo::ORDER_STATUS_ASK_4_RETURN
            || $orderInfo->pay_status != OrderInfo::PAY_STATUS_PAYED
            || $orderInfo->shipping_status != OrderInfo::SHIPPING_STATUS_SHIPPED) {
            Yii::$app->session->setFlash('error', '只有申请退货状态才能修改为同意退货状态, os = '. $orderInfo->order_status. ', ps = '. $orderInfo->pay_status. ', ss = '. $orderInfo->shipping_status);
            return $this->gotoView($orderId);
        }

        $orderInfo->note = $note;
        $orderInfo->order_status = OrderInfo::ORDER_STATUS_AGREE_RETURN;
        if ($orderInfo->save()) {
            $orderGroup = $orderInfo->orderGroup;
            if (!empty($orderGroup)) {
                $orderGroup->setupOrderStatus();
                $orderGroup->save();
            }
            $this->flashSuccess('操作成功, os = '. $orderInfo->order_status. ', ps = '. $orderInfo->pay_status. ', ss = '. $orderInfo->shipping_status);
        }
        else {
            $this->flashError($orderInfo);
        }
        return $this->gotoView($orderId);
    }

    /**
     * 退货
     * @throws BadRequestHttpException
     */
    public function actionReturn() {
        $note = Yii::$app->request->post('note');
        $orderId = Yii::$app->request->post('orderId');

        if (empty($note)) {
            throw new BadRequestHttpException('请返回填写操作日志');
        }

        if (empty($orderId)) {
            throw new BadRequestHttpException('请返回填写订单号');
        }

        $orderInfo = OrderInfo::findOne([
            'order_id' => $orderId,
        ]);
        if (empty($orderInfo)) {
            throw new BadRequestHttpException('没有找到订单');
        }

        //判断订单状态是不是同意退货
        if ($orderInfo->order_status != OrderInfo::ORDER_STATUS_AGREE_RETURN
            || $orderInfo->pay_status != OrderInfo::PAY_STATUS_PAYED
            || $orderInfo->shipping_status != OrderInfo::SHIPPING_STATUS_SHIPPED) {
            Yii::$app->session->setFlash('error', '只有申请退货状态才能修改为退货状态, os = '. $orderInfo->order_status. ', ps = '. $orderInfo->pay_status. ', ss = '. $orderInfo->shipping_status);
            return $this->gotoView($orderId);
        }

        $orderInfo->note = $note;
        $orderInfo->order_status = OrderInfo::ORDER_STATUS_RETURNED;
        if ($orderInfo->save()) {
            $orderGroup = $orderInfo->orderGroup;
            if (!empty($orderGroup)) {
                $orderGroup->setupOrderStatus();
                $orderGroup->save();
            }
            $this->flashSuccess('操作成功, os = '. $orderInfo->order_status. ', ps = '. $orderInfo->pay_status. ', ss = '. $orderInfo->shipping_status);
        }
        else {
            $this->flashError($orderInfo);
        }
        return $this->gotoView($orderId);
    }

    /**
     * 退货完成
     * @throws BadRequestHttpException
     */
    public function actionReturnDone() {
        $note = Yii::$app->request->post('note');
        $orderId = Yii::$app->request->post('orderId');

        if (empty($note)) {
            throw new BadRequestHttpException('请返回填写操作日志');
        }

        if (empty($orderId)) {
            throw new BadRequestHttpException('请返回填写订单号');
        }

        $orderInfo = OrderInfo::findOne([
            'order_id' => $orderId,
        ]);
        if (empty($orderInfo)) {
            throw new BadRequestHttpException('没有找到订单');
        }

        //判断订单状态是不是已退货
        if ($orderInfo->order_status != OrderInfo::ORDER_STATUS_RETURNED
            || $orderInfo->pay_status != OrderInfo::PAY_STATUS_PAYED
            || $orderInfo->shipping_status != OrderInfo::SHIPPING_STATUS_SHIPPED) {
            Yii::$app->session->setFlash('error', '只有申请退货状态才能修改为退货状态, os = '. $orderInfo->order_status. ', ps = '. $orderInfo->pay_status. ', ss = '. $orderInfo->shipping_status);
            return $this->gotoView($orderId);
        }

        $orderInfo->note = $note;
        $orderInfo->order_status = OrderInfo::ORDER_STATUS_RETURNED_DONE;
        $orderInfo->pay_status = OrderInfo::PAY_STATUS_REFUND;
        $orderInfo->shipping_status = OrderInfo::SHIPPING_STATUS_UNSHIPPED;

        if ($orderInfo->save()) {
            $orderGroup = $orderInfo->orderGroup;
            if (!empty($orderGroup)) {
                $orderGroup->setupOrderStatus();
                $orderGroup->save();
            }
            $this->flashSuccess('操作成功, os = '. $orderInfo->order_status. ', ps = '. $orderInfo->pay_status. ', ss = '. $orderInfo->shipping_status);
        }
        else {
            $this->flashError($orderInfo);
        }
        return $this->gotoView($orderId);
    }

    public function actionPrint($id) {
        if (empty($id)) {
            throw new BadRequestHttpException('缺少订单ID');
        }
        $orderInfo = OrderInfo::findOne([
            'order_id' => $id,
        ]);

        return $this->renderPartial('print', [
            'model' => $orderInfo,
        ]);
    }

    /**
     * ajax拉取订单列表，给select2控件使用
     * @param null $q
     * @param null $id
     * @return array
     */
    public function actionOrderInfoList($q = null, $id = null) {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $query = new Query();
            $query->select('order_id AS id, order_sn AS text')
                ->from(OrderInfo::tableName())
                ->where(['like', 'order_sn', $q])
                ->limit(20);
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        }
        elseif ($id > 0) {
            $out['results'] = ['id' => $id, 'text' => OrderInfo::find($id)->order_sn];
        }
        return $out;
    }

    public function actionAdvanceShipping($id) {
        $orderInfo = $this->findModel($id);

        //订单所有的商品
        $orderGoods = OrderGoods::find()->where([
            'order_id' => $id,
        ])->indexBy('rec_id')->all();


        if (OrderGoods::loadMultiple($orderGoods, Yii::$app->request->post()) && OrderGoods::validateMultiple($orderGoods)) {

            $shippingInfo = Yii::$app->request->post('shippingInfo');
            if (empty($shippingInfo)) {
                $this->flashErrorMessage('请填写运单号');
                return $this->redirect(['advance-shipping', 'id' => $id]);
            }

            //总发货数量
            $totalCount = 0;
            foreach ($orderGoods as $k => $goods) {
                if ($goods->goods_number - $goods->send_number < $goods['shippingNum'] || $goods['shippingNum'] < 0) {
                    Yii::$app->session->setFlash('error', '订单发货失败 '. $goods['goods_name']. ' 发货数量错误，待发货数量为：'. ($goods->goods_number - $goods->send_number). ', 这次发货数量为'. $goods['shippingNum']);
                    return $this->redirect(['advance-shipping', 'id' => $id]);
                }
                $totalCount += $goods->shippingNum;
            }

            if ($totalCount <= 0) {
                $this->flashErrorMessage('发货数量为0，请检查发货数量是否填写有误');
                return $this->redirect(['advance-shipping', 'id' => $id]);
            }

            $deliveryOrder = DeliveryOrder::createShippedDeliveryOrderFromOrderInfo($orderInfo);
            $deliveryOrder->invoice_no = $shippingInfo;
            $deliveryOrder->group_id = $orderInfo->group_id;

            $transaction = DeliveryOrder::getDb()->beginTransaction();
            try {
                $orderInfo->link('deliveryOrder', $deliveryOrder);
                foreach($orderGoods as $goods) {
                    if ($goods['shippingNum'] > 0) {
                        $deliveryGoods = new DeliveryGoods();
                        $deliveryGoods->goods_id = $goods->goods_id;
                        $deliveryGoods->goods_name = $goods->goods_name;
                        $deliveryGoods->send_number = $goods->shippingNum;
                        $deliveryGoods->brand_name = $goods->goods->brand->brand_name ?: '';
                        $deliveryGoods->goods_sn = $goods->goods_sn;
                        $deliveryGoods->is_real = 1;
                        $deliveryGoods->extension_code = $goods->extension_code;
                        $deliveryGoods->parent_id = 0;
                        $deliveryGoods->goods_price = $goods->goods_price;
                        $deliveryGoods->order_goods_rec_id = $goods->rec_id;

                        $deliveryOrder->link('deliveryGoods', $deliveryGoods);
                        $deliveryGoods->link('orderGoods', $goods);

                        $goods->send_number += $goods->shippingNum;
                        if ($goods->send_number > $goods->goods_number) {
                            Yii::error('发货数量超过了商品数量 rec_id = '. $goods->rec_id, __METHOD__);
                            $goods->send_number = $goods->goods_number;
                        }
                        $goods->save(false);
                    }
                }

                $transaction->commit();

                $orderInfo->order_status = OrderInfo::ORDER_STATUS_SPLITED;

                //如果全部商品都发货了，就把shipping_status改为已发货，否则改为部分发货
                if ($orderInfo->isAllGoodsShipped()) {
                    $orderInfo->shipping_status = OrderInfo::SHIPPING_STATUS_SHIPPED;
                    $orderInfo->shipping_time = DateTimeHelper::getFormatGMTTimesTimestamp();
                }
                else {
                    $orderInfo->shipping_status = OrderInfo::SHIPPING_STATUS_SHIPPED_PART;
                }

                if (!$orderInfo->save()) {
                    Yii::error('订单修改状态失败 orderId = '. $orderInfo->order_id. ', orderSn = '. $orderInfo->order_sn, __METHOD__);
                    $this->flashError($orderInfo);
                }

                $orderGroup = $orderInfo->orderGroup;
                if (!empty($orderGroup)) {
                    $orderGroup->setupOrderStatus();
                    $orderGroup->syncTimeInfo();
                    $orderGroup->save();
                }

                $this->flashSuccess('本次发货成功');
                return $this->redirect(['advance-shipping', 'id' => $id]);
            } catch(\Exception $e) {
                $transaction->rollBack();
                throw $e;
            } catch(\Throwable $e) {
                $transaction->rollBack();
                throw $e;
            }
        }

        return $this->render('advance-shipping', [
            'model' => $orderInfo,
            'orderGoods' => $orderGoods,
        ]);
    }

    /**
     * Finds the OrderInfo model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return OrderInfo the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OrderInfo::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionExportForErp() {
        ini_set('max_execution_time', 120);
        ini_set('memory_limit', '1G');

        //  定义表头，方便在调整各列显示顺序的时候 只调整表头数组就，不用调整各列具体值的数组顺序
        $dataHeader = [
            //  order_info
            'group_id' => '[表头]单据编号(*)',
            'create_time' => '[表头]日期(*)',
            'user_name' => '[表头]购货单位(*)',
            'currency' => '[表头]币别(*)',
            'exchange_rate' => '[表头]汇率(*)',
            'checkout_date' => '[表头]结算日期(*)',
            'sale_type' => '[表头]销售方式',
            'delivery_type' => '[表头]交货方式',
            'delivery_address' => '[表头]交货地点',
            'checkout_type' => '[表头]结算方式',
            'description' => '[表头]摘要',

            'goods_sn' => '物料代码(*)',
            'goods_name' => '物料名称',
            'goods_attr' => '规格型号',
            'measure_unit' => '单位(*)',
            'goods_price' => '单价(*)',
            'goods_number' => '数量(*)',
            'tax_rate' => '税率(%)(*)',
            'discount_rate' => '折扣率(%)(*)',
            'delivery_date' => '交货日期(*)',
            'helper_attr' => '辅助属性',
            'note' => '备注',
        ];

        $searchModel = new OrderInfoSearch();
        $dataProvider = $searchModel->searchForExport(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;
        $models = $dataProvider->getModels();

        $dataList[] = array_values($dataHeader);

        if (!empty($models)) {
            foreach ($models as $model) {
                $userName = empty($model['users']) ? '未知用户': $model['users']->getUserShowName();
                if ($model->shipping_id == 2 ||
                    $model->shipping_id == 4 ||
                    $model->shipping_id == 6) {
                    $deliveryType = '包邮';
                } elseif ($model->shipping_id == 5) {
                    if (strstr($model->shipping_name, '到付')) {
                        $deliveryType = '到付';
                    } else {
                        $deliveryType = '包邮';
                    }
                } elseif ($model->shipping_id == 3) {
                    $deliveryType = '到付';
                } else {
                    throw new ServerErrorHttpException('总单号：'. $model['group_id']. ', 子单号：'. $model['order_sn']. ' 对应的shipping_id错误，shipping_id = '. $model->shipping_id. ', shipping_name = '. $model->shipping_name);
                }

                $payTime = $model['pay_time'];
                $date = DateTimeHelper::getFormatCNDate($payTime);

                $item = [
                    'group_id' => ''.$model['order_sn'],
                    'create_time' => $date,
                    'user_name' => $userName,
                    'currency' => '人民币',
                    'exchange_rate' => '1',
                    'checkout_date' => $date,
                    'sale_type' => '赊销',
                    'delivery_type' => $deliveryType,
                    'delivery_address' => $model['group_id']. '_'. $deliveryType,       //暂时用来存总单号
                    'checkout_type' => '*',
                    'description' => ''.$model['consignee']. ' '. $model['mobile']. ' '. Region::getAddress($model, $model['address']),
                ];


                $deliveryDate = DateTimeHelper::getFormatCNDate($payTime + 2 * 24 * 60 * 60);    //支付之后的2天内发货

                $goodsList = $model->ordergoods;
                foreach ($goodsList as $k => $goods) {
                    if ($k > 0) {
                        $item = [
                            'group_id' => '',
                            'create_time' => '',
                            'user_name' => '',
                            'currency' => '',
                            'exchange_rate' => '',
                            'checkout_date' => '',
                            'sale_type' => '',
                            'delivery_type' => '',
                            'delivery_address' => '',       //暂时用来存总单号
                            'checkout_type' => '',
                            'description' => '',
                        ];
                    }

                    if ($goods['goods']['prefix'] != 'NO') {
                        $item['goods_sn'] = $goods['goods']['prefix']. $goods['goods_sn'];
                    } else {
                        $item['goods_sn'] = ''. $goods['goods_sn'];
                    }

                    $item['goods_name'] = $goods['goods']['goods_name'];
                    $item['goods_attr'] = '';
                    $item['measure_unit'] = $goods['goods']['measure_unit'];
                    if ($model->extension_code == OrderInfo::EXTENSION_CODE_INTEGRAL) {
                        $item['goods_price'] = 0;
                    } else {
                        $item['goods_price'] = $goods['pay_price'];
                    }
                    $item['goods_number'] = $goods['goods_number'];
                    $item['tax_rate'] = 0;
                    $item['discount_rate'] = 0;
                    $item['delivery_date'] = $deliveryDate;
                    $item['helper_attr'] = '';
                    $item['note'] = '';

                    $dataList[] = $item;
                }

                if ($model->shipping_fee > 0) {
                    $item = [
                        'group_id' => '',
                        'create_time' => '',
                        'user_name' => '',
                        'currency' => '',
                        'exchange_rate' => '',
                        'checkout_date' => '',
                        'sale_type' => '',
                        'delivery_type' => '',
                        'delivery_address' => '',       //暂时用来存总单号
                        'checkout_type' => '',
                        'description' => '',
                    ];

                    $item['goods_sn'] = '666666';
                    $item['goods_name'] = '运费';
                    $item['goods_attr'] = '';
                    $item['measure_unit'] = '件';
                    $item['goods_price'] = '1.00';
                    $item['goods_number'] = intval($model->shipping_fee);
                    $item['tax_rate'] = 0;
                    $item['discount_rate'] = 0;
                    $item['delivery_date'] = $deliveryDate;
                    $item['helper_attr'] = '';
                    $item['note'] = '';

                    $dataList[] = $item;
                }
            }

            $file_name = '对接ERP订单列表'.date('YmdHis');
            OfficeHelper::excelExport($file_name, $dataList, '', '销售订单', false);
        }
        else {
            throw new ServerErrorHttpException('选择的条件没有订单');
        }
    }

    public function actionExportDivide() {
        ini_set('max_execution_time', 120);
        ini_set('memory_limit', '1G');

        //  定义表头，方便在调整各列显示顺序的时候 只调整表头数组就，不用调整各列具体值的数组顺序
        $data_header = [
            //  order_info
            'add_time' => '下单时间',
            'group_id' => '总单号',
            'order_sn' => '订单号',
            'managerName' => '客户归属人',
            'user_name' => '用户名',
            'consignee' => '收货人',
            'mobile' => '手机',
            'complete_address' => '地址',

            'brand_user_company_name' => '供应商名称',
            'brand_name' => '品牌名称',
            'brand_id' => '品牌ID',
            //  order_goods
            'goods_name' => '商品名称',
            'goods_sn' => '货号',
            'goods_number' => '数量',
            'goods_price' => '单价',
            'supply_price' => '成本价',
            'send_number' => '已发货数量',
            'market_price' => '市场价',

            'sku_amount' => 'SKU金额',
            'order_status' => '订单状态',
            'pay_status' => '付款状态',
            'postscript' => '客户备注',
            'shipping_name' => '快递名称',
            'shipping_fee' => '运费',
            'invoice_no' => '快递单号',
            'province' => '收货人所在省',
            'user_province' => '用户注册时填写的省',
        ];
        $data_array[] = array_values($data_header);

        $searchModel = new OrderInfoSearch();
        $queryParams = Yii::$app->request->queryParams;
        $queryParams['page_size'] = 0;
        $dataProvider = $searchModel->searchForExport($queryParams, 'export');
        $model_list = $dataProvider->getModels();

        if ($model_list) {
            foreach ($model_list as $model) {

                $province = 0;
                $city = 0;

                if (!empty($model->users)) {
                    $province = $model->users->province;
                    $city = $model->users->city;
                }

                $managerName = '';

                if ($province > 0 && $city > 0) {
                    //看谁命中这个区域
                    $managerUser = \common\models\Users::find()->joinWith([
                        'userRegion userRegion',
                    ])->where([
                        'userRegion.region_id' => [
                            $province,
                            $city,
                        ],
                    ])->andWhere([
                        'servicer_info_id' => 0,
                    ])->one();

                    if (!empty($managerUser)) {
                        $managerName = $managerUser->showName. '('. $managerUser->mobile_phone. ')';
                    }
                    else {
                        $managerName = '吴喜芝(13049889166)';
                    }
                }


                $order_goods = OrderGoods::find()
                    ->joinWith('goods')
                    ->where([
                        'order_id' => $model->order_id
                    ])->all();

                foreach ($order_goods as $goods) {
                    if (!empty($goods['goods']) && !empty($goods['goods']['brand_id'])) {
                        $brand_id = $goods['goods']['brand_id'];
                    } else {
                        $brand_id = 0;
                    }
                    $item = [
                        'add_time' => DateTimeHelper::getFormatCNDateTime($model->add_time),
                        'group_id' => $model->group_id,
                        'order_sn' => $model->order_sn,
                        'managerName' => $managerName,
                        'user_name' => empty($model->users) ? '' : $model->users->showName,
                        'consignee' => $model->consignee,
                        'mobile' => $model->mobile,
                        'complete_address' => Region::getUserAddress($model).' '.$model->address,

                        'brand_user_company_name' => ' ',
                        'brand_name' => '',
                        'brand_id' => $brand_id,
                        //  order_goods
                        'goods_name' => $goods->goods_name,
                        'goods_sn' => $goods->goods_sn ?: '',
                        'goods_number' => $goods->goods_number,
                        'goods_price' => $goods->goods_price,
                        'send_number' => $goods->send_number,
                        'supply_price' => isset($goods['goods']) ? $goods['goods']->getSupplyPrice() : 0,
                        'market_price' => $goods->market_price,

                        'sku_amount' => bcmul($goods->goods_price, $goods->goods_number, 2),
                        'order_status' => OrderInfo::$order_status_map[$model->order_status],
                        'pay_status' => OrderInfo::$pay_status_map[$model->pay_status],
                        'postscript' => $model->postscript ?: '',
                        'shipping_name' => $model->shipping_name ?: '',
                        'shipping_fee' => $model->shipping_fee ?: 0.00,
                        'invoice_no' => $model->invoice_no ?: '',
                        'province' => isset($model->provinceRegion) ? $model->provinceRegion->region_name: '',
                        'user_province' => isset($model->users->provinceRegion) ? $model->users->provinceRegion->region_name: '',
                    ];

                    $data_array[] = $item;
//                    $data_array[] = array_values($item);
                }

            }
        }

        $brandIdList = array_unique(array_column($data_array, 'brand_id'));
        $brandList = Brand::find()
            ->select(['brand_id', 'brand_name'])
            ->where(['brand_id' => $brandIdList])
            ->asArray()
            ->all();
        $brandMap = array_column($brandList, 'brand_name', 'brand_id');

        foreach ($data_array as &$data) {
            if (!empty($data['brand_id']) && isset($brandMap[$data['brand_id']])) {
                $data['brand_name'] = $brandMap[$data['brand_id']];
            }
            $data = array_values($data);
        }

        $file_name = '订单列表'.date('YmdHis');

        OfficeHelper::excelExport($file_name, $data_array);
    }
}
