<?php

namespace common\models;

use common\helper\DateTimeHelper;
use common\helper\GoodsHelper;
use common\helper\ImageHelper;
use common\helper\TextHelper;
use Yii;

/**
 * This is the model class for table "o_brand".
 *
 * @property integer $brand_id
 * @property string $brand_name
 * @property string $brand_depot_area
 * @property string $brand_logo
 * @property string $brand_logo_two
 * @property string $brand_bgcolor
 * @property string $brand_policy
 * @property string $brand_desc
 * @property string $brand_desc_long
 * @property string $short_brand_desc
 * @property string $site_url
 * @property integer $sort_order
 * @property integer $is_show
 * @property integer $album_id
 * @property integer $is_hot
 * @property string $brand_tag
 * @property string $discount
 * @property string $license
 * @property string $turn_show_time
 * @property integer $supplier_user_id
 * @property string $country
 * @property string $character
 * @property integer $event_id
 * @property integer $shipping_id
 * @property string $main_cat
 * @property string $brand_area
 * @property integer $top_touch_ad_position_id
 * @property integer $center_touch_ad_position_id
 * @property integer $biz_type
 */
class Brand extends \yii\db\ActiveRecord
{
    public $catId = '';

    const IS_SHOW       = 1;
    const IS_NOT_SHOW   = 0;

    const IS_HOT        = 1;
    const IS_NOT_HOT    = 0;

    const BRAND_TAG_HOT     = 1;
    const BRAND_TAG_ONLY    = 2;
    const BRAND_TAG_JK      = 3;
    const BRAND_TAG_EU      = 4;

    const BIZ_TYPE_XMCP     = 0;        //小美诚品
    const BIZ_TYPE_JOINT    = 1;        //合资品牌

    public static $is_show_map = [
        self::IS_SHOW       => '是',
        self::IS_NOT_SHOW   => '否',
    ];
    public static $is_hot_map = [
        self::IS_HOT       => '是',
        self::IS_NOT_HOT   => '否',
    ];

    public static $is_show_icon_map = [
        self::IS_SHOW       => '<span class="glyphicon glyphicon-ok"></span>',
        self::IS_NOT_SHOW   => '<span class="glyphicon glyphicon-remove"></span>',
    ];

    public static $brand_tag_map = [
        self::BRAND_TAG_HOT     => '热门品牌',
        self::BRAND_TAG_ONLY    => '独家品牌',
        self::BRAND_TAG_JK      => '日韩品牌',
        self::BRAND_TAG_EU      => '欧美品牌',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_brand';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['brand_name', 'brand_depot_area', 'brand_desc', 'short_brand_desc', 'brand_tag', 'servicer_strategy_id', 'supplier_user_id', 'turn_show_time', 'shipping_id', 'brand_area'], 'required'],
            [['brand_desc', 'brand_desc_long'], 'string'],
            [['sort_order', 'is_show', 'album_id', 'supplier_user_id', 'shipping_id', 'event_id', 'top_touch_ad_position_id', 'center_touch_ad_position_id', 'biz_type'], 'integer'],
            [['brand_name', 'brand_depot_area'], 'string', 'max' => 60],
            [['site_url'], 'string', 'max' => 255],
            [['brand_bgcolor', 'country'], 'string', 'max' => 10],
            [['discount'], 'string', 'max' => 4],
            [['short_brand_desc'], 'string', 'max' => 100],
            [['brand_tag', 'brand_area'], 'string', 'max' => 50],
            [['character'], 'string', 'max' => 1],
            [['sort_order'], 'integer', 'max' => 255],
            [['sort_order'], 'default', 'value' => 128],
            [['is_show', 'is_hot'], 'default', 'value' => 0],
            [['brand_logo', 'brand_logo_two'], 'required', 'on' => 'insert'],
//            [['brand_logo', 'brand_logo_two'], 'default', 'value' => ''],
            [['supplier_user_id', 'event_id', 'top_touch_ad_position_id', 'center_touch_ad_position_id'], 'default', 'value' => 0],
            [['shipping_id'], 'default', 'value' => Shipping::getDefaultShippingId()],
            [['turn_show_time'], 'string'],
            ['main_cat', 'string', 'max' => 40],

            [
                ['brand_logo', 'brand_logo_two', 'brand_policy',],
                'image',
                'checkExtensionByMimeType' => false,
                'extensions' => 'jpg, jpeg, gif, png',
                'on' => ['insert', 'update']
            ],
            [['country', 'brand_name', 'brand_area'], 'filter', 'filter' => 'trim', 'skipOnArray' => true],
            [['catId'], 'safe'],
        ];
    }

    public function beforeValidate()
    {
        //验证brand_tag是否为合法的
        if (!empty($this->brand_tag)) {
            if (!preg_match('/^[\d,]+$/', $this->brand_tag, $matches)) {
                $this->addError('brand_tag', '热门品牌索引只能包含数字和逗号');
                return false;
            }
            $tags = explode(',', $this->brand_tag);
            foreach ($tags as $tag) {
                if (!is_numeric($tag)) {
                    $this->addError('brand_tag', '热门品牌的索引只能包含数字和逗号');
                    return false;
                }
            }
        }
        return parent::beforeValidate(); // TODO: Change the autogenerated stub
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            if (!isset($this->event_id)) {
                $this->event_id = 0;
            }

            if (!isset($this->top_touch_ad_position_id)) {
                $this->top_touch_ad_position_id = 0;
            }

            if (!isset($this->center_touch_ad_position_id)) {
                $this->center_touch_ad_position_id = 0;
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'brand_id' => '品牌ID',
            'brand_name' => '品牌名称',
            'brand_depot_area' => '品牌发货地址',
            'brand_logo' => '首页Logo(白底)',
            'brand_logo_two' => '详情页Logo 220*80(透明底)',
            'brand_bgcolor' => '品牌logo的背景色,显示字品牌详情页面',
            'brand_policy' => '品牌政策，当前用图片，后面增加品牌政策表，做品牌促销方案和全站促销方案',
            'brand_desc' => '品牌描述',
            'brand_desc_long' => '品牌列表页左侧显示的文案',
            'short_brand_desc' => '品牌短描述',
            'site_url' => '品牌官网',
            'sort_order' => '排序权重',
            'is_show' => '是否显示',
            'is_hot' => '是否热门',
            'album_id' => '相册ID',
            'brand_tag' => '热门分类映射',
            'servicer_strategy_id' => '服务商分成',
            'supplier_user_id' => '品牌商',
            'shipping_id' => '配送方式',
            'discount' => '品牌折扣(在首页和品牌政策显示)',
            'turn_show_time' => '上架时间',
            'country' => '国家',
            'character' => '首字母',
            'event_id' => '参与优惠券活动的ID(用于派券)',
            'main_cat' => '主营品类',
            'brand_area' => '品牌所在区域',
            'top_touch_ad_position_id' => '微信端顶部banner广告位',
            'center_touch_ad_position_id' => '微信端中部广告位',
        ];
    }

    /**
     * @inheritdoc
     * @return BrandQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new BrandQuery(get_called_class());
    }

    public static function getBrandName($brand_id)
    {
        if ($brand_id) {
            $brand = self::find('brand_name')->where(['brand_id' => $brand_id])->one();
            if ($brand) {
                return $brand->brand_name;
            }
        }

        return '';
    }

    /**
     * 获取指定品牌ID 的 品牌ID => 品牌名称
     * @param $brand_id_list
     * @return array
     */
    public static function getBrandListMap($brand_list = [])
    {
        if ($brand_list) {
            $rs = self::find()->select('brand_id, brand_name')
                ->where(['brand_id' => $brand_list])
                ->asArray()
                ->all();
        } else {
            $rs = self::find()->select('brand_id, brand_name')
                ->asArray()
                ->all();
        }

        return array_column($rs, 'brand_name', 'brand_id');
    }

    public function getBrandBanner() {
        if (empty($this->touchBrand)) {
            return '';
        }
        return $this->touchBrand->brand_banner;
    }

    public function getBrandDetail() {
        if (empty($this->touchBrand)) {
            return '';
        }
        return TextHelper::formatRichText($this->touchBrand->brand_content);
    }

    public function getLicense() {
        if (empty($this->touchBrand)) {
            return '';
        }
        return TextHelper::formatRichText($this->touchBrand->license);
    }

    /**
     * 关联品牌资质表
     * @return \yii\db\ActiveQuery
     */
    public function getTouchBrand()
    {
        return $this->hasOne(TouchBrand::className(), ['brand_id' => 'brand_id']);
    }

    /**
     * 获取品牌关联的美妆知识库文章
     * @return $this
     */
    public function getTouchArticleList() {
        return $this->hasMany(TouchArticle::className(), [
            'brand_id' => 'brand_id',
        ])->onCondition([
            TouchArticle::tableName().'.cat_id' => 24,
            'is_open' => 1,
        ])->orderBy([
            'sort_order' => SORT_DESC,
            'article_id' => SORT_DESC,
        ]);
    }

    /**
     * 关联商品表
     * @return \yii\db\ActiveQuery
     */
    public function getGoods(){
        return $this->hasMany(Goods::className(),['brand_id' => 'brand_id']);
    }

    /**
     * 获取分成信息
     * @return \yii\db\ActiveQuery
     */
    public function getServicerStrategy() {
        return $this->hasOne(ServicerStrategy::className(), ['id' => 'servicer_strategy_id']);
    }

    /**
     * 获取供应商
     * @return \yii\db\ActiveQuery
     */
    public function getSupplierUser() {
        return $this->hasOne(Users::className(), ['user_id' => 'supplier_user_id']);
    }

    public function getBrandGoodsList() {
        return $this->hasMany(Goods::className(), [
            'brand_id' => 'brand_id',
        ])->onCondition([
            Goods::tableName().'.is_on_sale' => 1,
            Goods::tableName().'.is_delete' => 0,
        ]);
    }

    /**
     * 获取所有在微信端中间显示的商品列表块
     * @return $this
     */
    public function getBrandSpecCatList() {
        return $this->hasMany(BrandSpecGoodsCat::className(), [
            'brand_id' => 'brand_id',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => \common\behaviors\UploadImageBehavior::className(),
                'attribute' => 'brand_logo',
                'scenarios' => ['insert', 'update'],
                'path' => '@mRoot/data/attached/brand_logo/{brand_id}',
                'storePrefix' => 'data/attached/brand_logo/{brand_id}',
                'url' => Yii::$app->params['shop_config']['img_base_url'].'/brand_logo/{brand_id}',
                'thumbs' => [],
            ],
            [
                'class' => \common\behaviors\UploadImageBehavior::className(),
                'attribute' => 'brand_logo_two',
                'scenarios' => ['insert', 'update'],
                'path' => '@mRoot/data/attached/brand_logo_two/{brand_id}',
                'storePrefix' => 'data/attached/brand_logo_two/{brand_id}',
                'url' => Yii::$app->params['shop_config']['img_base_url'].'/brand_logo_two/{brand_id}',
                'thumbs' => [],
            ],
        ];
    }

    /**
     * @return mixed
     */
    public static function CharacterList()
    {
        $list = self::find()->where([
            'is_show' => Brand::IS_SHOW,
        ])->orderBy([
            'sort_order' => SORT_DESC
        ])->asArray()->all();

        foreach($list as $brand) {
            if(!empty($brand['character'])) {
                $country_icon = GoodsHelper::getCountryIcon($brand['country']);
                $characterBrandList[$brand['character']][] = [
                    'brand_id' => $brand['brand_id'],
                    'brand_name' => $brand['brand_name'],
                    'brand_logo' => ImageHelper::get_image_path($brand['brand_logo_two']),
                    'm_url' => '/default/brand/goods_list/id/'.$brand['brand_id'].'.html',
                    'country' => $country_icon
                ];
            }
        }
        //按 字母顺序排序
        ksort($characterBrandList);
        if(!empty($characterBrandList['#'])) {
            $spec['#'] = $characterBrandList['#'];
            unset($characterBrandList['#']);
            $list = array_merge($characterBrandList, $spec);
        }
        return $list;
    }

    /**
     * 用于派券
     * event_id ： 品牌参与优惠券活动的ID
     * 品牌设置了 event_id，
     * @return $this
     */
    public function getEvent() {
        $curDateTime = DateTimeHelper::getFormatDateTimeNow();
        Yii::warning('curDateTime = '. $curDateTime, __METHOD__);
        return $this->hasOne(Event::className(), ['event_id' => 'event_id'])
            ->onCondition([
                '<=',
                'start_time',
                $curDateTime,
            ])->andOnCondition([
                '>=',
                'end_time',
                $curDateTime,
            ])->andOnCondition([
                'is_active' => 1,
            ]);
    }

    /**
     * 品牌 与 活动 的关联关系
     */
    public function getEventToBrand()
    {
        return $this->hasMany(EventToBrand::className(), ['brand_id' => 'brand_id']);
    }

    /**
     * 品牌 与 活动 的关联关系
     */
    public function getShipping()
    {
        return $this->hasOne(Shipping::className(), ['shipping_id' => 'shipping_id']);
    }

    /**
     * 品牌 参与的 满减/优惠券活动  goods->brand—>eventToBrand->event
     */
    public function getEventList()
    {
        return $this->hasMany(Event::className(), ['event_id' => 'event_id'])
            ->via('eventToBrand');
    }

    /**
     * 品牌 与 一级分类 的关系，通过中间表连接
     * @return \yii\db\ActiveQuery
     */
    public function getBrandCat()
    {
        return $this->hasMany(BrandCat::className(),['brand_id' => 'brand_id']);
    }

    /**
     * brand->brandCat->category
     */
    public function getCatList()
    {
        return $this->hasMany(Category::className(),['cat_id' => 'cat_id'])
            ->via('brandCat');
    }

    /**
     * 关联增值政策表
     * @return \yii\db\ActiveQuery
     */
    public function getBrandPolicy() {
        return $this->hasMany(BrandPolicy::className(), ['brand_id' => 'brand_id'])
            ->onCondition([
                BrandPolicy::tableName().'.status' => BrandPolicy::STATUS_VALID,
            ]);
    }

    /**
     * 获取 指定地址 对应的本品牌的配送政策
     * @param $brandId
     * @param $supplierUserId
     * @param $address
     * @return array
     */
    public static function getShippingDesc($brandId, $supplierUserId, $address)
    {
        if (!empty($brandId)) {
            if (!empty($supplierUserId) && $supplierUserId == 1257) {
                $shippingId = Yii::$app->params['zhiFaDefaultShippingId'];
            } else {
                $brand = Brand::find()->select(['shipping_id'])->where(['brand_id' => $brandId])->one();

                if (!empty($brand)) {
                    //  如果品牌没有指定配送方式，则设置默认配送方式
                    if (empty($brand->shipping_id)) {
                        $brand->shipping_id = Yii::$app->params['default_shipping_id'];
                        $brand->save();
                    }
                    $shippingId = $brand->shipping_id;
                }
            }

            $shippingAreaInfo = ShippingArea::getShippingInfo($shippingId, $address);

            //  没有匹配到区域 则使用配送政策的名称，有匹配到区域，则使用区域名称
            if (empty($shippingAreaInfo->shipping_area_name)) {
                $shippingDesc = $shippingAreaInfo->shipping->shipping_name;
            } else {
                $shippingDesc = $shippingAreaInfo->shipping_area_name;
            }

            return [
                'code' => 0,
                'data' => [
                    'shippingDesc' => $shippingDesc
                ]
            ];
        } else {
            return [
                'code' => 1,
                'msg' => '非法请求！无效参数'    //  商品对应的品牌不存在
            ];
        }
    }

    /**
     * 处理 品牌绑定的 领券活动
     */
    public function assignCouponEvent($userId)
    {
        $getCouponList = [];
        if (!empty($this->event->fullCutRule)) {

            foreach ($this->event->fullCutRule as $rule) {
                $takenCount = $rule->getCouponCountTaken($userId);
                $limitCount = $this->event->times_limit;
                $ruleItem = [
                    'brandId' => $this->brand_id,
                    'eventId' => $this->event_id,
                    'eventName' => $this->event->event_name,
                    'eventDesc' => $this->event->event_desc,
                    'subType' => $this->event->sub_type,
                    'ruleId' => $rule->rule_id,
                    'ruleName' => $rule->rule_name,
                    'above' => intval($rule->above),
                    'cut' => intval($rule->cut),
                    'startTime' => DateTimeHelper::getFormatCNDate($this->event->start_time),
                    'endTime' => DateTimeHelper::getFormatCNDate($this->event->end_time),
                    'color' => $this->event->bgcolor,
                    'takenCount' => $takenCount,
                    'limitCount' => $limitCount,
                    'canTake' => $takenCount < $limitCount,
                ];
                $getCouponList[] = $ruleItem;
            }
        }

        return $getCouponList;
    }

    /**根据品牌id 获取商品数
     * @param $brand_id
     * @return int|string
     */
    public static function getBrandGoodsCount($brand_id) {
        return Goods::find()->where([
            'brand_id' => $brand_id,
            'is_on_sale' => Goods::IS_ON_SALE,
            'is_delete' => Goods::IS_NOT_DELETE,
        ])->asArray()->count();
    }

    /**
     * 获取所有的品牌地区名称
     */
    public static function getAllBrandAreaName()
    {
        return Brand::find()->select('brand_area')->where(['not', ['brand_area' => null]])
            ->andWhere(['not',['brand_area' => '']])
            ->andWhere([
                'is_show' => 1,
            ])
            ->asArray()
            ->groupBy('brand_area')
            ->orderBy(['brand_area' => SORT_DESC])
            ->all();
    }
}
