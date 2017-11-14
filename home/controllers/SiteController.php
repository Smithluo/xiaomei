<?php
namespace home\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;

/**
 * Site controller
 */
class SiteController extends Controller
{

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
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
        $this->layout = false;
        return $this->render('index', [
            'pcSiteUrl' => Yii::$app->params['pcHost']
        ]);
    }

    public function actionBrand()
    {
        $this->layout = false;
        return $this->render('brand', [
            'pcSiteUrl' => Yii::$app->params['pcHost']
        ]);
    }

    //  美妆店
    public function actionStore()
    {
        $this->layout = false;
        return $this->render('store', [
            'pcSiteUrl' => Yii::$app->params['pcHost']
        ]);
    }
}
