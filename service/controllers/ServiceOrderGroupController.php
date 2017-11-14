<?php

namespace service\controllers;

use common\helper\DateTimeHelper;
use common\helper\ImageHelper;
use common\helper\ShippingHelper;
use common\models\DeliveryOrder;
use Yii;
use common\models\OrderGroup;
use service\models\ServiceOrderGroupSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * ServiceOrderGroupController implements the CRUD actions for OrderGroup model.
 */
class ServiceOrderGroupController extends XmController
{
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
        $searchModel = new ServiceOrderGroupSearch();
        $searchModel->date_added = DateTimeHelper::getFormatDate(time() - 30 * 24 * 60 * 60);
        $searchModel->date_modified = DateTimeHelper::getFormatDate(time());
        $dataProvider = $searchModel->searchByOrderGroup(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'index' => 1 ,
        ]);
    }

    /**
     * Displays a single OrderGroup model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new OrderGroup model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
//    public function actionCreate()
//    {
//        $model = new OrderGroup();
//
//        if ($model->load(Yii::$app->request->post()) && $model->save()) {
//            return $this->redirect(['view', 'id' => $model->id]);
//        } else {
//            return $this->render('create', [
//                'model' => $model,
//            ]);
//        }
//    }

    /**
     * Updates an existing OrderGroup model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
//    public function actionUpdate($id)
//    {
//        $model = $this->findModel($id);
//
//        if ($model->load(Yii::$app->request->post()) && $model->save()) {
//            return $this->redirect(['view', 'id' => $model->id]);
//        } else {
//            return $this->render('update', [
//                'model' => $model,
//            ]);
//        }
//    }

    /**
     * Deletes an existing OrderGroup model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
//    public function actionDelete($id)
//    {
//        $this->findModel($id)->delete();
//
//        return $this->redirect(['index']);
//    }

    /**
     * Finds the OrderGroup model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return OrderGroup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OrderGroup::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
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
            return [
                'code' => 2,
                'msg' => '未找到订单',
            ];
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
}
