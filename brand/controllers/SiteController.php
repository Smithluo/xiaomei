<?php
namespace brand\controllers;

use brand\models\BrandLoginForm;
use common\models\BrandUser;
use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use brand\models\PasswordResetRequestForm;
use brand\models\ResetPasswordForm;
use brand\models\ChangePasswordForm;
use brand\models\ContactForm;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'change-password'],
                'rules' => [
                    [
                        'actions' => ['logout', 'change-password'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post', 'get'],
                ],
            ],
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
        if (Yii::$app->user->isGuest) {
            $this->redirect(['site/login']);
        } else {
            $this->redirect(['order/index']);
        }
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new BrandLoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();   //    '/index.php?r=order/index'
        } else {
            $this->layout = 'login';
            $code = Yii::$app->request->isPost ? 1 : 0;

            return $this->render('login', [
                'model' => $model,
                'code' => $code,
                'err_msg' => '用户名不存在或密码错误',
                'r_version' => \Yii::$app->params['r_version'],
            ]);
        }
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending email.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    /*public function actionSignup()
    {
        $model = new BrandSignupForm();
        if ($model->load(Yii::$app->request->post())) {

            if ($user = $model->signup()) {
                return json_encode([
                    'code' => 0,
                    'msg' => '品牌商账号注册成功',
                ]);
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            } else {
                return json_encode([
                    'code' => 1,
                    'msg' => '品牌商账号注册失败',
                ]);
            }
        }

        return $this->render('signup', [
            'model' => $model,
            'labels' => (new BrandUser)->attributeLabels(),
        ]);
    }*/

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for email provided.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password was saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    /**
     * 修改用户密码  真实在用
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
                        $model->addError('password', '设置新密码失败');
                    }
                } else {
                    $model->addError('password_repeat', '两次输入的新密码不一致');
                }
            }
            else {
                $model->addError('password_old', '密码错误');
            }
        }

        return $this->render('changePassword.php', [
            'model' => $model,
            'r_version' => \Yii::$app->params['r_version'],
        ]);
    }

    public function actionError()
    {

        if($error=Yii::app()->errorHandler->error)
        {
            if(Yii::app()->request->isAjaxRequest) {
                echo $error['message'];
            }
            else {
                $this->render('error', $error);
            }

        }
    }
}
