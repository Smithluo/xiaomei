<?php

namespace backend\controllers;

use backend\models\Goods;
use backend\models\Shipping;
use kartik\grid\EditableColumnAction;
use Yii;
use backend\models\GiftPkg;
use backend\models\GiftPkgSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * GiftPkgController implements the CRUD actions for GiftPkg model.
 */
class GiftPkgController extends Controller
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
     * 富文本插件处理图片上传
     * @return array
     */
    public function actions()
    {
        return ArrayHelper::merge(parent::actions(), [
            'upload' => [
                'class' => 'kucha\ueditor\UEditorAction',
                'config' => [
                    'imageUrlPrefix' => 'http://img.xiaomei360.com',
                    'imagePathFormat' => '/image/{yyyy}{mm}{dd}/{time}{rand:6}',
                    'imageRoot' => Yii::getAlias('@mRoot').'/data/attached',
                ],
            ],
            'edit-value' => [
                'class' => EditableColumnAction::className(),
                'modelClass' => GiftPkg::className(),
                'outputValue' => function($model, $attribute, $key, $index) {
                    return ''. $model->$attribute;
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
     * Lists all GiftPkg models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new GiftPkgSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'shippingList' => Shipping::getShippingCodeNameMap(),
            'isOnSaleMap' => GiftPkg::$isOnSaleMap,
        ]);
    }

    /**
     * Displays a single GiftPkg model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = GiftPkg::find()
            ->joinWith('giftPkgGoods')
            ->where([GiftPkg::tableName().'.id' => $id])
            ->one();

        $giftGoodsIdList = [];
        if (!empty($model->giftPkgGoods)) {
            $giftGoodsIdList = ArrayHelper::getColumn($model->giftPkgGoods, 'goods_id');
        }
        $model->giftGoodsList = $giftGoodsIdList;

        return $this->render('view', [
            'model' => $model,
            'shippingList' => Shipping::getShippingCodeNameMap(),
            'isOnSaleMap' => GiftPkg::$isOnSaleMap,
            'goodsList' => Goods::getUnDeleteGoodsMap(),
        ]);

    }

    /**
     * Creates a new GiftPkg model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new GiftPkg();
        $model->setScenario('insert');

        $post = Yii::$app->request->post();
        if ($model->load($post)) {
            if (!empty($post['GiftPkg']['giftGoodsList']) && $model->safeSave($post['GiftPkg']['giftGoodsList'])) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        if (empty($model->shipping_code)) {
            $model->shipping_code = Yii::$app->params['default_shipping_code']; //  默认到付
        }
        if (empty($model->is_on_sale)) {
            $model->shipping_code = GiftPkg::NOT_ON_SALE; //  默认不上架
        }

        $shippingList = Shipping::getShippingCodeNameMap();
        return $this->render('create', [
            'model' => $model,
            'shippingList' => $shippingList,
            'isOnSaleMap' => GiftPkg::$isOnSaleMap,
            'goodsList' => Goods::getUnDeleteGoodsMap(),
        ]);
    }

    /**
     * Updates an existing GiftPkg model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = GiftPkg::find()
            ->joinWith([
                'giftPkgGoods',
                'giftPkgGoods.goods'
            ])->where([GiftPkg::tableName().'.id' => $id])
            ->one();

        $model->setScenario('update');

        $giftGoodsIdList = [];
        if (!empty($model->giftPkgGoods)) {
            $giftGoodsIdList = ArrayHelper::getColumn($model->giftPkgGoods, 'goods_id');
        }
        $model->giftGoodsList = array_values($giftGoodsIdList);

        $post = Yii::$app->request->post();
        if ($model->load($post)) {
            if (!empty($post['GiftPkg']['giftGoodsList'])) {
                if ($model->safeSave($post['GiftPkg']['giftGoodsList'])) {
                    Yii::$app->session->setFlash('success', '礼包互动更新成功');
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }
        }

        $goodsList = Goods::getUnDeleteGoodsMap();
        $giftGoodsList = [];
        if (!empty($model->giftPkgGoods)) {
            foreach ($model->giftPkgGoods as $goods) {
                if (!empty($goodsList[$goods->goods_id])) {
                    $giftGoodsList[$goods->goods_id] = $goodsList[$goods->goods_id];
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
            'shippingList' => Shipping::getShippingCodeNameMap(),
            'isOnSaleMap' => GiftPkg::$isOnSaleMap,
            'goodsList' => $goodsList,
            'giftGoodsList' => $giftGoodsList,
        ]);
    }

    /**
     * Deletes an existing GiftPkg model.
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
     * 上下架切换
     */
    public function actionToggle($id)
    {
        $model = $this->findModel($id);
        $model->is_on_sale = $model->is_on_sale ? GiftPkg::NOT_ON_SALE : GiftPkg::IS_ON_SALE;
        $model->save();
        return $this->redirect(['index']);
    }

    /**
     * Finds the GiftPkg model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return GiftPkg the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = GiftPkg::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
