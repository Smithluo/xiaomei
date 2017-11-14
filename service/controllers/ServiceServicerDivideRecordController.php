<?php

namespace service\controllers;

use common\helper\DateTimeHelper;
use common\models\CashRecord;
use common\models\OrderInfo;
use Tale\Jade\Compiler\Exception;
use Yii;
use common\models\ServicerDivideRecord;
use common\models\ServicerDivideRecordSearch;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ServiceServicerDivideRecordController implements the CRUD actions for ServicerDivideRecord model.
 */
class ServiceServicerDivideRecordController extends XmController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'cash', 'view'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all ServicerDivideRecord models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ServicerDivideRecordSearch();
        $searchModel->date_added = DateTimeHelper::getFormatDate(time() - 30 * 24 * 60 * 60);
        $searchModel->date_modified = DateTimeHelper::getFormatDate(time());
        $dataProvider = $searchModel->searchByOrder(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'index' => 1,
        ]);
    }

    public function actionView($id) {
        $model = OrderInfo::find()->with([
            'users',
            'ordergoods',
            'servicerDivideRecord',
        ])->where([
            'order_id' => $id,
        ])->one();

        if (!$model) {
            throw new NotFoundHttpException('未找到订单');
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    public function actionSalesManIndex() {
        $searchModel = new ServicerDivideRecordSearch();
        $searchModel->date_added = DateTimeHelper::getFormatDate(time() - 30 * 24 * 60 * 60);
        $searchModel->date_modified = DateTimeHelper::getFormatDate(time());
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'index' => 1,
        ]);
    }

//    /**
//     * Displays a single ServicerDivideRecord model.
//     * @param integer $id
//     * @return mixed
//     */
//    public function actionView($id)
//    {
//        return $this->render('view', [
//            'model' => $this->findModel($id),
//        ]);
//    }
//
//    /**
//     * Creates a new ServicerDivideRecord model.
//     * If creation is successful, the browser will be redirected to the 'view' page.
//     * @return mixed
//     */
//    public function actionCreate()
//    {
//        $model = new ServicerDivideRecord();
//
//        if ($model->load(Yii::$app->request->post()) && $model->save()) {
//            return $this->redirect(['view', 'id' => $model->id]);
//        } else {
//            return $this->render('create', [
//                'model' => $model,
//            ]);
//        }
//    }
//
//    /**
//     * Updates an existing ServicerDivideRecord model.
//     * If update is successful, the browser will be redirected to the 'view' page.
//     * @param integer $id
//     * @return mixed
//     */
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
//
//    /**
//     * Deletes an existing ServicerDivideRecord model.
//     * If deletion is successful, the browser will be redirected to the 'index' page.
//     * @param integer $id
//     * @return mixed
//     */
//    public function actionDelete($id)
//    {
//        $this->findModel($id)->delete();
//
//        return $this->redirect(['index']);
//    }


    public function actionCash() {
        $ids = Yii::$app->request->post('ids');
        if(empty($ids)) {
            Yii::info('缺少ids', __METHOD__);
            die(json_encode([
                'code'=>1,
                'msg'=>'缺少参数',
            ]));
        }

        $divideRecords = ServicerDivideRecord::findAll(['id' => $ids, 'money_in_record_id' => 0, 'parent_servicer_user_id' => Yii::$app->user->identity['user_id']]);

        if(empty($divideRecords)) {
            Yii::info('没有找到可提取到钱包的订单', __METHOD__);
            die(json_encode(
                [
                    'code' => 4,
                    'msg' => '没有可以提取的订单',
                ]
            ));
        }
        else {
            Yii::info('提现的分成列表: '. VarDumper::export($divideRecords), __METHOD__);
        }

        $currCash = 0.0;
        $currCashLevel2 = 0.0;
        $orderSn = '';
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $keys = ['cash', 'user_id', 'note', 'created_time', 'balance'];
            $data = [];
            foreach($divideRecords as $divideRecord) {
                $currCashLevel2 += $divideRecord->divide_amount;
                $currCash += $divideRecord->divide_amount + $divideRecord->parent_divide_amount;        //把二级服务商的余额也提取出来
                $orderSn .= $divideRecord->orderInfo->order_sn;

                if($divideRecord->servicer_user_id > 0) {

                    $origTotalCash = CashRecord::totalCash($divideRecord->servicer_user_id);
                    $origTotalCash = empty($origTotalCash) ? 0.00 : $origTotalCash;

                    //给二级服务商批量创建流水
                    $data[] = [
                        0 => $currCashLevel2,
                        1 => $divideRecord->servicer_user_id,
                        2 => $divideRecord->orderInfo->order_sn,
                        3 => Yii::$app->formatter->asDatetime(time(), 'yyyy-MM-dd hh:mm:ss'),
                        4 => $origTotalCash + $currCashLevel2,
                    ];
                }
            }

            Yii::info('二级服务商批量插入提取到钱包 data = '. VarDumper::export($data), __METHOD__);

            Yii::$app->db->createCommand()->batchInsert(CashRecord::tableName(), $keys, $data)->execute();

            $cashRecord = new CashRecord();
            $cashRecord->cash = $currCash;
            $cashRecord->user_id = Yii::$app->user->identity['user_id'];
            $cashRecord->note = $orderSn;
            $cashRecord->created_time = Yii::$app->formatter->asDatetime(time(), 'yyyy-MM-dd hh:mm:ss');

            $totalCash = CashRecord::totalCash();
            $totalCash = empty($totalCash) ? 0.00 : $totalCash;

            $cashRecord->balance = $totalCash + $currCash;

            if($cashRecord->validate()) {
                Yii::info('插入主分成记录 cashRecord = '. VarDumper::export($cashRecord), __METHOD__);
                if(Yii::$app->db->createCommand()->insert(CashRecord::tableName(), [
                    'cash' => $cashRecord->cash,
                    'user_id' => $cashRecord->user_id,
                    'note' => $cashRecord->note,
                    'created_time' => $cashRecord->created_time,
                    'balance' => $cashRecord->balance,
                ])->execute()) {

                    $cashRecordId = Yii::$app->db->getLastInsertID();

                    Yii::$app->db->createCommand()->update(ServicerDivideRecord::tableName(), ['money_in_record_id'=>$cashRecordId], ['id' => $ids])->execute();

                    Yii::info('更新流水记录 money_in_record_id = '. VarDumper::export($cashRecordId). ', ids = '. VarDumper::export($ids), __METHOD__);

                    $transaction->commit();

                    Yii::info('提取成功');

                    die(json_encode([
                        'code' => 0,
                        'msg' => '提取成功',
                    ]));
                }
            }

            $transaction->rollBack();

            Yii::info('提取失败 code = 2, errors = '. VarDumper::export($cashRecord->errors));

            die(json_encode([
                'code' => 2,
                'msg' => '提取失败',
                'data' => $cashRecord->errors,
            ]));
        }catch (Exception $e) {
            $transaction->rollBack();

            Yii::info('提取失败 code = 3, e = '. VarDumper::export($e));

            die(json_encode([
                'code' => 3,
                'msg' => '提取失败',
                'data' => $cashRecord->errors,
            ]));
        }
    }

    /**
     * Finds the ServicerDivideRecord model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ServicerDivideRecord the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ServicerDivideRecord::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
