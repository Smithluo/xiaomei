<?php

namespace backend\controllers;

use Yii;
use backend\models\IndexFullCutGoods;
use backend\models\IndexFullCutGoodsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\Goods;

/**
 * IndexFullCutGoodsController implements the CRUD actions for IndexFullCutGoods model.
 */
class IndexFullCutGoodsController extends Controller
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
     * Lists all IndexFullCutGoods models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new IndexFullCutGoodsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $goodsList = Goods::getUnDeleteGoodsMap();  //  参与活动的商品有 商品参与活动 也有整个品牌参与活动

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'goodsList' => $goodsList,
        ]);
    }

    /**
     * Displays a single IndexFullCutGoods model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        $goodsName = $goodsName = Goods::getGoodsName($model->goods_id);
        $goodsList = IndexFullCutGoods::getFullCutGoodsList();

        return $this->render('view', [
            'model' => $model,
            'goodsName' => $goodsName[$model->goods_id],
            'goodsList' => $goodsList,
        ]);
    }

    /**
     * Creates a new IndexFullCutGoods model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new IndexFullCutGoods();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            $goodsList = IndexFullCutGoods::getFullCutGoodsList();

            return $this->render('create', [
                'model' => $model,
                'goodsList' => $goodsList,
            ]);
        }
    }

    /**
     * Updates an existing IndexFullCutGoods model.
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
            $goodsList = IndexFullCutGoods::getFullCutGoodsList();
            $goodsName = Goods::getGoodsName($model->goods_id);

            return $this->render('update', [
                'model' => $model,
                'goodsList' => $goodsList,
                'goodsName' => $goodsName[$model->goods_id],
            ]);
        }
    }

    /**
     * Deletes an existing IndexFullCutGoods model.
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
     * Finds the IndexFullCutGoods model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return IndexFullCutGoods the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = IndexFullCutGoods::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }



}
