<?php

namespace backend\controllers;

use kartik\grid\EditableColumnAction;
use Yii;
use common\models\FashionGoods;
use backend\models\FashionGoodsSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * FashionGoodsController implements the CRUD actions for FashionGoods model.
 */
class FashionGoodsController extends Controller
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


    public function actions()
    {
        return ArrayHelper::merge(parent::actions(),[
            'editSortOrder' => [
                'class' => EditableColumnAction::className(),
                'modelClass' => FashionGoods::className(),
                'outputValue' => function($model, $attribute, $key, $index) {
                    return ''.$model->$attribute;
                },
                'outputMessage' => function($model, $attribute, $key, $index) {
                    return '';
                },
                'showModelErrors' => true,
                'errorOptions' => ['header' => '']
            ],
            'editName' => [
                'class' => EditableColumnAction::className(),
                'modelClass' => FashionGoods::className(),
                'outputValue' => function($model, $attribute, $key, $index) {
                    return ''.$model->$attribute;
                },
                'outputMessage' => function($model, $attribute, $key, $index) {
                    return '';
                },
                'showModelErrors' => true,
                'errorOptions' => ['header' => '']
            ],
            'editDesc' => [
                'class' => EditableColumnAction::className(),
                'modelClass' => FashionGoods::className(),
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
    /**
     * Lists all FashionGoods models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new FashionGoodsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single FashionGoods model.
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
     * Creates a new FashionGoods model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $fashionGoodsParams  = Yii::$app->request->post('FashionGoods', []);

        foreach($fashionGoodsParams as $key=> $param) {
            if(empty($param['goods_id']))
            {
                unset($fashionGoodsParams[$key]);
            }
        }

        $count = count($fashionGoodsParams);
        $models = [new FashionGoods()];
        for($i = 1; $i < $count; $i++) {
            $models[] = new FashionGoods();
        }

        if (FashionGoods::loadMultiple($models,Yii::$app->request->post()) && FashionGoods::validateMultiple($models)) {
            foreach($models as $model) {
                $model->save();
            }
            return $this->redirect(['index']);
        } else {
            $model = new FashionGoods();
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing FashionGoods model.
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
     * Deletes an existing FashionGoods model.
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
     * Finds the FashionGoods model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return FashionGoods the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = FashionGoods::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
