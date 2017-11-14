<?php

namespace backend\controllers;

use backend\models\GoodsTag;
use common\helper\GoodsHelper;
use common\helper\NumberHelper;
use common\models\GoodsLockStock;
use common\models\Spu;
use Yii;
use yii\base\Exception;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\UploadedFile;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use backend\models\GoodsGallery;
use backend\models\MoreGoodsGallery;
use backend\models\ServicerStrategy;
use backend\models\Users;
use backend\models\Category;
use backend\models\Goods;
use backend\models\GoodsSearch;
use backend\models\GoodsInfoImportForm;
use backend\models\Tags;
use backend\models\Shipping;
use backend\models\UserRank;
use common\helper\TextHelper;
use common\controllers\Controller;
use common\helper\DateTimeHelper;
use kartik\grid\EditableColumnAction;

/**
 * GoodsController implements the CRUD actions for Goods model.
 */
class GoodsController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'view', 'update', 'delete', 'create', 'upload', 'out-put-goods-list',
                    'edit-goods-desc', 'update-goods-info', 'editGoodsSn', 'editGoodsName', 'editGoodsBrief',
                    'editStartNum', 'editGoodsNumber', 'editMeasureUnit', 'editNumberPerBox', 'editSort','
                    import', 'goods-list',
                ],
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

    /**
     * 模板上直接修改字段值，ajax调用
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
                'modelClass' => Goods::className(),
                'outputValue' => function($model, $attribute, $key, $index) {
                    return ''.$model->$attribute;
                },
            ],
            'editHot' => [
                'class' => EditableColumnAction::className(),
                'modelClass' => Goods::className(),
                'outputValue' => function($model, $attribute, $key, $index) {
                    return ''.Yii::$app->params['is_or_not_map'][$model->$attribute];
                },
                'outputMessage' => function($model, $attribute, $key, $index) {
                    return '';
                },
                'showModelErrors' => true,
                'errorOptions' => ['header' => '']
            ],
            'editSort' => [
                'class' => EditableColumnAction::className(),
                'modelClass' => Goods::className(),
                'outputValue' => function($model, $attribute, $key, $index) {
                    return ''.(int)$model->$attribute;
                },
                'outputMessage' => function($model, $attribute, $key, $index) {
                    return '';
                },
                'showModelErrors' => true,
                'errorOptions' => ['header' => '']
            ],
            'editGoodsSn' => [
                'class' => EditableColumnAction::className(),
                'modelClass' => Goods::className(),
                'outputValue' => function($model, $attribute, $key, $index) {
                    return ''.$model->$attribute;
                },
                'outputMessage' => function($model, $attribute, $key, $index) {
                    return '';
                },
                'showModelErrors' => true,
                'errorOptions' => ['header' => '']
            ],
            'editSkuSize' => [
                'class' => EditableColumnAction::className(),
                'modelClass' => Goods::className(),
                'outputValue' => function($model, $attribute, $key, $index) {
                    return ''.$model->$attribute;
                },
                'outputMessage' => function($model, $attribute, $key, $index) {
                    return '';
                },
                'showModelErrors' => true,
                'errorOptions' => ['header' => '']
            ],
            'editPrefix' => [
                'class' => EditableColumnAction::className(),
                'modelClass' => Goods::className(),
                'outputValue' => function($model, $attribute, $key, $index) {
                    return ''.Goods::$prefixMap[$model->$attribute];
                },
                'outputMessage' => function($model, $attribute, $key, $index) {
                    return '';
                },
                'showModelErrors' => true,
                'errorOptions' => ['header' => '']
            ],
            'editSpuId' => [
                'class' => EditableColumnAction::className(),
                'modelClass' => Goods::className(),
                'outputValue' => function($model, $attribute, $key, $index) {
                    $spuMap = Spu::spuMap();
                    if (isset($spuMap[$model->$attribute])) {
                        return $spuMap[$model->$attribute];
                    } else {
                        return '未设置';
                    };
                },
                'outputMessage' => function($model, $attribute, $key, $index) {
                    return '';
                },
                'showModelErrors' => true,
                'errorOptions' => ['header' => '']
            ],
            'editGoodsName' => [
                'class' => EditableColumnAction::className(),
                'modelClass' => Goods::className(),
                'outputValue' => function($model, $attribute, $key, $index) {
                    return ''.$model->$attribute;
                },
                'outputMessage' => function($model, $attribute, $key, $index) {
                    return '';
                },
                'showModelErrors' => true,
                'errorOptions' => ['header' => '']
            ],
            'editGoodsNumber' => [
                'class' => EditableColumnAction::className(),
                'modelClass' => Goods::className(),
                'outputValue' => function($model, $attribute, $key, $index) {
                    return ''.(int)$model->$attribute;
                },
                'outputMessage' => function($model, $attribute, $key, $index) {
                    return '';
                },
                'showModelErrors' => true,
                'errorOptions' => ['header' => '']
            ],
            'editStartNum' => [
                'class' => EditableColumnAction::className(),
                'modelClass' => Goods::className(),
                'outputValue' => function($model, $attribute, $key, $index) {
                    $model->setScenario('update');
                    $model->checkStartNumber();
                    if ($model->hasErrors()) {
                        return false;
                    } else {
                        return ''.(int)$model->$attribute;
                    }

                },
                'outputMessage' => function($model, $attribute, $key, $index) {
//                    $model->setScenario('update');
                    $model->checkStartNumber();
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
            'editKeywords' => [
                'class' => EditableColumnAction::className(),
                'modelClass' => Goods::className(),
                'outputValue' => function($model, $attribute, $key, $index) {
                    return ''.$model->$attribute;
                },
                'outputMessage' => function($model, $attribute, $key, $index) {
                    return '';
                },
                'showModelErrors' => true,
                'errorOptions' => ['header' => '']
            ],
            'editGoodsBrief' => [
                'class' => EditableColumnAction::className(),
                'modelClass' => Goods::className(),
                'outputValue' => function($model, $attribute, $key, $index) {
                    return ''.$model->$attribute;
                },
                'outputMessage' => function($model, $attribute, $key, $index) {
                    return '';
                },
                'showModelErrors' => true,
                'errorOptions' => ['header' => '']
            ],
            'editSellerNote' => [
                'class' => EditableColumnAction::className(),
                'modelClass' => Goods::className(),
                'outputValue' => function($model, $attribute, $key, $index) {
                    return ''.$model->$attribute;
                },
                'outputMessage' => function($model, $attribute, $key, $index) {
                    return '';
                },
                'showModelErrors' => true,
                'errorOptions' => ['header' => '']
            ],
            'editShelfLife' => [
                'class' => EditableColumnAction::className(),
                'modelClass' => Goods::className(),
                'outputValue' => function($model, $attribute, $key, $index) {
                    return ''.$model->$attribute;
                },
                'outputMessage' => function($model, $attribute, $key, $index) {
                    return '';
                },
                'showModelErrors' => true,
                'errorOptions' => ['header' => '']
            ],
            'editMeasureUnit' => [
                'class' => EditableColumnAction::className(),
                'modelClass' => Goods::className(),
                'outputValue' => function($model, $attribute, $key, $index) {
                    return ''.$model->$attribute;
                },
                'outputMessage' => function($model, $attribute, $key, $index) {
                    return '';
                },
                'showModelErrors' => true,
                'errorOptions' => ['header' => '']
            ],
            'editNumberPerBox' => [
                'class' => EditableColumnAction::className(),
                'modelClass' => Goods::className(),
                'outputValue' => function($model, $attribute, $key, $index) {
                    return ''.(int)$model->$attribute;
                },
                'outputMessage' => function($model, $attribute, $key, $index) {
                    return '';
                },
                'showModelErrors' => true,
                'errorOptions' => ['header' => '']
            ],
            'editBuyByBox' => [
                'class' => EditableColumnAction::className(),
                'modelClass' => Goods::className(),
                'outputValue' => function($model, $attribute, $key, $index) {
                    return ''.Yii::$app->params['is_or_not_map'][$model->$attribute];
                },
                'outputMessage' => function($model, $attribute, $key, $index) {
                    return '';
                },
                'showModelErrors' => true,
                'errorOptions' => ['header' => '']
            ],
            'editGoodsWeight' => [
                'class' => EditableColumnAction::className(),
                'modelClass' => Goods::className(),
                'outputValue' => function($model, $attribute, $key, $index) {
                    return ''.NumberHelper::weightFormat($model->$attribute);
                },
                'outputMessage' => function($model, $attribute, $key, $index) {
                    return '';
                },
                'showModelErrors' => true,
                'errorOptions' => ['header' => '']
            ],
            'editMarketPrice' => [
                'class' => EditableColumnAction::className(),
                'modelClass' => Goods::className(),
                'outputValue' => function($model, $attribute, $key, $index) {
                    return ''.NumberHelper::price_format($model->$attribute);
                },
                'outputMessage' => function($model, $attribute, $key, $index) {
                    return '';
                },
                'showModelErrors' => true,
                'errorOptions' => ['header' => '']
            ],
            'editShopPrice' => [
                'class' => EditableColumnAction::className(),
                'modelClass' => Goods::className(),
                'outputValue' => function($model, $attribute, $key, $index) {
                    //  修改本店售价，同步更新最小价格
                    $minPrice = GoodsHelper::getMinPrice($model->goods_id);
                    Goods::updateAll(['min_price' => $minPrice], ['goods_id' => $model->goods_id]);
                    return ''.NumberHelper::price_format($model->$attribute);
                },
                'outputMessage' => function($model, $attribute, $key, $index) {
                    return '';
                },
                'showModelErrors' => true,
                'errorOptions' => ['header' => '']
            ],
            'editNeedRank' => [
                'class' => EditableColumnAction::className(),
                'modelClass' => Goods::className(),
                'outputValue' => function($model, $attribute, $key, $index) {
                    $userRankMap = UserRank::$user_rank_map;
                    return ''.$userRankMap[$model->need_rank];
                },
                'outputMessage' => function($model, $attribute, $key, $index) {
                    return '';
                },
                'showModelErrors' => true,
                'errorOptions' => ['header' => '']
            ],
            'editExpireDate' => [
                'class' => EditableColumnAction::className(),
                'modelClass' => Goods::className(),
                'outputValue' => function($model, $attribute, $key, $index) {
                    $userRankMap = UserRank::$user_rank_map;
                    return ''.$userRankMap[$model->need_rank];
                },
                'outputMessage' => function($model, $attribute, $key, $index) {
                    return '';
                },
                'showModelErrors' => true,
                'errorOptions' => ['header' => '']
            ],
            'editTagIds' => [
                'class' => EditableColumnAction::className(),
                'modelClass' => Goods::className(),
                'outputValue' => function($model, $attribute, $key, $index) {
                    return ''.$model->$attribute;
                },
                'outputMessage' => function($model, $attribute, $key, $index) {
                    return '';
                },
                'showModelErrors' => true,
                'errorOptions' => ['header' => '']
            ],

            //  edit-best, edit-new, edit-spec 字段已废弃
            'edit-best' => [
                'class' => EditableColumnAction::className(),
                'modelClass' => Goods::className(),
                'outputValue' => function($model, $attribute, $key, $index) {
                    return ''.$model->$attribute;
                },
                'outputMessage' => function($model, $attribute, $key, $index) {
                    return '';
                },
                'showModelErrors' => true,
                'errorOptions' => ['header' => '']
            ],
            'edit-new' => [
                'class' => EditableColumnAction::className(),
                'modelClass' => Goods::className(),
                'outputValue' => function($model, $attribute, $key, $index) {
                    return ''.$model->$attribute;
                },
                'outputMessage' => function($model, $attribute, $key, $index) {
                    return '';
                },
                'showModelErrors' => true,
                'errorOptions' => ['header' => '']
            ],
            'edit-spec' => [
                'class' => EditableColumnAction::className(),
                'modelClass' => Goods::className(),
                'outputValue' => function($model, $attribute, $key, $index) {
                    return ''.$model->$attribute;
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
     * Lists all Goods models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new GoodsSearch();
        $queryParams = Yii::$app->request->queryParams;
        $dataProvider = $searchModel->search($queryParams);

        $cat_id_map = Category::getGoodsCatIdMap();
        $ranks = UserRank::find()->all();
        $goodsInfoImportModel = new GoodsInfoImportForm();

        $allTagIds = Tags::getAllTagIds();
        $percentTotalMap = ServicerStrategy::getStrategyMap();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'cat_id_map' => $cat_id_map,
            'allTagIds' => $allTagIds,
            'ranks' => $ranks,
            'goodsInfoImportModel' => $goodsInfoImportModel,
            'percentTotalMap' => $percentTotalMap,
            'spuMap' => Spu::spuMap(),
        ]);
    }

    /**
     * Displays a single Goods model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        $params = $this->findModelForViewOrEdit($id);

        return $this->render('view', $params);
    }

    /**
     * Creates a new Goods model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Goods();
        $model->setScenario('insert');
//        $model->setScenario('create');
        $model->discount_disable = 1;   //  默认不使用会员折扣，在编辑时不设默认
        $shippingList = Shipping::findAll(['enabled' => 1]);

        $goodsGallery = [];
        $suppliers = Users::getAllSuppliersArr();

        $post = Yii::$app->request->post();
        if ($model->load($post)) {
            if (Goods::safeSave($model, $post)) {
                return $this->redirect(['view', 'id' => $model->goods_id]);
            }
        }
        $GoodsGalleryList = $model->getGoodsGallery()->indexBy('img_id')->all();

        $moreGalleryList = [];
        for ($i = 0; $i < 3; ++$i) {
            $gallery = new MoreGoodsGallery();
            $moreGalleryList[] = $gallery;
        }

        // 分配标签数据
        if ($model->tags) {
            $tags_str = Tags::getTagsStr($model->tags);
            $tagsMap = Tags::getTagsMap($model->tags);

            $model->tagIds = array_keys($tagsMap);
        } else {
            $tags_str = [];
            $tagsMap = [];
        }
        $allTagIds = Tags::getAllTagIds();

        return $this->render('create', [
            'model' => $model,
            'allUserRank' => UserRank::find()->all(),
            'shippingList' => $shippingList,
            'suppliers' => $suppliers,
            'tags_str' => $tags_str,
            'tagsMap' => $tagsMap,
            'allTagIds' => $allTagIds,
            'goodsGallery' => $goodsGallery,
            'GoodsGalleryList' => $GoodsGalleryList,
            'moreGalleryList' => $moreGalleryList,
            'spuMap' => Spu::spuMap(),
        ]);
    }

    /**
     * 复制商品
     * @param $id   商品id
     * @return string|\yii\web\Response
     */
    public function actionCopy($id)
    {
        $_model = Goods::find()->where(['goods_id' => $id])->asArray()->one();
        unset($_model['goods_id']);
        //  商品图与id严格对应，保证一个商品的图片替换不影响其他商品
        unset($_model['goods_thumb']);
        unset($_model['goods_img']);
        unset($_model['original_img']);

        $model = new Goods();
        $model->setAttributes($_model);
        if ($model) {
            //  修改部分值为默认
            $model->goods_name = $model->goods_name;
            $model->is_on_sale = Goods::NOT_ON_SALE;
            $model->click_count = 0;
            $model->discount_disable = Goods::DISCOUNT_DISABLE;   //  不参与全局会员折扣计算
            $model->add_time = DateTimeHelper::getFormatGMTTimesTimestamp(time());   //  商品入库的时间
            $model->extension_code = 'general';
        }

        $model->setScenario('insert');
        $shippingList = Shipping::findAll(['enabled' => 1]);

        $goodsGallery = [];
        $suppliers = Users::getAllSuppliersArr();
        $GoodsGalleryList = $model->getGoodsGallery()->indexBy('img_id')->all();

        $moreGalleryList = [];
        for ($i = 0; $i < 3; ++$i) {
            $gallery = new MoreGoodsGallery();
            $moreGalleryList[] = $gallery;
        }

        // 分配标签数据
        if ($model->tags) {
            $tags_str = Tags::getTagsStr($model->tags);
            $tagsMap = Tags::getTagsMap($model->tags);

            $model->tagIds = array_keys($tagsMap);
        } else {
            $tags_str = [];
            $tagsMap = [];
        }

        $allTagIds = Tags::getAllTagIds();

        if (!empty($model->goods_desc)) {
            $model->goods_desc = TextHelper::formatRichText($model->goods_desc);
        }

        return $this->render('create', [
            'model' => $model,
            'allUserRank' => UserRank::find()->all(),
            'shippingList' => $shippingList,
            'suppliers' => $suppliers,
            'tags_str' => [],
            'tagsMap' => $tagsMap,
            'allTagIds' => $allTagIds,
            'goodsGallery' => $goodsGallery,
            'GoodsGalleryList' => $GoodsGalleryList,
            'moreGalleryList' => $moreGalleryList,
            'spuMap' => Spu::spuMap(),
        ]);
    }

    /**
     * 复制为积分商品
     * @param $id   商品id
     * @return string|\yii\web\Response
     */
    public function actionIntegral($id)
    {
        $_model = Goods::find()->where(['goods_id' => $id])->asArray()->one();
        unset($_model['goods_id']);
        //  商品图与id严格对应，保证一个商品的图片替换不影响其他商品
        unset($_model['goods_thumb']);
        unset($_model['goods_img']);
        unset($_model['original_img']);

        $model = new Goods();
        $model->setAttributes($_model);
        if ($model) {
            //  修改部分值为默认
            $model->is_on_sale = Goods::NOT_ON_SALE;
            $model->click_count = 0;
            $model->discount_disable = Goods::DISCOUNT_DISABLE;   //  不参与全局会员折扣计算
            $model->add_time = DateTimeHelper::getFormatGMTTimesTimestamp(time());   //  商品入库的时间
            $model->extension_code = 'integral_exchange';
        }

        $model->setScenario('insert');
        $shippingList = Shipping::findAll(['enabled' => 1]);

        $goodsGallery = [];
        $suppliers = Users::getAllSuppliersArr();
        $GoodsGalleryList = $model->getGoodsGallery()->indexBy('img_id')->all();

        $moreGalleryList = [];
        for ($i = 0; $i < 3; ++$i) {
            $gallery = new MoreGoodsGallery();
            $moreGalleryList[] = $gallery;
        }

        // 分配标签数据
        if ($model->tags) {
            $tags_str = Tags::getTagsStr($model->tags);
            $tagsMap = Tags::getTagsMap($model->tags);

            $model->tagIds = array_keys($tagsMap);
        } else {
            $tags_str = [];
            $tagsMap = [];
        }

        $allTagIds = Tags::getAllTagIds();

        if (!empty($model->goods_desc)) {
            $model->goods_desc = TextHelper::formatRichText($model->goods_desc);
        }


        $spuMap = [];   //  积分商品不考虑SPU

        return $this->render('create', [
            'model' => $model,
            'allUserRank' => UserRank::find()->all(),
            'shippingList' => $shippingList,
            'suppliers' => $suppliers,
            'tags_str' => [],
            'tagsMap' => $tagsMap,
            'allTagIds' => $allTagIds,
            'goodsGallery' => $goodsGallery,
            'GoodsGalleryList' => $GoodsGalleryList,
            'moreGalleryList' => $moreGalleryList,
            'spuMap' => $spuMap,
        ]);
    }

    /**
     * Updates an existing Goods model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $params = $this->findModelForViewOrEdit($id);
        $model = $params['model'];
        $model->setScenario('update');

        $post = Yii::$app->request->post();

        if ($model->load($post)) {
            if (Goods::safeSave($model, $post)) {
                return $this->redirect(['view', 'id' => $model->goods_id]);
            } elseif ($model->getErrors()) {
                $this->flashError($model);
            }
        }

        return $this->render('update', $params);
    }

    /**
     * Deletes an existing Goods model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->is_delete = Goods::IS_DELETE;
        $model->is_on_sale = Goods::NOT_ON_SALE;

        $model->save();

        return $this->redirect(['index']);
    }

    /**
     * 导出商品信息列表
     * @return bool
     */
    public function actionExport()
    {
        ini_set('memory_limit', '1G');
        $search = new GoodsSearch();
        $queryParams = Yii::$app->request->queryParams;
        $search->export($queryParams);
    }

    /**
     * Finds the Goods model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Goods the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Goods::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * 为 view 、 update 构造model
     * @param $id
     * @return array
     */
    protected function findModelForViewOrEdit($id)
    {
        /*$model = Goods::find()
            ->joinWith('volumePrice')
            ->joinWith('goodsAttr')
            ->joinWith('goodsCat')
            ->joinWith('brand')
            ->joinWith('percentTotal')
            ->joinWith('goodsGallery')
            ->joinWith('moqs')
            ->where([
                Goods::tableName() . '.goods_id' => $id
            ])->one();*/
        $model = self::findModel($id);

        // 分配价格梯度数据
        if ($model && !empty($model->volumePrice)) {
            $i = 1;
            foreach ($model->volumePrice as $price) {
                $number_key = 'volume_number_' . $i;
                $price_key = 'volume_price_' . $i;
                $model->$number_key = $price['volume_number'];
                $model->$price_key = $price['volume_price'];

                $i++;
            }
        }
        // 分配商品属性数据
        if ($model && !empty($model->goodsAttr)) {
            foreach ($model->goodsAttr as $attr) {
                switch ($attr['attr_id']) {
                    case 165:
                        $model->region = $attr['attr_value'];
                        break;
                    case 211:
                        $model->effect = $attr['attr_value'];
                        break;
                    case 212:
                        $model->sample = $attr['attr_value'];
                        break;
                    default :
                        break;
                }
            }
        }
        // 分配moq数据
        if ($model->moqs) {
            foreach ($model->moqs as $moq) {
                if ($moq['user_rank'] == UserRank::USER_RANK_MEMBER) {
                    $model->moq_vip = $moq->moq;
                } elseif ($moq['user_rank'] == UserRank::USER_RANK_VIP) {
                    $model->moq_svip = $moq->moq;
                }
            }
        }

        // 分配标签数据
        if ($model->tags) {
            $tags_str = Tags::getTagsStr($model->tags);
            $tagsMap = Tags::getTagsMap($model->tags);

            $model->tagIds = array_keys($tagsMap);
        } else {
            $tags_str = [];
            $tagsMap = [];
        }
        $allUserRank = UserRank::find()->all();
        $shippingList = Shipping::findAll(['enabled' => 1]);
        $suppliers = Users::getAllSuppliersArr();

        //  轮播图
        $goodsGallery = $model->getGoodsGallery()->all();

        //  在商品详情页显示多个轮播图的处理
        $GoodsGalleryList = $model->getGoodsGallery()->indexBy('img_id')->all();

        $moreGalleryList = [];
        for ($i = 0; $i < 3; ++$i) {
            $gallery = new MoreGoodsGallery();
            $moreGalleryList[] = $gallery;
        }

        $model->goods_desc = TextHelper::formatRichText($model->goods_desc);
        $allTagIds = Tags::getAllTagIds();

        $newStockLock = new GoodsLockStock();

        return [
            'model' => $model,
            'spuName' => !empty($model->spu_id) ? $model->spu->name : '未设置SPU',
            'allUserRank' => $allUserRank,
            'shippingList' => $shippingList,
            'suppliers' => $suppliers,
            'tags_str' => $tags_str,
            'tagsMap' => $tagsMap,
            'allTagIds' => $allTagIds,
            'goodsGallery' => $goodsGallery,
            'GoodsGalleryList' => $GoodsGalleryList,
            'moreGalleryList' => $moreGalleryList,
            'newStockLock' => $newStockLock,
            'spuMap' => Spu::spuMap(),
        ];
    }

    /**
     * 获取轮播图的对象列表，只存储4张轮播图
     *
     * @param $model
     * @return array
     */
    protected function galleryList($model)
    {
        $goodsGallery = [];
        if ($model->goodsGallery) {
            foreach ($model->goodsGallery as $item) {
                $goodsGallery[] = $item;
            }
        }

        return $goodsGallery;
    }

    /**
     * 商品信息批量导入
     * @return string|void
     */
    public function actionUpdateGoodsInfo()
    {
        $goodsInfoImportModel = new GoodsInfoImportForm();

        if (Yii::$app->request->isPost) {
            $goodsInfoImportModel->file = UploadedFile::getInstance($goodsInfoImportModel, 'file');
            if ($goodsInfoImportModel->import()) {
                // 文件上传成功
                $this->redirect(['index']);
                return;
            }
        }

        return '导入失败';
    }

    public function actionDeleteGallery($id, $goods_id) {
        $gallery = GoodsGallery::findOne([
            'img_id' => $id,
        ]);
        if (!empty($gallery)) {
            $gallery->delete();
        };
        return $this->redirect(['update', 'id' => $goods_id]);
    }

    /**
     * Deletes an existing Event model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionToggle($id)
    {
        $_model = $this->findModel($id);
        $_model->setScenario('update');
        if ($_model->is_on_sale == Goods::IS_ON_SALE) {
            $_model->setAttribute('is_on_sale', Goods::NOT_ON_SALE);
        } else {
            $_model->setAttribute('is_on_sale', Goods::IS_ON_SALE);
        }

        if ($_model->save()) {
            Yii::$app->session->setFlash('success', '商品上架状态修改成功');
        } else {
            die(json_encode($_model->errors));
            Yii::$app->session->setFlash('faild', '商品上架状态修改失败'.TextHelper::getErrorsMsg($_model->errors));
        }

        $referrer = Yii::$app->request->referrer;
        return $this->redirect($referrer);
    }

    /**
     * 切换 明星单品
     *
     * @param $id
     * @return \yii\web\Response
     */
    public function actionStar($id)
    {
        $star = GoodsTag::find()
            ->where([
                'goods_id' => $id,
                'tag_id' => 5,
            ])->one();
        if (!empty($star)) {
            if ($star->delete()) {
                Yii::$app->session->setFlash('success', '删除商品标签 明星单品 成功');
            } else {
//                die(json_encode($star->errors));
                Yii::$app->session->setFlash('success', '删除商品标签 明星单品 成功');
            }
        } else {
            $starTag = new GoodsTag();
            $starTag->goods_id = $id;
            $starTag->tag_id = 5;
            if ($starTag->save()) {
                Yii::$app->session->setFlash('success', '商品打标为 明星单品 成功');
            } else {
//                die(json_encode($starTag->errors));
                Yii::$app->session->setFlash('success', '商品打标为 明星单品 失败');
            }
        }

        $referrer = Yii::$app->request->referrer;
        return $this->redirect($referrer);
    }

    public function actionQueryGoods($k) {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $result = [
            'code' => 0,
            'msg' => '',
            'data' => [],
        ];

        $query = Goods::find()->select([
            'goods_id',
            'goods_name',
            'goods_sn',
        ])->orderBy([
            'goods_name' => SORT_ASC,
            'goods_id' => SORT_DESC,
        ])->limit(20);

        if (is_int($k)) {
            $query->orWhere([
                'goods_id' => $k,
            ]);
        }
        $query->orWhere([
            'like',
            'goods_name',
            $k,
        ]);
        $query->orWhere([
            'like',
            'goods_sn',
            $k
        ]);

        $result['data'] = $query->all();

        return $result;
    }

    /**
     * ajax拉取商品，给select2控件使用
     * @param null $q
     * @param null $id
     * @return array
     */
    public function actionGoodsList($q = null, $id = null) {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $query = new Query();
            $query->select('goods_id, goods_name, goods_sn')
                ->from(Goods::tableName())
                ->where(['like', 'goods_name', $q])
                ->orWhere(['like', 'goods_sn', $q])
                ->orWhere([
                    'goods_id' => $q
                ])->andWhere([
                    'is_on_sale' => 1,
                    'is_delete' => 0,
                ])
                ->limit(20);
            $command = $query->createCommand();
            $data = $command->queryAll();

            $out['results'] = [];
            foreach ($data as $item) {
                $out['results'][] = [
                    'id' => $item['goods_id'],
                    'text' => '('. $item['goods_id']. ')'. $item['goods_name']. '('. $item['goods_sn']. ')',
                ];
            }
        }
        elseif ($id > 0) {
            $goods = Goods::find($id);
            $out['results'] = ['id' => $id, 'text' => '('. $id. ')'. $goods['goods_name']. '('. $goods['goods_sn']. ')'];
        }
        return $out;
    }

    public function actionLockStock($id) {
        $model = $this->findModel($id);
        //处理锁定库存
        Yii::$app->getDb()->createCommand()->setSql('lock tables '. Goods::tableName(). ' WRITE')->execute();
        $transaction = ActiveRecord::getDb()->beginTransaction();
        try {
            $lockStock = new GoodsLockStock();
            if ($lockStock->load(Yii::$app->request->post())) {
                $lockStock->goods_id = $model->goods_id;
                $lockStock->lock_time = DateTimeHelper::gmtime();
                $lockStock->user_id = Yii::$app->user->identity['user_id'];
                if ($lockStock->validate()) {
                    if ($lockStock->save()) {
                        $model->goods_number -= $lockStock->lock_num;
                        if ($model->goods_number < 0) {
                            throw new Exception('商品库存锁定失败 库存不足');
                        }
                        if (!$model->save()) {
                            throw new Exception('商品库存锁定失败 0');
                        }
                    } else {
                        throw new Exception('锁定库存锁定失败 1');
                    }
                } else {
                    throw new Exception('锁定库存验证失败 e = '. VarDumper::dumpAsString($lockStock->errors));
                }
            } else {
                throw new Exception('锁定库存加载失败');
            }

            $transaction->commit();
            Yii::$app->getDb()->createCommand()->setSql("unlock tables")->execute();
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->getDb()->createCommand()->setSql("unlock tables")->execute();
            throw $e;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::$app->getDb()->createCommand()->setSql("unlock tables")->execute();
            throw $e;
        }

        return $this->gotoView($id);
    }

    public function actionReleaseLockStock($id) {
        $lockStock = GoodsLockStock::find()->joinWith([
            'goods goods',
        ])->where([
            'id' => $id,
        ])->one();

        if (empty($lockStock)) {
            throw new NotFoundHttpException('找不到锁定库存', 1);
        }

        $goodsId = $lockStock->goods_id;

        $lockStock->release();

        return $this->gotoView($goodsId);
    }

}
