<?php

namespace brand\controllers;

use brand\models\ResetPasswordForm;
use common\models\BankInfo;
use common\models\BrandAdmin;
use Yii;
use common\models\Users;
use common\models\UsersSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * UserController implements the CRUD actions for Users model.
 */
class UserController extends Controller
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

                ],
            ],
        ];
    }

    /**
     * Lists all Users models.
     * @return mixed
     */
//    public function actionIndex()
//    {
//        $this->redirect(['userinfo']);
//    }

    /**
     * Displays a single Users model.
     * @param string $id
     * @return mixed
     */
    public function actionUserinfo()
    {
        $user = Users::find()->where(['user_id' => Yii::$app->user->identity->id])->one();
        $bank_info = BankInfo::find()->where(['id' => $user->bank_info_id])->one();
        $brand_admin = BrandAdmin::find()->where(['id' => $user->brand_admin_id])->one();
        return $this->render('userinfo', [
            'model' => $user,
            'bank_info' => $bank_info,
            'brand_admin' => $brand_admin,
            'r_version' => \Yii::$app->params['r_version'],
        ]);
    }


    /**
     * Updates an existing Users model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionModifypwd()
    {
        $model = $this->findModel(Yii::$app->user->identity->id);
        $reset_pwd_form = new ResetPasswordForm();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['modifypwd', 'id' => $model->user_id]);
        } else {
            return $this->render('modifypwd', [
                'model' => $model,
                'reset_pwd_form' => $reset_pwd_form,
                'r_version' => \Yii::$app->params['r_version'],
            ]);
        }
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
