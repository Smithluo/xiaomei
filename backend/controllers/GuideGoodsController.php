<?php

namespace backend\controllers;

use kartik\grid\EditableColumnAction;
use Yii;
use common\models\GuideGoods;
use common\models\GuideGoodsSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * GuideGoodsController implements the CRUD actions for GuideGoods model.
 */
class GuideGoodsController extends Controller
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
                'modelClass' => GuideGoods::className(),
                'outputValue' => function($model, $attribute, $key, $index) {
                    return ''.$model->$attribute;
                },
                'outputMessage' => function($model, $attribute, $key, $index) {
                    return '';
                },
                'showModelErrors' => true,
                'errorOptions' => ['header' => '']
           ],
            'editType' => [
                'class' => EditableColumnAction::className(),
                'modelClass' => GuideGoods::className(),
                'outputValue' => function($model, $attribute, $key, $index) {
                    return ''.GuideGoods::TypeMap()[$model->$attribute];
                },
                'outputMessage' => function($model, $attribute, $key, $index) {
                    return '';
                },
                'showModelErrors' => true,
                'errorOptions' => ['header' => '']
            ],
            'editGoodsID' => [
                'class' => EditableColumnAction::className(),
                'modelClass' => GuideGoods::className(),
                'outputValue' => function($model, $attribute, $key, $index) {
                    $goods = GuideGoods::Goods();
                    $goodsId = $model->goods_id;
                    $result = ''.$goods[$goodsId];
                    return $result;
                },
                'outputMessage' => function($model, $attribute, $key, $index) {
                    return '';
                },
                'showModelErrors' => true,
                'errorOptions' => ['header' => '']
            ]
        ]);
    }
    /**
     * Lists all GuideGoods models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new GuideGoodsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single GuideGoods model.
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
     * Creates a new GuideGoods model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {

        $guideGoodsParams  = Yii::$app->request->post('GuideGoods', []);

        foreach($guideGoodsParams as $key=> $param) {
            if(empty($param['goods_id']))
            {
                unset($guideGoodsParams[$key]);
            }
        }

        $count = count($guideGoodsParams);
        $models = [new GuideGoods()];
        for($i = 1; $i < $count; $i++) {
            $models[] = new GuideGoods();
        }

        if (GuideGoods::loadMultiple($models,Yii::$app->request->post()) && GuideGoods::validateMultiple($models)) {
            foreach($models as $model) {
                $model->save();
            }
            Yii::$app->session->setFlash('success', '创建成功');
            return $this->redirect(['index']);
        } else {
            $model = new GuideGoods();
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing GuideGoods model.
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
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing GuideGoods model.
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
     * Finds the GuideGoods model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return GuideGoods the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = GuideGoods::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
