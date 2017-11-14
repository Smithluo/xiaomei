<?php

namespace backend\controllers;

use kartik\grid\EditableColumnAction;
use Yii;
use backend\models\IndexZhifaYouxuan;
use backend\models\IndexZhifaYouxuanSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * IndexZhifaYouxuanController implements the CRUD actions for IndexZhifaYouxuan model.
 */
class IndexZhifaYouxuanController extends Controller
{
    public function actions()
    {
        $actionValue = [
            'class' => EditableColumnAction::className(),
            'modelClass' => IndexZhifaYouxuan::className(),
            'outputValue' => function($model, $attribute, $key, $index) {
                return ''.$model->$attribute;
            },
            'outputMessage' => function($model, $attribute, $key, $index) {
                if ($model->hasErrors()) {
                    $errors = $model->getFirstError($attribute);
                    return $errors;
                }
                return '';
            },
            'showModelErrors' => true,
            'errorOptions' => ['header' => '错误'],
        ];
        return ArrayHelper::merge(
            parent::actions(),
            [
                'edit-url' => $actionValue,
                'edit-sort' => $actionValue,
            ]
        );
    }

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
     * Lists all IndexZhifaYouxuan models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new IndexZhifaYouxuanSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single IndexZhifaYouxuan model.
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
     * Creates a new IndexZhifaYouxuan model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new IndexZhifaYouxuan();
        $model->setScenario('insert');
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing IndexZhifaYouxuan model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->setScenario('update');
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing IndexZhifaYouxuan model.
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
     * Finds the IndexZhifaYouxuan model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return IndexZhifaYouxuan the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = IndexZhifaYouxuan::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
