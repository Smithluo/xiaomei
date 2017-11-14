<?php

namespace brand\controllers;

use common\helper\DateTimeHelper;
use brand\models\OrderInfo;
use Yii;
use brand\models\BrandDivideRecord;
use brand\models\BrandDivideRecordSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\CashRecord;
use yii\filters\AccessControl;

/**
 * BrandDivideRecordController implements the CRUD actions for BrandDivideRecord model.
 */
class BrandDivideRecordController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['cash', 'update'],
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
     * Lists all BrandDivideRecord models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BrandDivideRecordSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single BrandDivideRecord model.
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
     * Creates a new BrandDivideRecord model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new BrandDivideRecord();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing BrandDivideRecord model.
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
     * Deletes an existing BrandDivideRecord model.
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
     * Finds the BrandDivideRecord model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return BrandDivideRecord the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BrandDivideRecord::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * 提取订单分成到钱包
     * @throws \yii\db\Exception
     */
    public function actionCash() {
        $id = Yii::$app->request->post('id');
        $type = Yii::$app->request->get('type');
        if(empty($id) && (!isset($type) || $type != 'all')) {
            die(json_encode([
                'code'=>1,
                'msg'=>'缺少参数',
            ]));
        }

        $session = Yii::$app->session;
        if ($type == 'all') {
            $divideRecords = BrandDivideRecord::findAll([
                'cash_record_id' => 0,
                'status' => BrandDivideRecord::BRAND_DIVIDE_RECORD_STATUS_UNTRACTED,
                'brand_id' => $session->get('user_brand_list')
            ]);
        } else {
            $divideRecords[] = BrandDivideRecord::findOne([
                'id' => $id,
                'cash_record_id' => 0,
                'status' => BrandDivideRecord::BRAND_DIVIDE_RECORD_STATUS_UNTRACTED,
                'brand_id' => $session->get('user_brand_list')
            ]);
        }

        if($divideRecords == NULL) {
            die(json_encode(
                [
                    'code' => 4,
                    'msg' => '没有可以提取的订单',
                ]
            ));
        }
        foreach ($divideRecords as $divideRecord) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $totalCash = CashRecord::totalCash();
                $totalCash = empty($totalCash) ? 0.00 : $totalCash;

                $cashRecord = new CashRecord();
                $cashRecord->cash = $divideRecord->divide_amount;
                $cashRecord->user_id = Yii::$app->user->identity['user_id'];
                $cashRecord->note = OrderInfo::findOne(['order_id' => $divideRecord->order_id])->order_sn;
                $cashRecord->created_time = DateTimeHelper::getFormatDateTime(time());
                $cashRecord->balance = bcadd($totalCash, $divideRecord->divide_amount, 2);

                if($cashRecord->validate()) {
                    Yii::$app->db->createCommand()->insert(CashRecord::tableName(), [
                        'cash' => $cashRecord->cash,
                        'user_id' => $cashRecord->user_id,
                        'note' => $cashRecord->note,
                        'created_time' => $cashRecord->created_time,
                        'balance' => $cashRecord->balance,
                    ])->execute();
                    $cashRecordId = Yii::$app->db->getLastInsertID();

                    Yii::$app->db->createCommand()->update(
                        BrandDivideRecord::tableName(),
                        [
                            'cash_record_id' => $cashRecordId,
                            'status' => BrandDivideRecord::BRAND_DIVIDE_RECORD_STATUS_TRACTED
                        ],
                        ['id' => $id]
                    )->execute();

                    $transaction->commit();

                    die(json_encode([
                        'code' => 0,
                        'msg' => '提取成功',
                    ]));
                }

                $transaction->rollBack();

                die(json_encode([
                    'code' => 2,
                    'msg' => '提取失败',
                    'data' => $cashRecord->errors,
                ]));
            }catch (Exception $e) {
                $transaction->rollBack();
                die(json_encode([
                    'code' => 3,
                    'msg' => '提取失败',
                    'data' => $cashRecord->errors,
                ]));
            }
        }

    }

}
