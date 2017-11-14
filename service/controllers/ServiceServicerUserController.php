<?php

namespace service\controllers;

use common\helper\CacheHelper;
use common\helper\DateTimeHelper;
use common\models\CashRecord;
use common\models\UserRank;
use service\models\Users;
use Yii;
use common\models\ServicerUserInfo;
use common\models\ServiceUser;
use service\models\ServiceUserSearch;
use yii\base\ErrorException;
use yii\filters\AccessControl;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ServiceServicerUserController implements the CRUD actions for ServiceUser model.
 */
class ServiceServicerUserController extends XmController
{
    public $enableCsrfValidation = false;

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
                        'actions' => ['index', 'create', 'update', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    'update' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all ServiceUser models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ServiceUserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, true);

        $roles = Users::find()
            ->select(['user_id'])
            ->where(['servicer_super_id'=>Yii::$app->user->identity['user_id']])
            ->asArray()
            ->all();

        $notHasManager = true;

        foreach($roles as $v)
        {
            if(!empty(Yii::$app->authManager->getRolesByUser($v['user_id'])['service_manager']))
            {
                $notHasManager = false;
            }
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'notHasManager' => $notHasManager,
            'index' => 2,
        ]);
    }

    /**
     * Creates a new ServiceUser model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ServiceUser();
        $servicerUserInfo = new ServicerUserInfo();

        if ($model->load(Yii::$app->request->post())) {
            $model->setPassword('888888');
            $model->ec_salt = null;
            $model->servicer_super_id = Yii::$app->user->identity['user_id'];
            $model->company_name = Yii::$app->user->identity['company_name'];
            //  默认业务员为审核通过的普通会员
            $model->user_rank = UserRank::USER_RANK_REGISTED;
            $model->is_checked = Users::IS_CHECKED_STATUS_PASSED;
            $model->province = Yii::$app->user->identity['province'];

            do {
                $user_name = 'XM_'.uniqid();
            }while(ServiceUser::findOne(['user_name' => $user_name]));

            $model->user_name = $user_name;

            do {
                $servicer_code = 'XM'.uniqid();
            }while(ServicerUserInfo::findOne(['servicer_code' => $servicer_code]));

            $servicerUserInfo->servicer_code = $servicer_code;
            if($servicerUserInfo->save()) {

                //改用id作为业务码,为了不影响其它地方逻辑暂时多执行一次保存
                $servicerUserInfo->servicer_code = 'XM'. sprintf('%08d', $servicerUserInfo->id);

                if($servicerUserInfo->save()) {
                    Yii::info('生成code成功 servicerUserInfo = '. VarDumper::export($servicerUserInfo), __METHOD__);

                    if(empty($servicerUserInfo->servicer_code)) {
                        Yii::info('code为空', __METHOD__);
                        $model->servicer_info_id = 0;
                    }
                    else {
                        $model->servicer_info_id = $servicerUserInfo->id;
                    }
                    $model->reg_time = DateTimeHelper::getFormatGMTTimesTimestamp(time());

                    $roles = Users::find()
                        ->select(['user_id'])
                        ->where(['servicer_super_id'=>Yii::$app->user->identity['user_id']])
                        ->asArray()
                        ->all();
                    //是否不含有业务经理角色
                    $notHasManager = true;

                    foreach($roles as $v)
                    {
                        if(!empty(Yii::$app->authManager->getRolesByUser($v['user_id'])['service_manager']))
                        {
                            $notHasManager = false;
                        }
                    }
                    //不含有 业务经理角色
                    $auth = Yii::$app->getAuthManager();
                    if($notHasManager === true || $model->role == 'saleman')
                    {
                        if($model->save())
                        {
                            if($model->role == 'saleman' )
                            {
                                $auth->assign($auth->getRole('service_saleman'), $model->user_id);
                            }
                            elseif($model->role == 'manager' )
                            {
                                $auth->assign($auth->getRole('service_manager'), $model->user_id);
                            }

                            Yii::info('创建用户成功 model = '. VarDumper::export($model), __METHOD__);
                            CacheHelper::setServicerCache();    //  创建业务员后及时更新缓存，便于后台审核业务员
                            die(json_encode([
                                'code'=>0,
                                'msg'=>'创建用户成功',
                            ]));
                            return $this->redirect(['index']);
                        }
                    }
                    elseif($notHasManager === false && $model->role == 'manager')
                    {
                        die(json_encode([
                            'code'=>7,
                            'msg'=>'业务经理有且只有一个',
                        ]));
                    }

                    if($model->hasErrors()) {
                        Yii::info('创建用户失败 errors = '. VarDumper::export($model->errors), __METHOD__);
                        $firstError = $model->getFirstError(array_keys($model->errors)[0]);
                        die(json_encode([
                            'code'=>1,
                            'msg'=>$firstError,
                        ]));
                    }
                }
                else {
                    Yii::info('生成验证码失败 errors = '. VarDumper::export($servicerUserInfo->errors), __METHOD__);
                    die(json_encode([
                        'code'=>2,
                        'msg'=>'生成验证码失败',
                        'data'=>$servicerUserInfo->errors,
                    ]));
                }
            }
        }
    }

    /**
     * Updates an existing ServiceUser model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate()
    {
        $post = Yii::$app->request->post();
        $code = 0;
        $msg = '修改成功';
        $data = null;

        $id = $post['id'];
        $cash = $post['cash'];
        $role = $post['ServiceUser']['role'];
        $model = $this->findModel($id);

        if($id == 0) {
            Yii::info('缺少参数id', __METHOD__);
            die(json_encode(['code'=>3, 'msg'=>'缺少必要参数id']));
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            //判断业务员是否属于该服务商
            if($model->servicer_super_id != Yii::$app->user->identity['user_id'])
            {
                Yii::warning('没有权限', __METHOD__);
                die(json_encode(['code'=>4, 'msg'=>'没有权限']));
            }

            $auth = Yii::$app->getAuthManager();

            //判断本服务商下是否已经有业务经理了
            //拿到所有业务员
            $roles = Users::find()
                ->select(['user_id'])
                ->where(['servicer_super_id'=>Yii::$app->user->identity['user_id']])
                ->asArray()
                ->all();

            $notHasManager = true;

            foreach($roles as $v)
            {
                if(!empty(Yii::$app->authManager->getRolesByUser($v['user_id'])['service_manager']))
                {
                    $notHasManager = false;
                }
            }

            if($model->role == 'manager')
            {
                if($notHasManager === true)
                {
                    $auth->revokeAll($model->user_id);
                    $auth->assign($auth->getRole('service_manager'), $model->user_id);
                }
                else
                {
                    Yii::warning('已经有销售经理了 不能添加销售经理',__METHOD__);
                    die(json_encode(['code'=>5, 'msg'=>'该服务商下已有业务经理角色']));
                }

            }
            elseif($model->role == 'saleman' )
            {
                $auth->revokeAll($model->user_id);
                $auth->assign($auth->getRole('service_saleman'), $model->user_id);
            }
            else
            {
                throw new ForbiddenHttpException('对不起 你没有该权限');
            }
            Yii::info('修改资料成功 model = '. VarDumper::export($model), __METHOD__);
        }
        else {
            Yii::info('修改资料错误 errors = '. VarDumper::export($model->errors), __METHOD__);
            $code = 1;
            $msg = '修改资料错误';
            $data = json_encode($model->errors);
        }

        $totalCash = CashRecord::totalCash($id);
        if($cash > 0 && $cash < $totalCash) {
            $cashRecord = new CashRecord();
            $cashRecord->user_id = $id;
            $cashRecord->cash = -$cash;
            $cashRecord->created_time = DateTimeHelper::getFormatDateTime(time());
            $cashRecord->balance = $totalCash - $cash;

            if($cashRecord->save()) {
                Yii::info('提取余额成功 cashRecord = '. VarDumper::export($cashRecord), __METHOD__);
            }
            else {
                Yii::info('提取余额失败 errors = '. VarDumper::export($cashRecord->errors), __METHOD__);
                $code = 2;
                $msg = '提取余额失败';
                $data = json_encode($cashRecord->errors);
            }
        }

        $result = ['code'=>$code, 'msg'=>$msg, 'data'=>$data];
        Yii::info('result = '. VarDumper::export($result), __METHOD__);
        die(json_encode($result));
    }

    /**
     * Deletes an existing ServiceUser model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    //暂时不允许删除
//    public function actionDelete()
//    {
//        $id = Yii::$app->request->post('id');
//        $model = $this->findModel($id);
//        $model->servicer_super_id = 0;
//        $model->servicer_info_id = 0;
//        if($model->save()) {
//            die(json_encode(
//                [
//                    'code'=>0,
//                    'msg'=>'删除成功',
//                ]
//            ));
//        }
//        else {
//            die(json_encode(
//                [
//                    'code'=>1,
//                    'msg'=>'删除失败',
//                    'data'=>$model->errors,
//                ]
//            ));
//        }
//    }

    /**
     * Finds the ServiceUser model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return ServiceUser the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ServiceUser::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
