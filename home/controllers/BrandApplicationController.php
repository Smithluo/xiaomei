<?php

namespace home\controllers;

use Yii;
use home\models\BrandApplication;
use home\models\BrandApplicationSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * BrandApplicationController implements the CRUD actions for BrandApplication model.
 */
class BrandApplicationController extends Controller
{
    public $enableCsrfValidation = false;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'create' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Creates a new BrandApplication model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new BrandApplication();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return json_encode([
                'code' => 0,
                'msg' => '提交成功',
            ]);
        } else {
            return json_encode([
                'code' => 1,
                'msg' => $model->getFirstError(array_keys($model->errors)[0]),
            ]);
        }
    }

    /**
     * Finds the BrandApplication model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return BrandApplication the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BrandApplication::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
