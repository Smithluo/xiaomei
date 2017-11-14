<?php

namespace backend\controllers;

use backend\models\GiftPkg;
use backend\models\Goods;
use kartik\grid\EditableColumnAction;
use Yii;
use backend\models\GiftPkgGoods;
use backend\models\GiftPkgGoodsSearch;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * GiftPkgGoodsController implements the CRUD actions for GiftPkgGoods model.
 */
class GiftPkgGoodsController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['editGoodsNum'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
//                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return ArrayHelper::merge(
            parent::actions(),
            [
                'editGoodsNum' => [
                    'class' => EditableColumnAction::className(),
                    'modelClass' => GiftPkgGoods::className(),
                    'outputValue' => function($model, $attribute, $key, $index) {
                        return ''.$model->$attribute;
                    },
                    'outputMessage' => function($model, $attribute, $key, $index) {
                        return '';
                    },
                    'showModelErrors' => true,
                    'errorOptions' => ['header' => '']
                ],
            ]
        );
    }

    /**
     * Lists all GiftPkgGoods models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new GiftPkgGoodsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'giftPkgList' => GiftPkg::idNameMap(),
            'goodsList' => Goods::getALLGoodsMap(),
        ]);
    }

    /**
     * Displays a single GiftPkgGoods model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        return $this->render('view', [
            'model' => $model,
            'giftPkgList' => GiftPkg::idNameMap(),
            'goodsList' => Goods::getALLGoodsMap(),
        ]);
    }

    /**
     * Creates a new GiftPkgGoods model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new GiftPkgGoods();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'giftPkgList' => GiftPkg::idNameMap(),
                'goodsList' => Goods::getUnDeleteGoodsMap(),
            ]);
        }
    }

    /**
     * Updates an existing GiftPkgGoods model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', '更新成功');
            } else {
                Yii::$app->session->setFlash('error', '更新失败，注意看提醒');
            }
        }

        return $this->render('update', [
            'model' => $model,
            'giftPkgList' => GiftPkg::idNameMap(),
            'goodsList' => Goods::getALLGoodsMap(),
        ]);
    }

    /**
     * Deletes an existing GiftPkgGoods model.
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
     * Finds the GiftPkgGoods model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return GiftPkgGoods the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = GiftPkgGoods::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
