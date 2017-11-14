<?php

namespace backend\controllers;

use backend\models\Users;
use common\helper\TextHelper;
use Yii;
use backend\models\Integral;
use common\models\IntegralSearch;
use yii\db\Exception;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * IntegralController implements the CRUD actions for Integral model.
 */
class IntegralController extends Controller
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
     * Lists all Integral models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new IntegralSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $statusMap = Integral::$statusMap;
        $payCodeMap = Integral::$payCodeMap;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'statusMap' => $statusMap,
            'payCodeMap' => $payCodeMap,
        ]);
    }

    /**
     * Displays a single Integral model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $statusMap = Integral::$statusMap;
        $payCodeMap = Integral::$payCodeMap;

        $model = $this->findModel($id);

        return $this->render('view', [
            'model' => $model,
            'statusMap' => $statusMap,
            'payCodeMap' => $payCodeMap,
        ]);
    }

    /**
     * Creates a new Integral model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Integral();

        if ($model->load(Yii::$app->request->post())) {
            //  手动修改积分 强制记录操作人信息
            $note = Yii::$app->user->identity->showName.'('.Yii::$app->request->getUserIP().')'.$model->note;
            $model->setAttribute('note', $note);


            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();
            $tb = Integral::tableName();
            $msg = '';
            try {
                $insertData = [
                    'integral' => $model->integral,
                    'user_id' => $model->user_id,
                    'pay_code' => $model->pay_code,
                    'out_trade_no' => $model->out_trade_no,
                    'note' => $model->note,
                    'created_at' => $model->created_at,
                    'updated_at' => $model->updated_at,
                    'status' => $model->status,
                ];

                Yii::trace($note.'手动添加积分流水。'.json_encode($insertData));
                $connection->createCommand()->insert($tb,$insertData)->execute();

                if ($id = Yii::$app->db->getLastInsertID()) {
                    //  如果当前记录的状态有变化，则修正 当前可用余额（最后一条生效记录的余额）
                    $balance = Integral::getBalance($model->user_id);

                    if ($balance < 0) {
                        $msg .= '积分可用余额不能小于0.';
                    }

                    $connection->createCommand()->update(
                        Users::tableName(),
                        ['int_balance' => $balance],
                        ['user_id' => $model->user_id]
                    )->execute();
                }

                $transaction->commit();
                return $this->redirect(['view', 'id' => $id]);
            } catch (Exception $e) {
                $msg .= '积分流水创建失败';
                $transaction->rollBack();
            }

            if ($msg) {
                Yii::$app->session->setFlash('error', $msg);
            } else {
                Yii::$app->session->setFlash('success', '积分流水创建成功');
            }
        }

        $statusMap = Integral::$statusMap;
        $payCodeMap = Integral::$payCodeMap;

        return $this->render('create', [
            'model' => $model,
            'statusMap' => $statusMap,
            'payCodeMap' => $payCodeMap,
        ]);
    }

    /**
     * Updates an existing Integral model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        //  更新 不允许手动编辑余额，自动计算
        if ($model->load(Yii::$app->request->post())) {
            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();
            $tb = Integral::tableName();
            try {
                $model->note = Yii::$app->user->identity->showName.'('.Yii::$app->request->getUserIP().')'.$model->note;
                $updateData = [
                    'integral' => $model->integral,
                    'user_id' => $model->user_id,
                    'pay_code' => $model->pay_code,
                    'out_trade_no' => $model->out_trade_no,
                    'note' => $model->note,
                    'created_at' => $model->created_at,
                    'updated_at' => $model->updated_at,
                    'status' => $model->status,
                ];
                Yii::trace('手动修改积分流水。'.json_encode($updateData));
                $connection->createCommand()->update($tb, $updateData, ['id' => $id])->execute();
                //  如果当前记录的状态有变化，则修正 当前可用余额（最后一条生效记录的余额）
                $userModel = Users::find()->where(['user_id' => $model->user_id])->one();
                if ($userModel) {
                    $balance = Integral::getBalance($userModel->user_id);

                    $connection->createCommand()->update(
                        Users::tableName(),
                        ['int_balance' => $balance],
                        ['user_id' => $model->user_id]
                    )->execute();

                    if ($balance < 0) {
                        Yii::$app->session->setFlash('error', '积分可用余额不能小于0');
                        throw new Exception('积分可用余额不能小于0', 1);
                    }
                } else {
                    Yii::$app->session->setFlash('error', '指定的用户不存在');
                    throw new Exception('指定的用户不存在', 2);
                }

                $transaction->commit();
                return $this->redirect(['view', 'id' => $model->id]);
            } catch (Exception $e) {
                Yii::$app->session->setFlash('error', '积分流水更新失败');
                $transaction->rollBack();
            }
        }

        $statusMap = Integral::$statusMap;
        $payCodeMap = Integral::$payCodeMap;

        return $this->render('update', [
            'model' => $model,
            'statusMap' => $statusMap,
            'payCodeMap' => $payCodeMap,
        ]);
    }

    /**
     * Deletes an existing Integral model.
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
     * Finds the Integral model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Integral the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Integral::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
