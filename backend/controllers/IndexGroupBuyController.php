<?php

namespace backend\controllers;

use kartik\grid\EditableColumnAction;
use Yii;
use common\models\IndexGroupBuy;
use common\models\IndexGroupBuySearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * IndexGroupBuyController implements the CRUD actions for IndexGroupBuy model.
 */
class IndexGroupBuyController extends Controller
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
     * Lists all IndexGroupBuy models.
     * @return mixed
     */

    public function actions()
    {
        return ArrayHelper::merge(parent::actions(),[
            'editSortOrder' => [
                'class' => EditableColumnAction::className(),
                'modelClass' => IndexGroupBuy::className(),
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
                'modelClass' => IndexGroupBuy::className(),
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
        $searchModel = new IndexGroupBuySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single IndexGroupBuy model.
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
     * Creates a new IndexGroupBuy model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $indexGroupBuyParams  = Yii::$app->request->post('IndexGroupBuy', []);

        foreach($indexGroupBuyParams as $key=> $param) {
            if(empty($param['activity_id']))
            {
                unset($indexGroupBuyParams[$key]);
            }
        }

        $count = count($indexGroupBuyParams);
        $models = [new IndexGroupBuy()];
        for($i = 1; $i < $count; $i++) {
            $models[] = new IndexGroupBuy();
        }

        if (IndexGroupBuy::loadMultiple($models,Yii::$app->request->post()) && IndexGroupBuy::validateMultiple($models)) {
            foreach($models as $model) {
                $model->save();
            }
            return $this->redirect(['index']);
        } else {
            $model =new IndexGroupBuy();
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing IndexGroupBuy model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

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
     * Deletes an existing IndexGroupBuy model.
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
     * Finds the IndexGroupBuy model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return IndexGroupBuy the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = IndexGroupBuy::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
