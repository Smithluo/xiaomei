<?php

namespace service\controllers;

use common\helper\CacheHelper;
use common\helper\NumberHelper;
use service\models\Goods;
use common\models\ServicerSpecStrategy;
use Yii;
use service\models\Brand;
use service\models\BrandSearch;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\VarDumper;

/**
 * BrandController implements the CRUD actions for Brand model.
 */
class ServiceBrandController extends XmController
{

    public $default_brand_id = 0;

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
                        'actions' => ['index', 'change-percent', 'brand-goods', 'change-all-percent'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'change-percent' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Brand models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BrandSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $brand = Brand::find()->where(['is_show' => 1])->orderBy(['brand_id' => SORT_ASC])->one();
        $goodsList = Goods::find()->with([
            'supplyInfo',
        ])->where([
            'brand_id' => $brand->brand_id
        ])->andWhere([
            'not',
            ['extension_code' => 'integral_exchange'],
        ])->andWhere([
            'is_on_sale' => 1,
            'is_delete' => 0,
        ])->all();

        $servicerDividePercentConfig = CacheHelper::getShopConfigParams(['servicer_divide_pre']);

        $percent = 0;
        if (isset($servicerDividePercentConfig['servicer_divide_pre']['value'])) {
            $percent = $servicerDividePercentConfig['servicer_divide_pre']['value'];
        }

        $this->default_brand_id = $brand->brand_id;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'goodsList' => $goodsList,
            'index' => 4,
            'percent' => $percent,
        ]);
    }

    /**
     * 修改给二级服务商的分成比例，会自动计算出一级服务商的分成比例
     * @return string
     */
    public function actionChangePercent() {
        $percent = Yii::$app->request->post('percent', 0);
        $brand_id = Yii::$app->request->post('brand_id', 0);
        if(empty($percent) || empty($brand_id)) {
            Yii::info('缺少分成比例或者品牌id', __METHOD__);
            return (json_encode(['code'=>1, 'msg'=>'请输入分成比例和品牌']));
        }
        if(!is_numeric($percent) || !is_numeric($brand_id)) {
            Yii::info('分成比例或者品牌id不是数字', __METHOD__);
            return (json_encode(['code'=>2, 'msg'=>'分成比例只能输入数字']));
        }
        if($percent > 100 || $percent < 0) {
            Yii::info('分成比例只能是0-100之间的数字', __METHOD__);
            return (json_encode(['code'=>3, 'msg'=>'分成比例只能在0-100间']));
        }
        $brand_info = Brand::findOne(['brand_id'=>$brand_id]);
        if($brand_info->servicer_strategy_id == 0) {
            Yii::info('当前品牌未设置服务商返点', __METHOD__);
            return (json_encode(['code'=>4, 'msg'=>'当前品牌未设置服务商返点']));
        }
//        $spec_strategy = ServicerSpecStrategy::findOne(['brand_id' => $brand_id, 'servicer_user_id'=>Yii::$app->user->id]);
//        if($spec_strategy == null) {
        $spec_strategy = new ServicerSpecStrategy();
        $spec_strategy->strategy_id = $brand_info->servicer_strategy_id;
        $spec_strategy->servicer_user_id = Yii::$app->user->id;
        $spec_strategy->brand_id = $brand_id;
//        }
        $spec_strategy->percent_level_2 = $percent;
        $spec_strategy->percent_level_1 = 100 - $percent;

        if($spec_strategy->save()) {
            Yii::info('操作成功 spec_strategy = '. VarDumper::export($spec_strategy->toArray()), __METHOD__);
            return (json_encode(['code'=>0, 'msg'=>'操作成功']));
        }
        else {
            Yii::info('操作失败 errors = '. VarDumper::export($spec_strategy->errors), __METHOD__);
            return (json_encode(['code'=>5, 'msg'=>'操作失败：'. json_encode($spec_strategy->errors[0])]));
        }
    }

    /**
     * 获取该品牌下所有商品信息
     */
    public function actionBrandGoods() {
        $brand_id = Yii::$app->request->post('brand_id');
        if($brand_id == 0) {
            Yii::info('缺少brand_id', __METHOD__);
            die(json_encode([
                'code' => 1,
                'msg' => '缺少参数',
            ]));
        }
        $goodsList = Goods::find()->where([
            'brand_id' => $brand_id
        ])->andWhere([
            'is_on_sale' => 1,
            'is_delete' => 0,
        ])->andWhere([
            'not',
            ['extension_code' => 'integral_exchange'],
        ])->with([
            'supplyInfo'
        ])->all();
        $brandInfo = Brand::findOne(['brand_id'=>$brand_id]);
        $data = [];

        $servicerDividePercentConfig = CacheHelper::getShopConfigParams(['servicer_divide_pre']);

        $percent = 0;
        if (isset($servicerDividePercentConfig['servicer_divide_pre']['value'])) {
            $percent = $servicerDividePercentConfig['servicer_divide_pre']['value'];
        }

        foreach($goodsList as $goods) {
            $row['goods_name'] = $goods->goods_name;
            $row['brand_name'] = $brandInfo->brand_name;

            $row['divide_amount'] = 0;
            if (!empty($goods->supplyInfo)) {
                $row['divide_amount'] = NumberHelper::price_format(($goods->shop_price - $goods->supplyInfo->supply_price) * $percent / 100.0);
            }

            $measure_unit = $goods->measure_unit;
            if(empty($measure_unit)) {
                $measure_unit = '件';
            }
            $discount = 1; //   后台不显示折扣，按原始梯度价格显示
            $price_list = \brand\models\VolumePrice::volume_price_list_format(\brand\models\VolumePrice::get_volume_price_list($goods->goods_id), $goods->shop_price, $discount, $goods->start_num, $goods->goods_number);

            $dest_price_list = [];
            foreach($price_list as $price_group) {
                $dest_price_list[] = $price_group['range'].$measure_unit.':￥'.$price_group['price'];
            }

            $row['price_list'] = $dest_price_list;

            $data[] = $row;
        }

        $result = [
            'code' => 0,
            'data' => $data,
        ];

        Yii::info('result = '. VarDumper::export($result), __METHOD__);

        die(json_encode($result));
    }

    /**
     * 一键修改所有品牌的二级服务商分成比例
     * @return string
     * @throws \yii\db\Exception
     */
    public function actionChangeAllPercent() {
        $percent = Yii::$app->request->post('percent', 0);
        $percent = str_replace('%', '', $percent);
        if(empty($percent)) {
            Yii::info('缺少分成比例参数', __METHOD__);
            $this->redirect(['index']);
            return;
//            return (json_encode(['code'=>1, 'msg'=>'缺少分成比例参数']));
        }
        if(!is_numeric($percent)) {
            Yii::info('分成比例只能输入数字', __METHOD__);
            $this->redirect(['index']);
            return;
//            return (json_encode(['code'=>2, 'msg'=>'分成比例只能输入数字']));
        }
        if($percent >= 100 || $percent <= 0) {
            Yii::info('分成比例只能在0-100指尖', __METHOD__);
            $this->redirect(['index']);
            return;
//            return (json_encode(['code'=>3, 'msg'=>'分成比例只能在0-100间']));
        }

        $userModel = Yii::$app->user->identity;
        $userModel['divide_percent'] = $percent;
        $userModel->save(false);

//        $brands = Brand::findAll(['is_show' => 1]);
//        $keys = ['brand_id', 'servicer_user_id', 'strategy_id', 'percent_level_2', 'percent_level_1'];
//        foreach($brands as $brand) {
//            //只有品牌设置的分成点数的才生成服务商的策略
//            if($brand->servicer_strategy_id > 0) {
//                $value = [];
//                $value[] = $brand->brand_id;
//                $value[] = Yii::$app->user->identity['user_id'];
//                $value[] = $brand->servicer_strategy_id;
//                $value[] = $percent;
//                $value[] = 100 - $percent;
//                $values[] = $value;
//            }
//            else {
//                Yii::error('actionChangeAllPercent no servicer_strategy_id brand_id = '. $brand->brand_id);
//            }
//        }
//        Yii::info('插入策略 values = '. VarDumper::export($values), __METHOD__);
//        if(count($values) > 0) {
//            Yii::$app->db->createCommand()->batchInsert(ServicerSpecStrategy::tableName(),
//                $keys,
//                $values
//            )->execute();
//        }

        $this->redirect(['index']);
    }

//    /**
//     * Displays a single Brand model.
//     * @param integer $id
//     * @return mixed
//     */
//    public function actionView($id)
//    {
//        return $this->render('view', [
//            'model' => $this->findModel($id),
//        ]);
//    }
//
//    /**
//     * Creates a new Brand model.
//     * If creation is successful, the browser will be redirected to the 'view' page.
//     * @return mixed
//     */
//    public function actionCreate()
//    {
//        $model = new Brand();
//
//        if ($model->load(Yii::$app->request->post()) && $model->save()) {
//            return $this->redirect(['view', 'id' => $model->brand_id]);
//        } else {
//            return $this->render('create', [
//                'model' => $model,
//            ]);
//        }
//    }
//
//    /**
//     * Updates an existing Brand model.
//     * If update is successful, the browser will be redirected to the 'view' page.
//     * @param integer $id
//     * @return mixed
//     */
//    public function actionUpdate($id)
//    {
//        $model = $this->findModel($id);
//
//        if ($model->load(Yii::$app->request->post()) && $model->save()) {
//            return $this->redirect(['view', 'id' => $model->brand_id]);
//        } else {
//            return $this->render('update', [
//                'model' => $model,
//            ]);
//        }
//    }
//
//    /**
//     * Deletes an existing Brand model.
//     * If deletion is successful, the browser will be redirected to the 'index' page.
//     * @param integer $id
//     * @return mixed
//     */
//    public function actionDelete($id)
//    {
//        $this->findModel($id)->delete();
//
//        return $this->redirect(['index']);
//    }

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
}
