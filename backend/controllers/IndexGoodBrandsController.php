<?php

namespace backend\controllers;

use kartik\grid\EditableColumnAction;
use service\models\Brand;
use Yii;
use common\models\IndexGoodBrands;
use common\models\IndexGoodBrandsSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * IndexGoodBrandsController implements the CRUD actions for IndexGoodBrands model.
 */
class IndexGoodBrandsController extends Controller
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
     * Lists all IndexGoodBrands models.
     * @return mixed
     */
    public function actions()
    {
        return ArrayHelper::merge(parent::actions(),[
            'editSortOrder' => [
                'class' => EditableColumnAction::className(),
                'modelClass' => IndexGoodBrands::className(),
                'outputValue' => function($model, $attribute, $key, $index) {
                    return ''.$model->$attribute;
                },
                'outputMessage' => function($model, $attribute, $key, $index) {
                    return '';
                },
                'showModelErrors' => true,
                'errorOptions' => ['header' => '']
            ],
            'editTitle' => [
                'class' => EditableColumnAction::className(),
                'modelClass' => IndexGoodBrands::className(),
                'outputValue' => function($model, $attribute, $key, $index) {
                    return ''.$model->$attribute;
                },
                'outputMessage' => function($model, $attribute, $key, $index) {
                    return '';
                },
                'showModelErrors' => true,
                'errorOptions' => ['header' => '']
            ],
        ]);
    }

    public function actionIndex()
    {
        $searchModel = new IndexGoodBrandsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single IndexGoodBrands model.
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
     * Creates a new IndexGoodBrands model.
     * If creation is successful, the browser will be redirected to the   'view' page.
     * @return mixed
     */
    public function actionCreate()
    {

        $model =new IndexGoodBrands();

        $model->setScenario('insert');
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', '操作成功');
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing IndexGoodBrands model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->setScenario('update');
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', '操作成功');
            return $this->redirect(['update', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing IndexGoodBrands model.
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
     * Finds the IndexGoodBrands model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return IndexGoodBrands the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = IndexGoodBrands::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
