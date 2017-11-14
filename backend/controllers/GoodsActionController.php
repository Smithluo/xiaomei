<?php

namespace backend\controllers;

use Yii;
use common\models\GoodsAction;
use backend\models\GoodsActionSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * GoodsActionController implements the CRUD actions for GoodsAction model.
 */
class GoodsActionController extends Controller
{

    /**
     * Lists all GoodsAction models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new GoodsActionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Finds the GoodsAction model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return GoodsAction the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = GoodsAction::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
