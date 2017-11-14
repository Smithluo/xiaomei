<?php

namespace backend\controllers;

use backend\models\Goods;
use Yii;
use backend\models\Spu;
use common\models\SpuSeach;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * SpuController implements the CRUD actions for Spu model.
 */
class SpuController extends Controller
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
//                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Spu models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SpuSeach();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Spu model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $skuGoodsList = Goods::find()
            ->select(['goods_id', 'goods_name', 'is_on_sale', 'sku_size'])
            ->where(['spu_id' => $id])
            ->all();

        return $this->render('view', [
            'model' => $this->findModel($id),
            'skuGoodsList' => $skuGoodsList,
            'isOnSaleMap' => Goods::$is_on_sale_map,
        ]);
    }

    /**
     * Creates a new Spu model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Spu();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Spu model.
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
            $skuGoodsList = Goods::find()
                ->select(['goods_id', 'goods_name', 'is_on_sale', 'sku_size'])
                ->where(['spu_id' => $id])
                ->all();

            return $this->render('update', [
                'model' => $model,
                'skuGoodsList' => $skuGoodsList,
                'isOnSaleMap' => Goods::$is_on_sale_map,
            ]);
        }
    }

    /**
     * 应该是指 o_goods.spu_id 为外键，避免删除在用的spu，暂时注释掉删除action
     * Deletes an existing Spu model.
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
     * Finds the Spu model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Spu the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Spu::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * 导出SPU
     */
    public function actionExport()
    {
        $spuList = Spu::find()->all();
        \moonland\phpexcel\Excel::export([
            'format' => 'Excel5',
            'fileName' => date('Ymd').'SPU数据',
            'models' => $spuList,
            'columns' => [
                'id',
                'name',
            ], //   without header working, because the header will be get label from attribute label.
            'headers' => [
                'id' => 'spu_id',
                'name' => 'SPU名称',
            ],
        ]);
    }
}
