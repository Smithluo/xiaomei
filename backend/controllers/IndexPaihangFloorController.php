<?php

namespace backend\controllers;

use kartik\grid\EditableColumnAction;
use Yii;
use common\models\IndexPaihangFloor;
use backend\models\IndexPaihangFloorSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * IndexPaihangFloorController implements the CRUD actions for IndexPaihangFloor model.
 */
class IndexPaihangFloorController extends Controller
{

    public function actions()
    {
        $actionValue = [
            'class' => EditableColumnAction::className(),
            'modelClass' => IndexPaihangFloor::className(),
            'outputValue' => function($model, $attribute, $key, $index) {
                return ''. $model->$attribute;
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
                'edit-sort' => $actionValue,
                'edit-title' => $actionValue,
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
     * Lists all IndexPaihangFloor models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new IndexPaihangFloorSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single IndexPaihangFloor model.
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
     * Creates a new IndexPaihangFloor model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new IndexPaihangFloor();
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
     * Updates an existing IndexPaihangFloor model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->setScenario('update');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['update', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing IndexPaihangFloor model.
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
     * Finds the IndexPaihangFloor model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return IndexPaihangFloor the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = IndexPaihangFloor::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
