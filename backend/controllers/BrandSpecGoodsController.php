<?php

namespace backend\controllers;

use backend\models\Goods;
use common\models\BrandSpecGoodsCat;
use kartik\grid\EditableColumnAction;
use Yii;
use common\models\BrandSpecGoods;
use common\models\BrandSpecGoodsSearch;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * BrandSpecGoodsController implements the CRUD actions for BrandSpecGoods model.
 */
class BrandSpecGoodsController extends Controller
{
    //加入图片上传能力
    public function actions()
    {
        $actionValue = [
            'class' => EditableColumnAction::className(),
            'modelClass' => BrandSpecGoods::className(),
            'outputValue' => function($model, $attribute, $key, $index) {
                if ($attribute == 'goods_id') {
                    if (empty(Goods::getGoodsMap()[$model->$attribute])) {
                        return null;
                    }
                    return Goods::getGoodsMap()[$model->$attribute];
                }
                return $model->$attribute;
            },
            'outputMessage' => function($model, $attribute, $key, $index) {
                if ($model->hasErrors()) {
                    $errors = $model->getFirstError($attribute);
                    return $errors;
                }
                return '';
            },
        ];
        return ArrayHelper::merge(parent::actions(), [
            'edit-value' => $actionValue,
        ]);
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
     * Lists all BrandSpecGoods models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BrandSpecGoodsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single BrandSpecGoods model.
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
     * Creates a new BrandSpecGoods model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($catId = 0)
    {
        $model = new BrandSpecGoods();

        if ($catId != 0) {
            $model->spec_goods_cat_id = $catId;
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $cat = BrandSpecGoodsCat::find()->where([
                'id' => $catId,
            ])->one();
            return $this->redirect(Url::to([
                '/brand/view',
                'id' => $cat['brand_id'],
            ]));
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing BrandSpecGoods model.
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
     * Deletes an existing BrandSpecGoods model.
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
     * Finds the BrandSpecGoods model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return BrandSpecGoods the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BrandSpecGoods::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
