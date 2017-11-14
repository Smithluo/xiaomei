<?php

namespace backend\controllers;

use backend\models\IndexFullGiftGoodsSearch;
use common\models\IndexFullGiftGoods;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\Goods;

/**
 * IndexFullGiftGoodsController implements the CRUD actions for IndexFullGiftGoods model.
 */
class IndexFullGiftGoodsController extends Controller
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
     * Lists all IndexFullGiftGoods models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new IndexFullGiftGoodsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $goodsList = Goods::getUnDeleteGoodsMap();  //  参与活动的商品有 商品参与活动 也有整个品牌参与活动

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'goodsList' => $goodsList,
        ]);
    }

    /**
     * Displays a single IndexFullGiftGoods model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        $goodsName = $goodsName = Goods::getGoodsName($model->goods_id);

        return $this->render('view', [
            'model' => $model,
            'goodsName' => $goodsName[$model->goods_id],
        ]);
    }

    /**
     * Creates a new IndexFullGiftGoods model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new IndexFullGiftGoods();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing IndexFullGiftGoods model.
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
            $goodsName = Goods::getGoodsName($model->goods_id);

            return $this->render('update', [
                'model' => $model,
                'goodsName' => $goodsName[$model->goods_id],
            ]);
        }
    }

    /**
     * Deletes an existing IndexFullGiftGoods model.
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
     * Finds the IndexFullGiftGoods model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return IndexFullGiftGoods the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = IndexFullGiftGoods::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }



}
