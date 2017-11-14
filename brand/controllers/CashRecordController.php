<?php

namespace brand\controllers;

use common\helper\DateTimeHelper;
use common\models\ShopConfig;
use Yii;
use brand\models\CashRecord;
use common\models\CashRecord as CashRecordBase;
use brand\models\CashRecordSearch;
use brand\models\BrandDivideRecord;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\BrandUser;

/**
 * CashRecordController implements the CRUD actions for CashRecord model.
 */
class CashRecordController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'create-out-record'],
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
     * Lists all CashRecord models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CashRecordSearch();

        $queryParams = Yii::$app->request->queryParams;
        if (empty($queryParams['end_date'])) {
            $queryParams['end_date'] = DateTimeHelper::getFormatDate(time());
        }
        if (empty($queryParams['start_date'])) {
            $start_data = strtotime('-1 month', strtotime($queryParams['end_date']));
            $queryParams['start_date'] = DateTimeHelper::getFormatDate($start_data);
        }


        $queryParams['user_id'] = Yii::$app->user->identity['user_id'];
        $dataProvider = $searchModel->search($queryParams);

        //  品牌商创建时间
        $start_time = BrandUser::find()->where(['user_id' => Yii::$app->user->identity->getId()])->one()->reg_time;
        $total_cash = CashRecord::totalCash();
        $total_in_cash = CashRecord::totalInCash();
        $total_out_cash = CashRecord::totalOutCash();
//        $total_active = BrandDivideRecord::totalActive();    //  品牌商可提取总额
        $total_frozen = BrandDivideRecord::totalFrozen($start_time);
//        $total_untracted = bcadd($total_active, $total_frozen, 4);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'record_list' => $dataProvider->getModels(),
            'queryParams' => $queryParams,
            'r_version' => Yii::$app->params['r_version'],
            'total_cash' => $total_cash,
            'total_in_cash' => $total_in_cash,
            'total_out_cash' => $total_out_cash,
//            'total_active' => $total_active,
            'total_frozen' => $total_frozen,
//            'total_untracted' => $total_untracted,
        ]);
    }

    /**
     * Displays a single CashRecord model.
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
     * Creates a new CashRecord model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new CashRecord();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing CashRecord model.
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
     * Deletes an existing CashRecord model.
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

    /**
     * 申请银行汇款
     * @return string|\yii\web\Response
     */
    public function actionCreateOutRecord() {
        $model = new CashRecord();

        $cash = Yii::$app->request->post('cash', 0);
        $withdraw_min = ShopConfig::getConfigValue('withdraw_min');
        if(!is_numeric($cash) || $cash < $withdraw_min) {
            die(json_encode(['code'=>1, 'msg'=>'请输入有效数值，最低提现金额为'.$withdraw_min]));
        }
        else {
            $totalCash = CashRecordBase::totalCash();
            if($totalCash < $cash) {
                die(json_encode(['code'=>2, 'msg'=>'余额不足']));
            }

            $model->user_id = Yii::$app->user->identity->user_id;
            $model->cash = -$cash;
            $model->created_time = DateTimeHelper::getFormatDateTime(time());
            $model->balance = $totalCash - $cash;

            if($model->save()) {
                die(json_encode(['code'=>0, 'msg'=>'申请提取成功，请等待汇款']));
            }
            else {
                die(json_encode(['code'=>4, 'msg'=>json_encode($model->errors[0])]));
            }
        }

    }
}
