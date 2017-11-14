<?php

namespace backend\controllers;

use backend\models\Brand;
use Yii;
use backend\models\KnowledgeShowBrand;
use backend\models\KnowledgeShowBrandSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * KnowledgeShowController implements the CRUD actions for KnowledgeShowBrand model.
 */
class KnowledgeShowBrandController extends Controller
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
     * Lists all KnowledgeShowBrand models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new KnowledgeShowBrandSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $brandMap = Brand::getBrandIdNameMap();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'brandMap' => $brandMap,
            'platformMap' =>  KnowledgeShowBrand::$platformMap,
        ]);
    }

    /**
     * Displays a single KnowledgeShowBrand model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
            'platformMap' =>  KnowledgeShowBrand::$platformMap,
        ]);
    }

    /**
     * Creates a new KnowledgeShowBrand model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new KnowledgeShowBrand();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            $brandMap = Brand::getBrandIdNameMap();

            return $this->render('create', [
                'model' => $model,
                'brandMap' => $brandMap,
                'platformMap' =>  KnowledgeShowBrand::$platformMap,
            ]);
        }
    }

    /**
     * Updates an existing KnowledgeShowBrand model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            $brandMap = Brand::getBrandIdNameMap();

            return $this->render('update', [
                'model' => $model,
                'brandMap' => $brandMap,
                'platformMap' =>  KnowledgeShowBrand::$platformMap,
            ]);
        }
    }

    /**
     * Deletes an existing KnowledgeShowBrand model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Finds the KnowledgeShowBrand model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return KnowledgeShowBrand the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = KnowledgeShowBrand::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
