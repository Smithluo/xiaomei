<?php

namespace backend\controllers;


use Yii;
use backend\models\GoodsPkg;
use common\models\GoodsPkgSearch;
use common\helper\TextHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * GoodsPkgController implements the CRUD actions for GoodsPkg model.
 */
class GoodsPkgController extends Controller
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
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all GoodsPkg models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new GoodsPkgSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single GoodsPkg model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new GoodsPkg model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new GoodsPkg();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->allow_goods_list) {
                $model->setAttribute('allow_goods_list', TextHelper::replaceDelimter($model->allow_goods_list));
            }

            if ($model->deny_goods_list) {
                $model->setAttribute('deny_goods_list', TextHelper::replaceDelimter($model->deny_goods_list));
            }
            $model->save();
            return $this->redirect(['view', 'id' => $model->pkg_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing GoodsPkg model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->allow_goods_list) {
                $model->setAttribute('allow_goods_list', TextHelper::replaceDelimter($model->allow_goods_list));
            }

            if ($model->deny_goods_list) {
                $model->setAttribute('deny_goods_list', TextHelper::replaceDelimter($model->deny_goods_list));
            }
            $model->save();
            return $this->redirect(['view', 'id' => $model->pkg_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing GoodsPkg model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the GoodsPkg model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return GoodsPkg the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = GoodsPkg::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
