<?php

namespace service\controllers;

use common\helper\DateTimeHelper;
use common\helper\NumberHelper;
use common\models\ServicerDivideRecord;
use Yii;
use common\models\CashRecord;
use common\models\CashRecordSearch;
use yii\filters\AccessControl;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\ServiceUser;
use common\helper\ServicerDivideHelper;
use common\models\OrderInfo;

/**
 * ServiceCashRecordController implements the CRUD actions for CashRecord model.
 */
class ServiceCashRecordController extends XmController
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
                        'actions' => ['index', 'create-out-record'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'create-out-record' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all CashRecord models.
     * @return mixed
     */
    public function actionIndex()
    {
        $inCash = 0.00;
        $outCash = 0.00;
        $monthInCash = 0.00;
        $monthOutCash = 0.00;
        //当前是一级服务商  身份判定与 cron/actionServicerCashAll 一致

        if(
            Yii::$app->user->identity['servicer_super_id'] == 0 ||
            Yii::$app->user->identity['user_id'] == 1150 ||
            (
                Yii::$app->user->identity['servicer_super_id'] == Yii::$app->user->identity['user_id'] &&
                Yii::$app->user->identity['servicer_user_id'] == Yii::$app->user->identity['user_id']
            )
        ) {
            //获取旗下所有二级服务商
//            $servicers = ServiceUser::findAll(['servicer_parent_id'=>Yii::$app->user->identity['user_id']]);
//            $query = ServiceUser::find();
//            $servicers = $query->select(['user_id', 'user_name', 'mobile_phone', 'servicer_code'=>'su.servicer_code'])
//                ->joinWith('servicerUserInfo su')
//                ->where(['servicer_super_id'=>Yii::$app->user->identity['user_id']])
//                ->all();
//
//            if(count($servicers) > 0) {
//                foreach($servicers as $servicer) {
//                    $servicer_user_ids[] = $servicer['user_id'];
//                }
//
//                $amounts = ServicerDivideHelper::getTotalDivideAmount($servicer_user_ids);
//
//                foreach($servicers as &$servicer) {
//                    foreach($amounts as $amount) {
//                        if($servicer->user_id == $amount['servicer_user_id']) {
//                            $servicer->divide_amount = $amount['total_amount'];
//                        }
//                    }
//                }
//            }

            $inCash = CashRecord::totalInCash();
            $inCash = empty($inCash) ? 0.00: $inCash;
            $outCash = CashRecord::totalOutCash();
            $outCash = empty($outCash) ? 0.00: $outCash;

            $monthInCash = CashRecord::monthTotalInCash();
            $monthInCash = empty($monthInCash) ? 0.00 : $monthInCash;
            $monthOutCash = CashRecord::monthTotalOutCash();
            $monthOutCash = empty($monthOutCash) ? 0.00: $monthOutCash;
        }

        $searchModel = new CashRecordSearch();
        $searchModel->date_added = DateTimeHelper::getFormatDate(time() - 30 * 24 * 60 * 60);
        $searchModel->date_modified = DateTimeHelper::getFormatDate(time());
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        Yii::info('inCash = '. $inCash. ', outCash = '. $outCash. ', monthInCash = '. $monthInCash. ', monthOutCash = '. $monthOutCash, __METHOD__);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'inCash' => NumberHelper::price_format($inCash),
            'outCash' => NumberHelper::price_format($outCash),
            'monthInCash' => NumberHelper::price_format($monthInCash),
            'monthOutCash' => NumberHelper::price_format($monthOutCash),
            'index' => 5,
        ]);
    }

//    /**
//     * Displays a single CashRecord model.
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
//     * Creates a new CashRecord model.
//     * If creation is successful, the browser will be redirected to the 'view' page.
//     * @return mixed
//     */
//    public function actionCreate()
//    {
//        $model = new CashRecord();
//
//        if ($model->load(Yii::$app->request->post()) && $model->save()) {
//            return $this->redirect(['view', 'id' => $model->id]);
//        } else {
//            return $this->render('create', [
//                'model' => $model,
//            ]);
//        }
//    }

//    /**
//     * 入账到钱包
//     * @return string|\yii\web\Response
//     */
//    public function actionCreateInRecord() {
//        $model = new CashRecord();
//
//        $divideRecordIds = json_decode(Yii::$app->request->post('divide_record_ids'));
//        $note = addslashes(Yii::$app->request->post('note'));
//        if(count($divideRecordIds)) {
////            $divideRecords = ServicerDivideRecord::findAll(['in', 'id', $divideRecordIds]);
//
//            $divideRecords = ServicerDivideRecord::find()->select('id', 'divide_amount')->andWhere(['in', 'id', $divideRecordIds])->all();
//            $total = 0;
//            foreach($divideRecords as $record) {
//                $total += $record->divide_amount;
//            }
//
//            $totalCash = CashRecord::totalCash();
//
//            $model->user_id = Yii::$app->user->identity->user_id;
//            $model->cash = $total;
//            $model->created_time = Yii::$app->formatter->asDate(time());
//            $model->note = $note;
//            $model->balance = $totalCash + $total;
//
//            if($model->save()) {
//                return $this->redirect(['view', 'id' => $model->id]);
//            }
//        }
//
//        return $this->render('create', [
//            'model' => $model,
//        ]);
//    }

    /**
     * 申请银行汇款
     * @return string|\yii\web\Response
     */
    public function actionCreateOutRecord() {
        $model = new CashRecord();

        $cash = Yii::$app->request->post('cash', 0);
        if($cash == 0) {
            Yii::info('请输入提取金额', __METHOD__);
            die(json_encode(['code'=>1, 'msg'=>'请输入提取金额']));
        }
        else {
            if($cash < 0) {
                Yii::info('提现金额不能为负数', __METHOD__);
                die(json_encode(['code'=>3, 'msg'=>'提现金额不能为负数']));
            }

            $totalCash = CashRecord::totalCash();
            if($totalCash < $cash) {
                Yii::info('余额不足', __METHOD__);
                die(json_encode(['code'=>2, 'msg'=>'余额不足']));
            }

            $model->user_id = Yii::$app->user->identity->user_id;
            $model->cash = -$cash;
            $model->created_time = DateTimeHelper::getFormatDateTime(time());
            $model->balance = $totalCash - $cash;

            if($model->save()) {
                Yii::info('申请提取成功 model = '. VarDumper::export($model), __METHOD__);
                die(json_encode(['code'=>0, 'msg'=>'申请提取成功，请等待汇款']));
            }
            else {
                Yii::info('申请提取失败 code = 4, errors = '. VarDumper::export($model->errors), __METHOD__);
                die(json_encode(['code'=>4, 'msg'=>json_encode($model->errors[0])]));
            }
        }
//        if ($model->load(Yii::$app->request->post()) && $model->save()) {
//            return $this->redirect(['view', 'id' => $model->id]);
//        } else {
//            return $this->render('create', [
//                'model' => $model,
//            ]);
//        }
    }

    /**
     * Updates an existing CashRecord model.
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
     * Deletes an existing CashRecord model.
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
     * Finds the CashRecord model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CashRecord the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CashRecord::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
