<?php

namespace backend\controllers;

use backend\models\BrandDiscountUploadForm;
use backend\models\Event;
use backend\models\UploadCountryIcons;
use backend\models\Users;
use common\helper\DateTimeHelper;
use common\models\BrandCat;
use common\models\BrandSpecGoodsCat;
use common\models\BrandSpecGoodsSearch;
use common\models\Category;
use common\models\ServicerStrategy;
use common\models\Shipping;
use common\models\TouchBrand;
use kartik\grid\EditableColumnAction;
use Yii;
use backend\models\Brand;
use backend\models\BrandSearch;
use yii\base\Exception;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use common\controllers\Controller;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\UploadedFile;

/**
 * BrandController implements the CRUD actions for Brand model.
 */
class BrandController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [
                            'index', 'update', 'create', 'view', 'import-discount', 'export', 'upload',
                            'editCharacter', 'editIsHot', 'editMainCat', 'upload-country', 'editValue'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
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
        return ArrayHelper::merge(parent::actions(), [
            'upload' => [
                'class' => 'kucha\ueditor\UEditorAction',
                'config' => [
                    'imageUrlPrefix' => 'http://img.xiaomei360.com',
                    'imagePathFormat' => '/image/{yyyy}{mm}{dd}/{time}{rand:6}',
                    'imageRoot' => Yii::getAlias('@mRoot').'/data/attached',
                ],
            ],
            'editValue' =>[
                'class' => EditableColumnAction::className(),
                'modelClass' => Brand::className(),
                'outputValue' => function($model, $attribute, $key, $index) {
                    if ($attribute == 'catId') {
                        $catId = $model->$attribute;
                        $result = '';
                        BrandCat::deleteAll(['brand_id' => $model->brand_id]);
                        if($catId) {
                            foreach ($catId as $value) {
                                $data = [
                                    'brand_id' => $model->brand_id,
                                    'cat_id' => $value,
                                ];
                                $brandCat = new BrandCat();
                                $brandCat->attributes = $data;
                                $brandCat->save();
                                $result .= '' . Category::getCatName($value)['cat_name'] . ',';
                            }

                            if ($result != '') {
                                return substr($result, 0, -1);
                            } else {
                                return '';
                            }
                        }else{
                            return '';
                        }
                    }else if ($attribute == 'is_hot') {
                        return ''.Brand::$is_show_icon_map[$model->$attribute];
                    }else {
                        return ''.$model->$attribute;
                    }
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
     * Lists all Brand models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BrandSearch();
        $uploadModel = new UploadCountryIcons();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $discountImportModel = new BrandDiscountUploadForm();
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'discountImportModel' => $discountImportModel,
            'uploadCountry' => $uploadModel,
        ]);
    }

    public function actionImportDiscount() {

        $discountImportModel = new BrandDiscountUploadForm();

        if (Yii::$app->request->isPost) {
            $discountImportModel->file = UploadedFile::getInstance($discountImportModel, 'file');
            $discountImportModel->import();
        }

        return $this->redirect(['index']);
    }

    /**
     * Displays a single Brand model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        if (!empty($model->touchBrand)) {
            $model->touchBrand->brand_content = \common\helper\TextHelper::formatRichText($model->touchBrand->brand_content);
            $model->touchBrand->license = \common\helper\TextHelper::formatRichText($model->touchBrand->license);
            $model->touchBrand->brand_qualification = \common\helper\TextHelper::formatRichText($model->touchBrand->brand_qualification);
        }

        //  修复 【品牌分成为0的情况 servicer_strategy_id =0 的数据 在view的时候报错——$servicerStrategy model为空】 的问题
        if ($model->servicer_strategy_id == 0) {
            $servicerStrategy = ServicerStrategy::findOne(['percent_total' => 0]);
            $model->servicer_strategy_id = $servicerStrategy->id;
            $model->save();
        }

        $servicerStrategy = ServicerStrategy::findOne(['id' => $model->servicer_strategy_id]);

        $couponEventModelList = Event::find()->select(['event_id', 'event_name'])->where([
            'is_active' => Event::IS_ACTIVE,
            'event_type' => Event::EVENT_TYPE_COUPON,
        ])->asArray()->all();

        $brandCatList = BrandCat::find()->where([
            'brand_id' => $id
        ])->asArray()->all();

        $brandCatIds = [];
        foreach ($brandCatList as $brandCat) {
            $brandCatIds[] = $brandCat['cat_id'];
        }
        $model->brandCatIds = $brandCatIds;

        $couponEventList = [];
        if (!empty($couponEventModelList)) {
            foreach ($couponEventModelList as $event) {
                $couponEventList[$event['event_id']] = '('. $event['event_id']. ')'. $event['event_name'];
            }
        }

        $newSpecCat = new BrandSpecGoodsCat();

        return $this->render('view', [
            'model' => $model,
            'servicerStrategy' => $servicerStrategy,
            'couponEventList' => $couponEventList,
            'newSpecCat' => $newSpecCat,
        ]);
    }

    /**
     * Creates a new Brand model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Brand();
        $touchBrand = new TouchBrand();
        $model->setScenario('insert');
        $touchBrand->setScenario('insert'); //  create
        $servicerStrategy = new ServicerStrategy();
        $shippingList = Shipping::findAll(['enabled' => 1]);
        $suppliers = Users::getAllSuppliers();

        if (
            $model->load(Yii::$app->request->post()) &&
            $touchBrand->load(Yii::$app->request->post()) &&
            $servicerStrategy->load(Yii::$app->request->post())
        ) {
            if (!isset($model->brand_logo_two)) {
                $model->brand_logo_two = '';
            }
            if (!isset($model->supplier_user_id)) {
                $model->supplier_user_id = 0;
            }

            $oldStrategy = ServicerStrategy::findOne([
                'percent_total' => $servicerStrategy->percent_total,
            ]);
            if (empty($oldStrategy)) {
                if (!$servicerStrategy->save()) {
                    $this->flashError($servicerStrategy);
                    return $this->render('create', [
                        'model' => $model,
                        'shippingList' => $shippingList,
                        'suppliers' => $suppliers,
                        'touchBrand' => $touchBrand,
                        'servicerStrategy' => $servicerStrategy,
                    ]);
                }
            }
            else {
                $servicerStrategy = $oldStrategy;
            }

            $model->servicer_strategy_id = $servicerStrategy->id;
            $model->turn_show_time = DateTimeHelper::getFormatGMTDateTime();    //  格林威治的时间
            if ($model->save()) {
                $touchBrand->brand_id = $model->brand_id;
                if ($touchBrand->save()) {
                    $this->flashSuccess('创建品牌成功');
                    return $this->gotoView($model->brand_id);
                }
                else {
                    $model->delete();
                    $this->flashError($touchBrand);
                    return $this->gotoView($model->brand_id);
                }
            }
            else {
                $this->flashError($model);
            }
        }
        else {
            if ($model->hasErrors()) {
                $this->flashError($model);
            }
            if ($touchBrand->hasErrors()) {
                $this->flashError($touchBrand);
            }

        }

        //  设置默认配送方式为运费到付
        $model->shipping_id = Shipping::getDefaultShippingId();
        return $this->render('create', [
            'model' => $model,
            'shippingList' => $shippingList,
            'suppliers' => $suppliers,
            'touchBrand' => $touchBrand,
            'servicerStrategy' => $servicerStrategy,
        ]);
    }

    /**
     * Updates an existing Brand model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $shippingList = Shipping::findAll(['enabled' => 1]);
        $suppliers = Users::getAllSuppliers();

        $model->setScenario('update');

        if (!empty($model->touchBrand)) {
            $model->touchBrand->setScenario('update');
        }

        if (
            $model->touchBrand->load(Yii::$app->request->post()) &&
            $model->servicerStrategy->load(Yii::$app->request->post()) &&
            $model->load(Yii::$app->request->post())
        ) {
            $servicerStrategy = $model->servicerStrategy;
            $oldStrategy = ServicerStrategy::findOne([
                'percent_total' => $servicerStrategy->percent_total,
            ]);
            if (empty($oldStrategy)) {
                if (!$servicerStrategy->save()) {
                    $this->flashError($servicerStrategy);
                    return $this->render('create', [
                        'model' => $model,
                        'shippingList' => $shippingList,
                        'suppliers' => $suppliers,
                        'touchBrand' => $model->touchBrand,
                        'servicerStrategy' => $servicerStrategy,
                    ]);
                }
            }
            else {
                $servicerStrategy = $oldStrategy;
            }

            $model->servicer_strategy_id = $servicerStrategy->id;

            $transaction = ActiveRecord::getDb()->beginTransaction();

            try {
                $params = Yii::$app->request->post();
                $catIds = $params['Brand']['brandCatIds'];
                BrandCat::deleteAll(['brand_id' => $model->brand_id]);
                if($catIds) {
                    foreach ($catIds as $value) {
                        $data = [
                            'brand_id' => $model->brand_id,
                            'cat_id' => $value,
                        ];
                        $brandCat = new BrandCat();
                        $brandCat->attributes = $data;
                        $brandCat->save();
                    }
                }

                $model->touchBrand->save();
                $model->save();

                $transaction->commit();
                $this->flashSuccess('修改品牌成功');
            } catch (Exception $e) {
                $transaction->rollBack();
                throw $e;
            } catch (\Throwable $e) {
                $transaction->rollBack();
                throw $e;
            }

//            if ($model->touchBrand->save() && $model->save()) {
//                $this->flashSuccess('修改品牌成功');
//            }
//            else {
//                if ($model->touchBrand->hasErrors()) {
//                    $this->flashError($model->touchBrand);
//                }
//                if ($model->hasErrors()) {
//                    $this->flashError($model);
//                }
//            }
            return $this->gotoView($id);
        } else {
            if ($model->hasErrors()) {
                $this->flashError($model);
            }
            if ($model->touchBrand->hasErrors()) {
                $this->flashError($model->touchBrand);
            }
            if ($model->servicerStrategy->hasErrors()) {
                $this->flashError($model->servicerStrategy);
            }
            return $this->gotoView($id);
        }
    }

    /**
     * Deletes an existing Brand model.
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
     * Finds the Brand model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Brand the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Brand::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * 导出商品信息列表
     * @return bool
     */
    public function actionExport()
    {

        $brandList = Brand::find()->select([
            'brand_id',
            'brand_name',
            'sort_order',
            'brand_tag',
            'country',
            'brand_desc',
            'brand_depot_area',
            'brand_desc_long',
        ])->where(['is_show' => 1])->orderBy(['brand_id' => SORT_ASC])->all();

        $time = \common\helper\DateTimeHelper::getFormatDateTimeNow();

        \moonland\phpexcel\Excel::export([
            'format' => 'Excel2007',
            'fileName' => '品牌列表_'. $time,
            'models' => $brandList,
            'columns' => [
                'brand_id',
                'brand_name',
                'sort_order',
                'brand_tag',
                'country',
                'brand_desc',
                'brand_depot_area',
                'brand_desc_long',
            ], //without header working, because the header will be get label from attribute label.
            'headers' => [
                'brand_id' => 'brand_id(品牌ID)',
                'brand_name' => 'brand_name(品牌名)',
                'sort_order' => 'sort_order(品牌排序值)',
                'brand_tag' => 'brand_tag(品牌在首页热门商品出现的序列号，填数字，如果有多个按逗号分开)',
                'country' => 'country(国家)',
                'brand_desc ' => 'brand_desc(品牌描述)',
                'brand_depot_area ' => 'brand_depot_area(品牌发货地址)',
                ' ' => 'brand_desc_long(品牌列表页左侧显示的文案)',
            ],
        ]);
    }

    /**
     * @return \yii\web\Response
     * 上传国旗图片
     */
    public function actionUploadCountry()
    {
        $model = new UploadCountryIcons();
        if (Yii::$app->request->isPost) {
            $model->icon = UploadedFile::getInstance($model, 'icon');
            if ($model->upload()) {
                // 文件上传成功
                Yii::$app->session->setFlash('success', '国旗图标上传成功');
                return $this->redirect('index');
            } else {
                Yii::$app->session->setFlash('failed', '国旗图标上传失败，原因是:'.VarDumper::export($model->getErrors()));
                return $this->redirect('index');
            }
        }

    }
}
