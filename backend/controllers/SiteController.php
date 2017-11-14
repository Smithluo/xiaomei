<?php
namespace backend\controllers;


use backend\models\GoodsActivity;
use common\helper\CacheHelper;
use common\helper\DateTimeHelper;
use Yii;
use common\helper\FileHelper;
use common\models\Goods;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use backend\models\BackendLoginForm;

/**
 * Site controller
 */
class SiteController extends Controller
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
                        'actions' => [ 'login', 'error', 'upload'],
                        'allow' => true,
                    ],
                    [
                        'actions' => [
                            'logout', 'index', 'version-log', 'clear-cache', 'refresh-cache', 'check'
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new BackendLoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * 清空缓存
     */
    public function actionClearCache() {
        $mCacheDir = Yii::getAlias('@mRoot'). '/data/cache';
        $scCacheDir = Yii::getAlias('@scRoot'). '/temp';
        $yiiCacheDir = Yii::getAlias('@market'). '/runtime/cache/zh'; // 在sc站用Yii::$app->cache->set()保存到的目录

        FileHelper::clearDirectory($mCacheDir);
        FileHelper::clearDirectory($scCacheDir.'/caches');
        FileHelper::clearDirectory($scCacheDir.'/compiled');
        FileHelper::clearDirectory($scCacheDir.'/static_caches');
        FileHelper::clearDirectory($yiiCacheDir);

        CacheHelper::setUserRankCache();
        CacheHelper::setShopConfigParams();
        CacheHelper::setRegionCache();
        CacheHelper::setRegionAppCache();
        CacheHelper::setRegionWechatRegisterCache();
        CacheHelper::setServicerCache();
        CacheHelper::setCategoryCache();
        CacheHelper::setGoodsCategoryCache();
        CacheHelper::setBrandCatCache();

        $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * 刷新缓存
     */
    public function actionRefreshCache()
    {
        CacheHelper::setUserRankCache();
        CacheHelper::setShopConfigParams();
        CacheHelper::setRegionCache();
        CacheHelper::setRegionAppCache();
        CacheHelper::setRegionWechatRegisterCache();
        CacheHelper::setServicerCache();
        CacheHelper::setGoodsCategoryCache();
        CacheHelper::setBrandCatCache();

        $this->redirect(['index']);
    }

    public function actionVersionLog()
    {
        return $this->render('version');
    }
//    public function actionUpload()
//    {
//        return $this->render('upload', ['model' => ]);
//    }

//  检验数据的入口页面
    public function actionCheck()
    {
        //  【1】商品信息检查   检查已上架未删除的商品 和 参与活动(o_goods_activity)的商品
        //  [1.1]   商品库存为0
        //  [1.2]   商品名称为空
        //  [1.3]   商品市场价为0
        //  [1.4]   直发商品重量为0
        $checkGoodsInfo = $this->checkGoodsInfo();
        //  [1.5]   商品品牌为空 或 不存在
        //  [1.6]   商品分类不存在 或 不存在
        //  [1.7]   商品设置为按箱购买 但装箱数为0 或 起售数量不是装箱数的整数倍
        //  [1.8]   商品库存 等于或小于 告警库存

        //  【2】活动信息检查

        //  【3】订单信息检查

        //  【4】用户信息检查

        return $this->render('check', $checkGoodsInfo);
    }

    //  商品信息检查   检查已上架未删除的商品 和 参与活动(o_goods_activity)的商品
    public function checkGoodsInfo()
    {
        $query = $this->getBasicGoodsQuery();
        //  1、商品库存为0
        $goodsStockEmptyNum = $query->andWhere(['goods_number' => 0])->count();
        //  2、商品名称为空
        $goodsNameEmptyNum = $query->andWhere(['goods_name' => ''])->count();
        //  3、商品市场价为0
        $marketPriceUnsetNum = $query->andWhere(['market_price' => 0.00])->count();
        //  4、直发商品重量为0
        $directGoodsNoWeightNum = $query->andWhere(['goods_weight' => 0.000])->count();

        return [
            'goodsStockEmptyNum' => $goodsStockEmptyNum,
            'goodsNameEmptyNum' => $goodsNameEmptyNum,
            'marketPriceUnsetNum' => $marketPriceUnsetNum,
            'directGoodsNoWeightNum' => $directGoodsNoWeightNum,
        ];
    }

    /**
     * 拼接基础的 query 只查看 当前上架的商品 和 参与当前有效活动的下架商品
     * @return $this
     */
    private function getBasicGoodsQuery()
    {
        $gmtTime = DateTimeHelper::gmtime();
        $activityGoods = GoodsActivity::find()
            ->select(['goods_id'])
            ->where(['<', 'start_time', $gmtTime])
            ->andWhere(['>', 'end_time', $gmtTime])
            ->all();

        if (!empty($activityGoods)) {
            //  如果当前有 正在进行中的 团采/秒杀 活动，则验证活动中的商品
            $activityGoodsIdList = ArrayHelper::getColumn($activityGoods, 'goods_id');

            $query = Goods::find()
                ->where([
                    'or',
                    [
                        'is_on_sale' => Goods::IS_ON_SALE,
                        'is_delete' => Goods::IS_NOT_DELETE
                    ],
                    [
                        'goods_id' => $activityGoodsIdList
                    ]
                ]);
        } else {
            $query = Goods::find()
                ->where([
                    'is_on_sale' => Goods::IS_ON_SALE,
                    'is_delete' => Goods::IS_NOT_DELETE
                ]);
        }

        return $query;
    }
}
