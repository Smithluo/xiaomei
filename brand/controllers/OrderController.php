<?php

namespace brand\controllers;

use common\helper\DateTimeHelper;
use common\models\BrandUser;
use common\models\ServicerDivideRecord;
use Yii;
use brand\models\OrderInfo;
use common\models\OrderInfoSearch;
use common\models\OrderGoods;
use common\helper\SessionHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use common\models\Users;
use common\models\Goods;
use common\models\DeliveryOrder;
use common\models\ShopConfig;
use brand\models\BrandDivideRecord;
use common\models\Region;
use common\helper\NumberHelper;

/**
 * OrderController implements the CRUD actions for OrderInfo model.
 */
class OrderController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'to-be-shipped', 'to-be-returned', 'confirm-return'],
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
                ],
            ],
        ];
    }

    /**
     * Lists all OrderInfo models.
     * @return mixed
     */
    public function actionIndex($order_cs_status_value = '')
    {
        $order_cs_status_value = Yii::$app->request->get('order_cs_status');
        $searchModel = new OrderInfoSearch();
        $session = Yii::$app->session;
        SessionHelper::getUserBrandList();

        $queryParams = Yii::$app->request->queryParams;
        //  按·品牌商拆单之后，检索订单需要挑选order_info.supplier_user_id对应的所有订单
        $queryParams['supplier_user_id'] = Yii::$app->user->identity->getId();
        if (isset($queryParams['brand_id']) && $queryParams['brand_id']) {
            $queryParams['brand_id'] = array_intersect($queryParams['brand_id'], BrandUser::getSupplierBrandIdList(Yii::$app->user->identity['user_id']));
        } else {
            $queryParams['brand_id'] = BrandUser::getSupplierBrandIdList(Yii::$app->user->identity['user_id']);
        }

        //  如果有指定的订单综合状态，则只显示指定状态。如果没指定，则显示所有供应商应该看到的状态
        if (isset($order_cs_status_value) && in_array($order_cs_status_value, array_keys(OrderInfo::$order_cs_status))) {
            $queryParams['order_cs_status'] = OrderInfo::$order_cs_status[$order_cs_status_value];
        } else {
            $queryParams['order_status'] = ['order_status' => OrderInfo::$order_status_show];
        }

        //  考虑没有点击查询的条件
        if (empty($queryParams['end_date'])) {
            $queryParams['end_date'] = DateTimeHelper::getFormatDate(time());
        }
        if (empty($queryParams['start_date'])) {
            $start_data = strtotime('-1 month', strtotime($queryParams['end_date']));
            $queryParams['start_date'] = DateTimeHelper::getFormatDate($start_data);
        }
        $brand_user = BrandUser::find()->where(['user_id' => Yii::$app->user->identity->getId()])->one();
        if ($brand_user) {
            $queryParams['reg_time'] = $brand_user->reg_time;
        }

        $dataProvider = $searchModel->searchForBrand($queryParams);

        $model_list = $dataProvider->getModels();

        $order_goods = [];
        foreach ($model_list as &$model) {
            $goods_num = 0;
            foreach ($model->ordergoods as $item) {
                $goods_num += $item->goods_number;
            }

            $order_goods[$model->order_id]['address'] = Region::getUserAddress($model).' '.$model->address;
            $users_model = Users::find()->select('company_name')->where(['user_id' => $model->user_id])->one();
            if ($users_model) {
                $order_goods[$model->order_id]['company_name'] = $users_model->company_name;
            } else {
                $order_goods[$model->order_id]['company_name'] = '';    //  有可能是测试数据错乱
            }

            //  拼接xmdata数据
            $delivery = DeliveryOrder::find()
                ->select('invoice_no, shipping_name')
                ->where(['order_id' => $model->order_id])
                ->one();
            if ($delivery) {
                if ($delivery->shipping_name == '每个品牌三件以上满百包邮') {
                    $delivery->shipping_name = '';
                }
                $order_goods[$model->order_id]['xmdata'] = 'id='.OrderInfo::getDeliverySn($model->order_id).'&shipping_name='.$delivery->shipping_name.'&invoice_no='.$delivery->invoice_no;
            }

            $order_amount = bcadd($model->goods_amount, $model->shipping_fee, 4);
            $order_pay_fee = bcmul($order_amount, ShopConfig::getConfigValue('order_pay_fee'), 4);
            $need_pay = BrandDivideRecord::getDivideAmountByOrderId($model->order_id);
            $order_goods[$model->order_id]['order_pay_fee'] = $order_pay_fee;
            $order_goods[$model->order_id]['need_pay'] = $need_pay;
            $server_need_pay = ServicerDivideRecord::findOne(['order_id' => $model->order_id]);
            if ($server_need_pay) {
                $order_goods[$model->order_id]['server_need_pay'] = bcadd($server_need_pay->divide_amount, $server_need_pay->parent_divide_amount, 4);
            } else {
                $order_goods[$model->order_id]['server_need_pay'] = 0;
            }
            $order_goods[$model->order_id]['cs_order_status'] = OrderInfo::getOrderCsStatus([
                'order_status'      => $model->order_status,
                'shipping_status'   => $model->shipping_status,
                'pay_status'        => $model->pay_status,
            ]);

            $order_goods[$model->order_id]['order_goods_list'] = '';
            $order_goods_list = OrderGoods::find()->select('goods_id, goods_name, goods_number, goods_price')->where(['order_id' => $model->order_id])->all();

            foreach ($order_goods_list as $goods) {
                $goods_info = Goods::find()->where(['goods_id' => $goods->goods_id])->one();
                $order_goods[$model->order_id]['order_goods_list'] .= '<div class="good-detail">
            <span class="sp1">'.$goods->goods_name.'</span>
            <span class="text-navy sp2">'.$goods->goods_number.$goods_info->measure_unit.'</span>
            <span class="text-danger">'.NumberHelper::format_as_money($goods->goods_price).'</span>
        </div>';
            }
        }
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'model_list' => $model_list,
            'order_goods' => $order_goods,
            'order_cs_status' => $order_cs_status_value ?OrderInfo::$order_cs_status_map_no_style[$order_cs_status_value] : '',
            'params' => $queryParams,
            'r_version' => \Yii::$app->params['r_version'],
        ]);
    }

    /**
     * 待发货列表
     */
    public function actionToBeShipped()
    {
        $this->redirect(['index', 'order_cs_status' => OrderInfo::ORDER_CS_STATUS_TO_BE_SHIPPED]);
    }

    /**
     * 待退货列表
     */
    public function actionToBeReturned()
    {
        //  只给品牌商显示用户已经退货的订单，不显示用户申请退货还没有录入退货单号的订单：OrderInfo::ORDER_CS_STATUS_TO_BE_RETURNED,
        $this->redirect(['index', 'order_cs_status' => OrderInfo::ORDER_CS_STATUS_RETURNED]);
    }

    /**
     * Displays a single OrderInfo model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
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

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->order_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * 确认收到用户退货
     * @return bool
     * @throws NotFoundHttpException
     */
    public function actionConfirmReturn()
    {
        $model = $this->findModel(Yii::$app->request->post('id'));
        $model->order_status = OrderInfo::ORDER_STATUS_RETURNED_DONE;
        $model->order_status = OrderInfo::SHIPPING_STATUS_RECEIVED;
        $model->order_status = OrderInfo::PAY_STATUS_REFUND;

        if ($model->save()) {
            echo json_encode([
                'code' => 0,
                'msg' => '确认收到退货商品'
            ]);
            exit();
        } else {
            echo json_encode([
                'code' => 1,
                'msg' => '操作执行失败，请重试'
            ]);
            exit();
        }
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

}
