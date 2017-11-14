<?php
namespace order\controllers;

use order\models\OrderGroupImportForm;
use order\models\OrderGroupSearch;
use order\models\OrderLoginForm;
use Yii;
use yii\helpers\VarDumper;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\UploadedFile;

/**
 * OrderSiteController
 */
class OrderSiteController extends Controller
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
        $importForm = new OrderGroupImportForm();

        return $this->render(
            'index',
            ['importForm' => $importForm]
        );
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

        $model = new OrderLoginForm();
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
     * 订单列表
     */
    public function actionList()
    {
        $searchModel = new OrderGroupSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $importForm = new OrderGroupImportForm();

        return $this->render('list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'importForm' => $importForm,
        ]);
        return $this->render('list');
    }


    public function actionImport() {
        ini_set('max_execution_time', 120);
        ini_set('memory_limit', '1G');

        $importForm = new OrderGroupImportForm();

        if (Yii::$app->request->isPost) {
            $importForm->file = UploadedFile::getInstance($importForm, 'file');
            $importForm->import();
        }

        return $this->redirect(['index']);
    }

}
