<?php
namespace service\controllers;

use common\helper\CacheHelper;
use common\helper\NumberHelper;
use common\helper\ServicerDivideHelper;
use common\models\CashRecord;
use common\models\OrderInfo;
use common\models\ServiceUser;
use common\models\Users;
use service\models\AdminServiceLoginForm;
use service\models\ChangePasswordForm;
use service\models\UserRegion;
use Yii;
use yii\base\InvalidParamException;
use yii\caching\Cache;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use service\models\ServiceLoginForm;
use service\models\PasswordResetRequestForm;
use service\models\ResetPasswordForm;
use service\models\SignupForm;
use service\models\ContactForm;

/**
 * ServiceSiteController
 */
class ServiceSiteController extends XmController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['index', 'logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
//            'verbs' => [
//                'class' => VerbFilter::className(),
//                'actions' => [
//                    'logout' => ['post'],
//                ],
//            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {

        $servicers = [];
        $servicerCashAll = 0.00;
        //当前是一级服务商  身份判定与 cron/actionServicerCashAll 一致
        if(
            Yii::$app->user->identity['servicer_super_id'] == 0 ||
            Yii::$app->user->identity['user_id'] == 1150 ||
            (
                Yii::$app->user->identity['servicer_super_id'] == Yii::$app->user->identity['user_id'] &&
                Yii::$app->user->identity['servicer_user_id'] == Yii::$app->user->identity['user_id']
            )
        ) {
            //获取旗下所有二级服务商   二级服务商不显示服务商自己
//            $servicers = ServiceUser::findAll(['servicer_parent_id'=>Yii::$app->user->identity['user_id']]);
            $query = ServiceUser::find();
            $servicers = $query->select(['user_id', 'user_name', 'nickname', 'mobile_phone', 'servicer_code'=>'su.servicer_code'])
                ->joinWith('servicerUserInfo su')
                ->where(['servicer_super_id' => Yii::$app->user->identity['user_id']])
                ->andWhere(['!=', 'user_id', Yii::$app->user->identity['user_id']])
                ->all();

            $servicer_user_ids = [];
            if ($servicers) {
                foreach($servicers as $servicer) {
                    $servicer_user_ids[] = $servicer['user_id'];
                }
            }

//            $amounts = ServicerDivideHelper::getTotalDivideAmount($servicer_user_ids);
            //批量获取业务员的可提取总额
            if ($servicer_user_ids) {
                $amounts = CashRecord::totalCashList($servicer_user_ids);

                foreach($servicers as &$servicer) {
                    foreach($amounts as $amount) {
                        if($servicer->user_id == $amount['user_id']) {
                            $servicer->divide_amount = NumberHelper::price_format($amount['total_cash']);
                        }
                    }
                }
            }

            Yii::info('首页 servicers = '. VarDumper::export($servicers), __METHOD__);
        }

        return $this->render('index', [
            'servicers' => $servicers,
            'index' => 0,
        ]);
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        $this->layout = 'login.php';
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new ServiceLoginForm();
        if ($model->load(Yii::$app->request->post())) {
            if($model->login()) {
                return $this->goBack();
            } else {
                Yii::info('登录失败 errors = '. VarDumper::export($model->errors), __METHOD__);
            }
        }

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::info('退出登录', __METHOD__);
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * 修改密码
     * @return string
     */
    public function actionChangePassword() {
        $model = new ChangePasswordForm();
        if($model->load(Yii::$app->request->post())) {
            $user = Yii::$app->user->identity;
            if($user->validatePassword($model->password_old)) {
                if ($model->password == $model->password_repeat) {
                    $user->setPassword($model->password);
                    $user->ec_salt = null;
                    if($user->save()) {
                        $this->actionLogout();
                    }
                    else {
                        $model->addError('password', '服务商信息未完善，请联系');
                    }
                } else {
                    $model->addError('password_repeat', '两次输入的新密码不一致');
                }
            }
            else {
                $model->addError('password_old', '原始密码错误');
            }

            if($model->hasErrors()) {
                Yii::info('修改密码失败 errors = '. VarDumper::export($model->errors), __METHOD__);
            }
        }

        return $this->render('changePassword.php', [
            'model' => $model,
        ]);
    }

//    /**
//     * Displays contact page.
//     *
//     * @return mixed
//     */
//    public function actionContact()
//    {
//        $model = new ContactForm();
//        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
//            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
//                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
//            } else {
//                Yii::$app->session->setFlash('error', 'There was an error sending email.');
//            }
//
//            return $this->refresh();
//        } else {
//            return $this->render('contact', [
//                'model' => $model,
//            ]);
//        }
//    }
//
//    /**
//     * Displays about page.
//     *
//     * @return mixed
//     */
//    public function actionAbout()
//    {
//        return $this->render('about');
//    }
//
//    /**
//     * Signs user up.
//     *
//     * @return mixed
//     */
//    public function actionSignup()
//    {
//        $model = new SignupForm();
//        if ($model->load(Yii::$app->request->post())) {
//            if ($user = $model->signup()) {
//                if (Yii::$app->getUser()->login($user)) {
//                    return $this->goHome();
//                }
//            }
//        }
//
//        return $this->render('signup', [
//            'model' => $model,
//        ]);
//    }
//
//    /**
//     * Requests password reset.
//     *
//     * @return mixed
//     */
//    public function actionRequestPasswordReset()
//    {
//        $model = new PasswordResetRequestForm();
//        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
//            if ($model->sendEmail()) {
//                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
//
//                return $this->goHome();
//            } else {
//                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for email provided.');
//            }
//        }
//
//        return $this->render('requestPasswordResetToken', [
//            'model' => $model,
//        ]);
//    }
//
//    /**
//     * Resets password.
//     *
//     * @param string $token
//     * @return mixed
//     * @throws BadRequestHttpException
//     */
//    public function actionResetPassword($token)
//    {
//        try {
//            $model = new ResetPasswordForm($token);
//        } catch (InvalidParamException $e) {
//            throw new BadRequestHttpException($e->getMessage());
//        }
//
//        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
//            Yii::$app->session->setFlash('success', 'New password was saved.');
//
//            return $this->goHome();
//        }
//
//        return $this->render('resetPassword', [
//            'model' => $model,
//        ]);
//    }

    public function actionAdminLogin() {
        $this->layout = 'login.php';
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new AdminServiceLoginForm();
        if ($model->load(Yii::$app->request->post())) {
            if($model->login()) {
                return $this->goBack();
            } else {
                Yii::info('登录失败 errors = '. VarDumper::export($model->errors), __METHOD__);
            }
        }

        return $this->render('adminLogin', [
            'model' => $model,
        ]);
    }
}
