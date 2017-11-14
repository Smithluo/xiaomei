<?php

namespace brand\controllers;

use common\helper\DateTimeHelper;
use common\helper\SMSHelper;
use common\models\ShopConfig;
use common\models\OrderInfo;
use Yii;
use common\models\DeliveryOrder;
use common\models\DeliveryOrderSearch;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * DeliveryOrderController implements the CRUD actions for DeliveryOrder model.
 */
class DeliveryOrderController extends Controller
{
//    public $enableCsrfValidation = false;   //  局部关闭csrf验证
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
     * Lists all DeliveryOrder models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DeliveryOrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single DeliveryOrder model.
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
     * Creates a new DeliveryOrder model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new DeliveryOrder();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->delivery_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing DeliveryOrder model. 发货或修改单号
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate()
    {
        $delivery_order_id = (int)Yii::$app->request->post('id');
        $shipping_name = trim(Yii::$app->request->post('shipping_name'));
        $shipping_name = Html::encode($shipping_name);
        $invoice_no = Yii::$app->request->post('invoice_no');
        $invoice_no = Html::encode($invoice_no);
        $model = $this->findModel($delivery_order_id);

        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $sql1= 'UPDATE '.DeliveryOrder::tableName().' SET `invoice_no`="'.$invoice_no.'", `shipping_name`="'.$shipping_name.'" WHERE `delivery_id`='.$delivery_order_id;
            $connection->createCommand($sql1)->execute();

            $gmt_time = DateTimeHelper::getFormatGMTTimesTimestamp(time());
            $sql2 = 'UPDATE '.OrderInfo::tableName().' SET `shipping_status`='.OrderInfo::SHIPPING_STATUS_SHIPPED.', `shipping_name`="'.$shipping_name.'", `shipping_time`= '.$gmt_time.', `invoice_no`="'.$invoice_no.'" WHERE `order_id`='.$model->order_id;
            $connection->createCommand($sql2)->execute();
            $transaction->commit();

            
            //  发送 发货短信给消费者
            $content = ShopConfig::getConfigValue('sms_order_shipped_content');
            $content = str_replace(
                    ['#order_sn#', '#ship_name#', '#invoice_no#'],
                    [$model->order_sn, $shipping_name, $invoice_no],
                    $content
            );
            Yii::info(DateTimeHelper::getFormatDateTime(time()).'向'.$model->mobile.'发送 订单发货短信：'.$content);
            //发送短信
            SMSHelper::sendSms($model->mobile, $content);

            echo json_encode([
                'code' => 0,
                'msg' => '发货成功',
            ]);
            exit();
        } catch (Exception $e) {
            $transaction->rollBack();
            echo json_encode([
                'code' => 1,
                'msg' => '发货单提交失败，请重试',
            ]);
            exit();
        }

    }


    /**
     * Deletes an existing DeliveryOrder model.
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
     * Finds the DeliveryOrder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return DeliveryOrder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DeliveryOrder::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
