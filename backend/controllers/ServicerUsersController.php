<?php

namespace backend\controllers;

use backend\models\UserRegion;
use common\helper\CacheHelper;
use common\models\BankInfo;
use common\models\Brand;
use common\models\ServicerSpecStrategy;
use Yii;
use backend\models\Users;
use backend\models\UsersSearch;
use common\models\ServicerUserInfo;
use yii\db\Query;
use common\controllers\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * ServicerUsersController implements the CRUD actions for Users model.
 */
class ServicerUsersController extends Controller
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
                            $model->servicer_user_id = $model->user_id;
                            $model->servicer_super_id = $model->user_id;
                            if ($model->save()) {
                                $auth = Yii::$app->getAuthManager();
                                $auth->assign($auth->getRole('service_boss'), $model->user_id);

                                UserRegion::deleteAll([
                                    'user_id' => $model->user_id,
                                ]);

                                if (!empty($model->regionList)) {
                                    foreach ($model->regionList as $regionId) {
                                        $userRegion = new UserRegion();
                                        $userRegion->user_id = $model->user_id;
                                        $userRegion->region_id = $regionId;
                                        $userRegion->save();
                                    }
                                }

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

        $regions = $model->regions;
        foreach ($regions as $region) {
            $model->regionList[] = $region['region_id'];
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

                UserRegion::deleteAll([
                    'user_id' => $model->user_id,
                ]);

                if (!empty($model->regionList)) {
                    foreach ($model->regionList as $regionId) {
                        $userRegion = new UserRegion();
                        $userRegion->user_id = $model->user_id;
                        $userRegion->region_id = $regionId;
                        $userRegion->save();
                    }
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

        $userPost = Yii::$app->request->post('Users');

        $model = Users::findOne([
            'user_id' => $userPost['user_id'],
        ]);
        $bankModel = new BankInfo();

        if (!isset($model)) {
            $model = new Users();
        }
        elseif ($model->servicer_info_id > 0) {
            Yii::$app->session->setFlash('error', '用户已经是服务商，请联系技术人员确认');
            return $this->redirect('upgrade');
        }

        if ($model->load(Yii::$app->request->post()) && $bankModel->load(Yii::$app->request->post())) {

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
                                $this->flashSuccess('操作成功');
                                return $this->redirect(['view', 'id' => $model->user_id]);
                            }
                            else {
                                $this->flashError($model);
                            }
                        }
                        else {
                            $this->flashError($servicerUserInfo);
                        }
                    }
                    else {
                        $this->flashError($servicerUserInfo);
                    }
                }
                else {
                    $this->flashError($bankModel);
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
