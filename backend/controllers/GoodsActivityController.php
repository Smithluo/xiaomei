<?php

namespace backend\controllers;

use backend\models\Shipping;
use backend\models\GoodsActivity;
use backend\models\Goods;
use common\helper\TextHelper;
use common\models\GoodsActivitySearch;
use kartik\grid\EditableColumnAction;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * GoodsActivityController implements the CRUD actions for GoodsActivity model.
 */
class GoodsActivityController extends Controller
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
        $editValueAction = [
            'class' => EditableColumnAction::className(),
            'modelClass' => GoodsActivity::className(),
            'outputValue' => function($model, $attribute, $key, $index) {
                return ''.$model->$attribute;
            },
            'outputMessage' => function($model, $attribute, $key, $index) {
                return '';
            },
            'showModelErrors' => true,
            'errorOptions' => ['header' => '']
        ];
        return ArrayHelper::merge(parent::actions(), [
            'upload' => [
                'class' => 'kucha\ueditor\UEditorAction',
                'config' => [
                    'imageUrlPrefix' => 'http://img.xiaomei360.com',
                    'imagePathFormat' => '/goods_activity/{yyyy}{mm}{dd}/{time}{rand:6}',
                    'imageRoot' => Yii::getAlias('@imgRoot').'',
                ],
            ],
            'edit-value' => $editValueAction,
            'editStartNum' => [
                'class' => EditableColumnAction::className(),
                'modelClass' => Goods::className(),
                'outputValue' => function($model, $attribute, $key, $index) {
                    $model->setScenario('update');
                    $model->checkStartNum();
                    if ($model->hasErrors()) {
                        return false;
                    } else {
                        return ''.(int)$model->$attribute;
                    }

                },
                'outputMessage' => function($model, $attribute, $key, $index) {
                    $model->checkStartNum();
                    if ($model->hasErrors()) {
                        return TextHelper::getErrorsMsg($model->errors);
                    } else {
                        return '';
                    }
                },
                'showModelErrors' => true,
                'errorMessages' => ['invalidEditable', 'invalidModel', 'editableException', 'saveException'],
                'errorOptions' => ['header' => '']
            ],
        ]);
    }

    /**
     * Lists all GoodsActivity models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new GoodsActivitySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
//        $shippingCodeNameMap = Shipping::getshippingCodeNameMap();
        $shippingCodeNameMap = Shipping::getShippingCodeDescMap();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'shippingCodeNameMap' => $shippingCodeNameMap,
        ]);
    }

    /**
     * Displays a single GoodsActivity model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $model = GoodsActivity::formatModel($model);
//        $shippingCodeNameMap = Shipping::getshippingCodeNameMap();
        $shippingCodeNameMap = Shipping::getShippingCodeDescMap();
        $act_type_map = GoodsActivity::$act_type_map;

        return $this->render('view', [
            'model' => $model,
            'shippingCodeNameMap' => $shippingCodeNameMap,
            'act_type_map' => $act_type_map,
        ]);
    }

    /**
     * Creates a new GoodsActivity model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new GoodsActivity();
        $model->setScenario('insert');

        //  获取活动类型
        if (!empty(Yii::$app->request->get('act_type'))) {
            $model->act_type = Yii::$app->request->get('act_type');
        }

        if ($model->load(Yii::$app->request->post())) {
            if ($this->safeSave($model)) {
                return $this->redirect(['view', 'id' =>  $model->act_id]);
            } else {
                Yii::$app->session->setFlash('error', '提交失败，请检查表单');
            }
        } else {
            if (Yii::$app->request->isGet) {
                //  指定要创建的活动的类型
                if (!empty($model->act_type)) {

                    //  秒杀活动指定配送方式为 小美支付(满额包邮),订单有效期默认半小时
                    if ($model->act_type == GoodsActivity::ACT_TYPE_FLASH_SALE) {
                        $model->shipping_code = 'fgaf';
                        $model->order_expired_time = 1800;
                    } else {
                        $model->shipping_code = 'fpd';
                        $model->order_expired_time = 172800;
                    }
                }
            }
        }

//        $shippingCodeNameMap = Shipping::getshippingCodeNameMap();
        $shippingCodeNameMap = Shipping::getShippingCodeDescMap();
        $act_type_map = GoodsActivity::$act_type_map;
        $allGoodsList = Goods::getUnDeleteGoodsMap();

        return $this->render('create', [
            'model' => $model,
            'shippingCodeNameMap' => $shippingCodeNameMap,
            'act_type_map' => $act_type_map,
            'allGoodsList' => $allGoodsList,
        ]);
    }

    /**
     * Updates an existing GoodsActivity model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->setScenario('update');
        $model = GoodsActivity::formatModel($model);

        //  把前端的时间控件转换成时间戳
        $data = Yii::$app->request->post();
        if ($model->load($data)) {
            if ($this->safeSave($model)) {
                return $this->redirect(['view', 'id' => $id]);
            } else {
//                Yii::$app->session->setFlash('error', '更新失败，请检查表单');
                return $this->redirect(['update', 'id' => $model->act_id]);
            }
        } else {
            if ($model->hasErrors()) {
                die(json_encode($model->getErrors()));
            }
//            $shippingCodeNameMap = Shipping::getshippingCodeNameMap();
            $shippingCodeNameMap = Shipping::getShippingCodeDescMap();
            $act_type_map = GoodsActivity::$act_type_map;
            $allGoodsList = Goods::getUnDeleteGoodsMap();

            return $this->render('update', [
                'model' => $model,
                'shippingCodeNameMap' => $shippingCodeNameMap,
                'act_type_map' => $act_type_map,
                'allGoodsList' => $allGoodsList,
            ]);
        }
    }

    /**
     * Deletes an existing GoodsActivity model.
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
     * Finds the GoodsActivity model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return GoodsActivity the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = GoodsActivity::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * 保存之前对ext_info进行处理，
     * 修正对应商品的起售数量和库存、本店售价设置为团采价格
     * @param $model
     * @return mixed
     */
    protected function safeSave($model){
        $ext_info = [];
        $ext_info['price_ladder'][] = [
            'amount' => $model->start_num,
            'price' => $model->act_price
        ];
        if (!empty($model->amount) && !empty($model->price)) {
            $ext_info['price_ladder'][] = [
                'amount' => $model->amount,
                'price' => $model->price
            ];
        }
        $ext_info['restrict_amount'] = $model->restrict_amount;
        $ext_info['gift_integral'] = $model->gift_integral;
        $ext_info['deposit'] = $model->deposit;
        $model->ext_info = serialize($ext_info);

        /*  团采/秒杀 与普通商品解耦，ext 信息、起售数量、价格 不交叉
        $goods = Goods::findOne($model->goods_id);
        if ($goods) {
            $goods->start_num = $model->start_num;
            $goods->shop_price = $model->act_price;
            //  如果设定了团拼活动支持的最大库存（下单量达到自动结束团拼）
            if ($model->restrict_amount) {
                $goods->goods_number = $model->restrict_amount;
            }

            $goods->save();
            if ($goods->hasErrors()) {
                $mgs = TextHelper::getErrorsMsg($goods->getErrors());
                Yii::$app->session->setFlash('error', $mgs);
            }
        } else {
            Yii::$app->session->setFlash('error', '商品信息不正确，请检查');
            return false;
        }
        */

        if ($model->act_type == GoodsActivity::ACT_TYPE_GROUP_BUY) {
            $goods = Goods::find()
                ->joinWith(['brand'])
                ->where(['goods_id' => $model->goods_id])
                ->one();
            $model->start_num = $goods->start_num;
            $model->act_price = $goods->shop_price;
            $model->buy_by_box = $goods->buy_by_box;
            $model->number_per_box = $goods->number_per_box;
            if ($goods->supplier_user_id == 1257) {
                $model->shipping_code = Yii::$app->params['zhiFaDefaultShippingCode']; //  默认直发团采的配送方式为 小美直发满额包邮
            } else {
                $shippingId = $goods->brand->shipping_id;
                $shippingCode = Shipping::getShippingCodeById($shippingId);
                $model->shipping_code = $shippingCode; //  默认非直发团采的配送方式为 到付
            }
        }


        if ($model->save()) {
            return true;
        } else {
            if ($model->hasErrors()) {
                $errMsg = TextHelper::getErrorsMsg($model->errors);
            } else {
                $errMsg = '';
            }
            Yii::$app->session->setFlash('error', '商品信息不正确，请检查'.PHP_EOL.$errMsg);
            return false;
        }
    }
}
