<?php

namespace backend\controllers;

use backend\models\Goods;
use kartik\grid\EditableColumnAction;
use Yii;
use common\models\RegisterDoneGoods;
use common\models\RegisterDoneGoodsSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * RegisterDoneGoodsController implements the CRUD actions for RegisterDoneGoods model.
 */
class RegisterDoneGoodsController extends Controller
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

            'editIsShow' => [
                'class' => EditableColumnAction::className(),
                'modelClass' => RegisterDoneGoods::className(),
                'outputValue' => function($model, $attribute, $key, $index) {
                    return ''.($model->$attribute == 0) ? '不显示' : '显示';
                },
                'outputMessage' => function($model, $attribute, $key, $index) {
                    return '';
                },
                'showModelErrors' => true,
                'errorOptions' => ['header' => '']
            ],
            'editSortOrder' => [
                'class' => EditableColumnAction::className(),
                'modelClass' => RegisterDoneGoods::className(),
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
     * Lists all RegisterDoneGoods models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RegisterDoneGoodsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single RegisterDoneGoods model.
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
     * Creates a new RegisterDoneGoods model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $registerDoneParams  = Yii::$app->request->post('RegisterDoneGoods', []);

        foreach($registerDoneParams as $key=> $param) {
            if(empty($param['goods_id']))
            {
                unset($registerDoneParams[$key]);
            }
        }

        $count = count($registerDoneParams);
        $models = [new RegisterDoneGoods()];
        for($i = 1; $i < $count; $i++) {
            $models[] = new RegisterDoneGoods();
        }

        if (RegisterDoneGoods::loadMultiple($models,Yii::$app->request->post()) && RegisterDoneGoods::validateMultiple($models)) {
            foreach($models as $model) {
                $model->save();
            }
            Yii::$app->session->setFlash('success', '操作成功');
            return $this->redirect(['index']);
        } else {

            $model = new RegisterDoneGoods();
            $goodsList = Goods::getGoodsMap();
            return $this->render('create', [
                'model' => $model,
                'goodsList' => $goodsList,
            ]);
        }
    }

    /**
     * Updates an existing RegisterDoneGoods model.
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
            $goodsList = Goods::getGoodsMap();
            return $this->render('update', [
                'model' => $model,
                'goodsList' => $goodsList,
            ]);
        }
    }

    /**
     * Deletes an existing RegisterDoneGoods model.
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
     * Finds the RegisterDoneGoods model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return RegisterDoneGoods the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = RegisterDoneGoods::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
