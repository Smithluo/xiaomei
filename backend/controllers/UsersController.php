<?php

namespace backend\controllers;

use backend\models\Users;
use backend\models\UsersSearch;
use backend\models\UserRank;
use common\helper\CacheHelper;
use common\models\BankInfo;
use common\models\Brand;
use common\models\Region;
use common\models\ServicerSpecStrategy;
use common\models\ServicerUserInfo;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;

/**
 * UsersController implements the CRUD actions for Users model.
 */
class UsersController extends Controller
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
                        'actions' => ['index', 'update', 'create', 'view', 'delete', 'upgrade'],
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
     * Lists all Users models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UsersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Users model.
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
     * Creates a new Users model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Users();
        $bankModel = new BankInfo();

        if ($model->load(Yii::$app->request->post()) && $bankModel->load(Yii::$app->request->post())) {
            $model->setPassword($model->password);
            $model->ec_salt = null;

            do {
                $servicer_code = uniqid('XM');
            }while(ServicerUserInfo::findOne(['servicer_code' => $servicer_code]));

            $servicerUserInfo= new ServicerUserInfo();
            $servicerUserInfo->servicer_code = $servicer_code;
            if($servicerUserInfo->save()) {

                $servicer_code = 'XM'. sprintf('%08d', $servicerUserInfo->id);
                $servicerUserInfo->servicer_code = $servicer_code;
                //  默认服务商为审核通过的普通会员
                $model->user_rank = UserRank::USER_RANK_REGISTED;
                $model->is_checked = Users::IS_CHECKED_STATUS_PASSED;

                if($servicerUserInfo->save()) {
                    if(empty($servicerUserInfo->servicer_code)) {
                        $model->servicer_info_id = 0;
                    }
                    else {
                        $model->servicer_info_id = $servicerUserInfo->id;
                    }

                    $model->reg_time = time();

                    if($bankModel->save()) {
                        $model->bank_info_id = $bankModel->id;

                        if($model->save()) {
                            $brands = Brand::find()->select(['brand_id', 'servicer_strategy_id'])->where(['not', ['servicer_strategy_id' => 0]])->all();
                            if(count($brands) > 0) {
                                $keys = ['brand_id', 'servicer_user_id', 'percent_level_2', 'percent_level_1'];
                                foreach($brands as $brand) {
                                    $value = [];
                                    $value[] = $brand->brand_id;
                                    $value[] = $model->user_id;
                                    $value[] = 20;
                                    $value[] = 80;

                                    $values[] = $value;
                                }

                                Yii::$app->db->createCommand()->batchInsert(ServicerSpecStrategy::tableName(),
                                    $keys,
                                    $values
                                )->execute();
                            }

                            $model->servicer_user_id = $model->user_id;
                            $model->servicer_super_id = $model->user_id;
                            if ($model->save()) {
                                Yii::$app->session->setFlash('success', '服务商用户已绑定自身');
                            } else {
                                Yii::$app->session->setFlash('warning', '服务商未成功绑定自身，如果服务商信息创建成功，请编辑服务商信息保存，系统将再次执行服务商绑定绑定自身');
                            }

                            CacheHelper::setServicerCache();    //  创建业务员后及时更新缓存，便于后台审核业务员
                            return $this->redirect(['view', 'id' => $model->user_id]);
                        }
                    }
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
            'bankModel' => $bankModel,
        ]);
    }

    /**
     * Updates an existing Users model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $bankModel = $model->bankinfo;
        if($bankModel == null) {
            $bankModel = new BankInfo();
        }
        if ($model->load(Yii::$app->request->post()) && $bankModel->load(Yii::$app->request->post())) {
            if($bankModel->save()) {
                if($model->bank_info_id == 0) {
                    $model->bank_info_id = $bankModel->id;
                }

                //  更新用户信息不需要重置密码
//                $model->setPassword($model->password);
//                $model->ec_salt = null;
                if ($model->token_expired == null) {
                    $model->token_expired = '';
                }
                //  如果服务商 没有绑定业务员 则在编辑保存的时候 绑定自己的业务码
                if ($model->parent_id == 0 && $model->servicer_user_id == 0) {
                    $model->servicer_user_id = $model->user_id;
                    $model->servicer_super_id = $model->user_id;
                }

                if ($model->save()) {
                    return $this->redirect(['view', 'id' => $model->user_id]);
                }
            }
        }

        $model->password = '';

        return $this->render('update', [
            'model' => $model,
            'bankModel' => $bankModel,
        ]);
    }

    public function actionUpgrade() {
        $model = new Users();
        $bankModel = new BankInfo();
        if ($model->load(Yii::$app->request->post()) && $bankModel->load(Yii::$app->request->post())) {

            $model = Users::findOne(['user_name' => $model->user_name]);

            if (!empty($model)) {
                do {
                    $servicer_code = uniqid('XM');
                }while(ServicerUserInfo::findOne(['servicer_code' => $servicer_code]));

                $servicerUserInfo= new ServicerUserInfo();
                $servicerUserInfo->servicer_code = $servicer_code;

                if($bankModel->save()) {
                    $model->bank_info_id = $bankModel->id;

                    if ($servicerUserInfo->save()) {
                        $servicer_code = 'XM'. sprintf('%08d', $servicerUserInfo->id);
                        $servicerUserInfo->servicer_code = $servicer_code;

                        if ($servicerUserInfo->save()) {
                            $model->servicer_info_id = $servicerUserInfo->id;
                            if ($model->save()) {
                                return $this->redirect(['view', 'id' => $model->user_id]);
                            }
                        }
                    }
                }
            }
        }
        
        return $this->render('_form_upgrade', [
            'model' => $model,
            'bankModel' => $bankModel,
        ]);
    }

    /**
     * Deletes an existing Users model.
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
     * Finds the Users model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Users the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Users::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
