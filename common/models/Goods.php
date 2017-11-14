<?php

namespace common\models;

use common\behaviors\ListenGoodsNumberBehavior;
use common\behaviors\ChangeOnSaleStockBehavior;
use common\helper\CacheHelper;
use common\helper\ImageHelper;
use common\helper\NumberHelper;
use common\helper\OrderGroupHelper;
use Yii;
use yii\helpers\ArrayHelper;
use backend\models\GoodsCat;
use common\behaviors\UploadImageBehavior;
use common\helper\DateTimeHelper;
use common\helper\GoodsHelper;
use common\behaviors\RecordGoodsActionBehavior;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "o_goods".
 *
 * @property integer $goods_id
 * @property integer $cat_id
 * @property string $goods_sn
 * @property string $goods_name
 * @property string $goods_name_style
 * @property integer $click_count
 * @property integer $brand_id
 * @property string $provider_name
 * @property integer $goods_number
 * @property string $measure_unit
 * @property integer $number_per_box
 * @property string $goods_weight
 * @property string $market_price
 * @property string $shop_price
 * @property string $min_price
 * @property string $promote_price
 * @property string $promote_start_date
 * @property string $promote_end_date
 * @property integer $warn_number
 * @property string $keywords
 * @property string $goods_brief
 * @property string $goods_desc
 * @property string $goods_thumb
 * @property string $goods_img
 * @property string $original_img
 * @property integer $is_real
 * @property string $extension_code
 * @property integer $is_on_sale
 * @property integer $is_alone_sale
 * @property integer $is_shipping
 * @property string $integral
 * @property string $add_time
 * @property integer $sort_order
 * @property integer $is_delete
 * @property integer $is_best
 * @property integer $is_new
 * @property integer $is_hot
 * @property integer $is_spec
 * @property integer $is_promote
 * @property integer $bonus_type_id
 * @property string $last_update
 * @property integer $goods_type
 * @property string $seller_note
 * @property integer $give_integral
 * @property integer $rank_integral
 * @property integer $suppliers_id
 * @property integer $is_check
 * @property string $children
 * @property string $shelf_life
 * @property string $start_num
 * @property string $tags
 * @property integer $discount_disable
 * @property string $moqs
 * @property integer $shipping_id
 * @property integer $supplier_user_id
 * @property integer $buy_by_box
 * @property string $certificate
 * @property string $shipping_code
 * @property integer $complex_order
 * @property integer $need_rank
 * @property string $expire_date
 * @property string $prefix
 * @property integer $qty
 * @property integer $spu_id
 * @property string $sku_size
 * @property integer $sale_count
 * @property integer $base_sale_count
 * @property integer $biz_type
 *
 * @property array $extInfo
 */
class Goods extends \yii\db\ActiveRecord
{
    const IS_ON_SALE    = 1;
    const NOT_ON_SALE   = 0;

    const IS_DELETE     = 1;
    const IS_NOT_DELETE = 0;

    const DISCOUNT_ENABLE = 0;
    const DISCOUNT_DISABLE = 1;

    const IS_SHIPPING       = 1;  //  包邮
    const IS_NOT_SHIPPING   = 0;  //  不包邮

    const BUY_BY_SINGLE     = 0;
    const BUY_BY_BOX        = 1;    //  按箱购买

    const GENERAL           = 'general';
    const INTEGRAL_EXCHANGE = 'integral_exchange';

    const BIZ_TYPE_XMCP     = 0;
    const BIZ_TYPE_JOINT    = 1;

    public static $is_on_sale_map = [
        self::IS_ON_SALE    => '上架',
        self::NOT_ON_SALE   => '下架',
    ];

    public static $is_on_sale_icon_map = [
        self::IS_ON_SALE    => '<span style="color: green" class="glyphicon glyphicon-ok"></span>',
        self::NOT_ON_SALE   => '<span style="color: red" class="glyphicon glyphicon-remove"></span>',
    ];

    public static $buyByBoxMap = [
        self::BUY_BY_BOX    => '<span style="color: green">按箱</span>',
        self::BUY_BY_SINGLE => '<span style="color: red">不按箱</span>',
    ];

    public static $discount_enable_map = [
        self::DISCOUNT_DISABLE   => '不使用',
        self::DISCOUNT_ENABLE    => '使用',
    ];

    public static $buy_by_box_map = [
        self::BUY_BY_SINGLE     => '不按箱购买',
        self::BUY_BY_BOX        => '按箱购买',
    ];

    public static $is_delete_map = [
        self::IS_NOT_DELETE => '未删除',
        self::IS_DELETE     => '已删除',
    ];

    public static $is_delete_icon_map = [
        self::IS_NOT_DELETE => '<span style="color: green" class="glyphicon glyphicon-ok">未删除</span>',
        self::IS_DELETE     => '<span style="color: red" class="glyphicon glyphicon-remove">已删除</span>',
    ];

    public static $extensionCodeMap = [
        self::GENERAL           => '普通商品',  //  默认  RMB交易
        self::INTEGRAL_EXCHANGE => '积分兑换',  //  积分交易 2016年12月5日 当前只支持完全积分兑换，不支持 部分金额使用积分
    ];

    //  前缀映射 后面可用配置样式
    public static $prefixMap = [
        'NO' => '未配置',
        'ZF' => '直发',
        'XY' => '非卖品小样',
        'WL' => '非卖品物料',
        'JF' => '积分兑换',
    ];

    /**
     * 扩展信息 $extInfo = [
     *  'goods_price' => number   相对当前用户的购买价,
     *  'discount'          => number   相对折扣,
     *  'shippingInfo'      => array    配送方式信息,
     *  'goodsAttrFormat'   => array    商品属性信息
     *  'pc_url'            => string   PC端的页面链接
     *  'wechat_url'        => string   微信端的页面链接
     * ]
     */
    public $extInfo;

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            ChangeOnSaleStockBehavior::className(),
            RecordGoodsActionBehavior::className(),
            ListenGoodsNumberBehavior::className(),

            //  图片上传
            [
                'class' => UploadImageBehavior::className(),
                'attribute' => 'original_img',
                'scenarios' => ['insert', 'update'],
                'path' => '@imgRoot/goods-cover/{brand_id}/{goods_id}/',
                'storePrefix' => 'data/attached/goods-cover/{brand_id}/{goods_id}/',
                'url' => Yii::$app->params['shop_config']['img_base_url'].'/goods-cover/{brand_id}/{goods_id}',
                'thumbPath' => '@imgRoot/goods-cover/{brand_id}/{goods_id}/',
                //  thumb、preview 会出现在o_goods.goods_img、o_goods.goods_thumb字段中，不要改
                'thumbs' => [
                    'preview' => ['width' => 620, 'height' => 620, 'quality' => 100],
                    'thumb' => ['width' => 295, 'height' => 295, 'quality' => 100],
                ],
            ],
        ]); // TODO: Change the autogenerated stub
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            //  商品显示图和缩略图 路径修正
            if (!empty($this->original_img) && $this->isAttributeChanged('original_img')) {
                $pathParts = pathinfo($this->original_img);
                $dirname = $pathParts['dirname'];
                $basename = $pathParts['basename'];
                $this->goods_img    = $dirname.'/preview-'.$basename;
                $this->goods_thumb  = $dirname.'/thumb-'.$basename;
            }
            //  修正商品添加时间
            $now = DateTimeHelper::getFormatGMTTimesTimestamp();
            if (empty($this->add_time)) {
                $this->add_time = $now;
            }
            $this->last_update = $now;
            //  修正goods_sn
            if (empty($this->goods_sn)) {
                $this->goods_sn = GoodsHelper::makeGoodsSn();
            }

            $this->min_price = $this->shop_price;
            return true;
        } else {
            return false;
        }
        // TODO: Change the autogenerated stub
    }

    /**
     * 新创建的商品 首图中的godos_id 没有被替换掉
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        //  处理商品首图
        $search = '/0/';
        $replace = '/'.$this->goods_id.'/';
        if (!empty($this->original_img) && stripos($this->original_img, $search) !== false) {
            $this->original_img = str_replace($search, $replace, $this->original_img);
            $this->goods_img = str_replace($search, $replace, $this->goods_img);
            $this->goods_thumb = str_replace($search, $replace, $this->goods_thumb);

            $this->setScenario('update');
            $this->save();
        }

        parent::afterSave($insert, $changedAttributes);

        // TODO: Change the autogenerated stub
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_goods';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'goods_id', 'cat_id', 'click_count', 'brand_id', 'goods_number', 'number_per_box', 'spu_id',
                     'warn_number', 'is_real', 'is_on_sale', 'qty', 'sale_count', 'base_sale_count',
                    'is_alone_sale', 'is_shipping', 'add_time', 'sort_order', 'is_delete',
                    'is_best', 'is_new', 'is_hot', 'is_spec', 'is_promote', 'bonus_type_id', 'last_update',
                    'goods_type', 'give_integral', 'rank_integral', 'suppliers_id', 'is_check', 'buy_by_box',
                    'start_num', 'discount_disable', 'shipping_id', 'supplier_user_id', 'complex_order', 'need_rank', 'biz_type',
                ],
                'integer'
            ],
            [
                [
                    'goods_name', 'brand_id', 'cat_id', 'number_per_box', 'start_num',
                    'market_price', 'shop_price', 'goods_number'
                ],
                'required'
            ],  //  'shipping_id', 商品的配送方式暂时不用， 使用品牌的 和 活动的     , 'spu_id', 'sku_size'
            ['original_img', 'required', 'on' => 'insert'],
            [['goods_weight', 'market_price', 'shop_price', 'min_price', 'promote_price'], 'number'],
            [['goods_desc'], 'string'], //  text
            [['goods_sn', 'goods_name_style'], 'string', 'max' => 60],
            [['goods_name'], 'string', 'max' => 120],
            [['expire_date'], 'string', 'max' => 10],
            [['goods_number', 'sort_order'], 'integer', 'max' => 65535],

            [['provider_name', 'certificate'], 'string', 'max' => 100],
            [['measure_unit', 'shipping_code'], 'string', 'max' => 20],
            [['keywords', 'goods_brief', 'goods_thumb', 'goods_img', 'seller_note', 'children'],
                'string', 'max' => 255],
            [['extension_code'], 'string', 'max' => 30],
            [['shelf_life'], 'string', 'max' => 10],
            [['prefix'], 'string', 'max' => 4],

            [
                ['original_img'],   //  , 'goods_img', 'goods_thumb'
                'image',
                'extensions' => 'jpg, jpeg, gif, png',
                'on' => ['insert', 'update']
            ],

            ['spu_id',              'default', 'value' => 0],
            ['sku_size',            'default', 'value' => ''],
            ['sort_order',          'default', 'value' => 30000],
            ['measure_unit',        'default', 'value' => '件'],
            ['shipping_id',         'default', 'value' => 0],   //  Shipping::getDefaultShippingId() 暂时废弃 商品的特例配置
            ['supplier_user_id',    'default', 'value' => 0],
            ['extension_code',      'default', 'value' => 'general'],
            ['need_rank',           'default', 'value' => 1],
            ['is_alone_sale',       'default', 'value' => 1],

            [['buy_by_box', 'number_per_box', 'start_num'], 'checkStartNumber', 'on' => ['insert', 'update']],
            [['spu_id', 'sku_size'], 'checkSpu', 'on' => ['insert', 'update']],

            ['is_spec',             'default', 'value' => 0],
            ['is_best',             'default', 'value' => 0],
            ['is_new',              'default', 'value' => 0],
            ['is_hot',              'default', 'value' => 0],
            [['goods_sn'], 'filter', 'filter' => 'trim'],

            [
                [
                    'goods_id', 'cat_id', 'brand_id', 'goods_number', 'is_on_sale', 'is_shipping', 'is_delete',
                    'spu_id', 'click_count', 'is_check', 'buy_by_box','start_num', 'discount_disable',
                    'number_per_box', 'is_real', 'shipping_id', 'supplier_user_id', 'complex_order', 'need_rank',
                    'qty', 'base_sale_count', 'sort_order', 'is_hot', 'last_update',
                ],
                'filter',
                'filter' => 'intval'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'goods_id' => '商品ID',
            'cat_id' => '商品分类',
            'goods_sn' => '唯一货号',
            'goods_name' => '商品名称',
            'goods_name_style' => '商品名称的样式',
            'click_count' => '浏览量',
            'brand_id' => '所属品牌',
            'provider_name' => '供应商名称(未使用)',
            'goods_number' => '库存',
            'measure_unit' => '计件单位',
            'number_per_box' => '发货箱规',
            'goods_weight' => '商品重量(kg)',
            'market_price' => '市场价',
            'shop_price' => '本店售价', //  (第一梯度价格)
            'min_price' => '最低售价',
            'promote_price' => '促销价',
            'promote_start_date' => '活动开始',
            'promote_end_date' => '活动结束',
            'warn_number' => '库存报警数量',
            'keywords' => '关键词(,)',
            'goods_brief' => '简短描述',
            'goods_desc' => '详细描述',
            'goods_thumb' => '缩略图',
            'goods_img' => '商品大图',
            'original_img' => '商品原图',
            'is_real' => '	实物商品', //  1，是；0，否；比如虚拟卡就为0，不是实物
            'extension_code' => '交易类型',
            'is_on_sale' => '上架',
            'is_alone_sale' => '能单独销售',   //  1，是；0，否；如果不能单独销售，则只能作为某商品的配件或者赠品销售
            'is_shipping' => '包邮',
            'integral' => '可用积分',   //  购买该商品可以使用的积分数量，估计应该是用积分代替金额消费；但程序好像还没有实现该功能
            'add_time' => '添加时间',
            'sort_order' => '排序权重(0~65535)',
            'is_delete' => '删除状态',  //  0，否；1，已删除
            'is_hot' => '购物车推荐', //  是否热销   (is_hot)
            'is_promote' => '促销',
            'bonus_type_id' => '赠送的红包', //  购买该商品所能领到的红包类型
            'last_update' => '最近更新',
            'goods_type' => '商品类型',   //  商品所属类型id，取值表goods_type的cat_id
            'seller_note' => '平台备注', //  商品的商家备注，仅商家可见
            'give_integral' => '赠送积分', //  购买该商品时每笔成功交易赠送的积分数量
            'rank_integral' => '等级积分',
            'suppliers_id' => '供应商ID',
            'is_check' => '审核通过',
            'children' => '套装商品',   // 字符串，goods_id => goods_num
            'shelf_life' => '保质期',
            'servicer_strategy_id' => '服务商分成记录ID',
            'start_num' => '起售数量',   // 第一梯度数量 、最低销售数量
            'supplier_user_id' => '供应商ID',    //  拆单
            'shipping_id' => '运费模版',
            'buy_by_box' => '按箱购买',
            'discount_disable' => '等级折扣(默认1不使用)',
            'certificate' => '证件号',
            'shipping_code' => '运费code',
            'complex_order' => '综合排序值',
            'need_rank' => '需要等级',  //  [1,2,3]
            'expire_date' => '有效期至',    //  北京时间的 yyyy-mm-dd 格式

            'is_best' => '是否精品',    //  0，否；1，是  ——废弃
            'is_new' => '是否新品',     //  ——废弃
            'is_spec' => '每周特供',    //  ——废弃
            'goodsRegion' => '产地',
            'goodsSample' => '物料配比',
            'goodsEffect' => '功效',
            'prefix' => '条码前缀',
            'qty' => '装箱规格',
            'sale_count' => '销量',
            'base_sale_count' => '基础销量',

            'spu_id' => 'SPU_ID',
            'sku_size' => '规格',
        ];
    }

    /**
     * 自定义校验规则——起售数量和发货箱规
     */
    public function checkStartNumber()
    {
        if (empty($this->start_num)) {
            $this->addError('start_num' , '商品的起售数量不能为0');
        }

        if ($this->buy_by_box) {
            if (empty($this->number_per_box)) {
                $this->addError('number_per_box' , '按箱购买的商品 【发货箱规】不能为0');
            } else {
                $mod = $this->start_num % $this->number_per_box;
                if ($mod) {
                    $this->addError('start_num' , '按箱购买的商品，起售数量应该是【发货箱规】的整数倍');
                }
            }
        }

    }

    /**
     * 自定义校验规则——SPU关联
     * 有关联SPU，则商品规格必填
     * 只填写商品规格，不填写SPU可以正常保存
     */
    public function checkSpu()
    {
        if (!empty($this->spu_id)) {
            if (empty($this->sku_size)) {
                $this->addError('sku_size', '关联SPU一定要填写 商品规格');
            }
        }
    }

    /**
     * @inheritdoc
     * @return GoodsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new GoodsQuery(get_called_class());
    }

    /**
     * 获取商品单位
     * 优先用商品自己的计件单位，如果没有的话就用分类的，如果分类还没有就给个默认的‘件’
     * @param $measure_unit
     * @param int $goods_id
     * @return string
     */
    public static function getMeasureUnit($measure_unit, $goods_id = 0) {
        if (!$measure_unit && $goods_id) {
            $goods = self::find()->where(['goods_id' => $goods_id])->one();

            if ($goods) {
                $measure_unit = $goods->measure_unit;
            } else {
                return '';
            }
        }

        if ($measure_unit) {
            return $measure_unit;
        }  else {
            return '件';
        }
    }

    /**
     * 获取商品信息 —— 普通商品(不参与活动，积分兑换商品)
     * 【1】获取商品信息
     * 【2】修正商品信息，活动归类
     * 【3】处理优惠活动
     * @param int $goodsId
     * @param strint $extensionCode in_array(['general', 'integral_exchange'])
     * @param int $userId
     * @param int $goodsNum 购买数量
     * @param int $couponId 用户选择的优惠券ID
     * @return array|Goods|null
     */
    public static function getGoodsInfo($goodsId, $extensionCode, $userId, $goodsNum = 0, $couponId = 0)
    {
        Yii::warning('参数列表 $goodsId = '.$goodsId.' ; $extensionCode = '.$extensionCode.' ; $userId = '. $userId.
            ' ; $goodsNum ='. $goodsNum.' ; $couponId ='.$couponId, __METHOD__);
        //  【1】获取商品信息
        $now = date('Y-m-d H:i:s');
        $gTb = self::tableName();
        $goodsInfo = self::find()
//            ->joinWith('comment')     //  评论暂时未启用
//            ->joinWith('bonus_type')  //  红包暂时未启用
//            ->joinWith('moq')         //  会员等级对应起订量 废弃
//            ->joinWith('member_price')//  会员等级对应价格 废弃
            ->joinWith([
                'brand',
                'brand.event',  //  品牌绑定的活动 用于优惠券 派券领券
                'brand.event.fullCutRule brandGetCouponRules',  //  品牌绑定的活动 用于优惠券 派券领券
                'brand.eventList brandEventList' => function($eventQuery) use ($now) {
                    $eventQuery->andOnCondition(['brandEventList.is_active' => Event::IS_ACTIVE])
                        ->andOnCondition([
                            'and',
                            ['<', 'brandEventList.start_time', $now],
                            ['>', 'brandEventList.end_time', $now]
                        ]);
                },
                'volumePrice',
                'goodsAttr',
                'eventList goodsEventList' => function($eventQuery) use ($now) {
                    $eventQuery->andOnCondition(['goodsEventList.is_active' => Event::IS_ACTIVE])
                        ->andOnCondition([
                            'and',
                            ['<', 'goodsEventList.start_time', $now],
                            ['>', 'goodsEventList.end_time', $now]
                        ]);
                },
                'shipping',
                'giftPkgList giftPkgList' => function ($giftPkgListQuery) {
                    $giftPkgListQuery->andOnCondition(['giftPkgList.is_on_sale' => GiftPkg::IS_ON_SALE]);
                },
            ])->where([
                $gTb.'.goods_id' => $goodsId,
                $gTb.'.extension_code' => $extensionCode,
//                $gTb.'.is_on_sale' => self::IS_ON_SALE,   //  在前端做判断
//                $gTb.'.is_delete' => self::IS_NOT_DELETE  //  在前端做判断
            ])->one();
        Yii::warning('$goodsInfo = '.VarDumper::dumpAsString($goodsInfo), __METHOD__);

        //  【2】修正商品信息，活动归类
        if (!empty($goodsInfo)) {
            //  修正与订单计算相关商品信息
            $goodsInfo = self::formatGoodsInfoForBuy($goodsInfo, $extensionCode, $userId, $goodsNum);
            Yii::warning('$goodsInfo = '.VarDumper::dumpAsString($goodsInfo), __METHOD__);

            //  修正商品的发货地和服务方
            $depotAreaAndService = self::resetDepotAreaAndService(
                $goodsId,
                $goodsInfo->supplier_user_id,
                $goodsInfo->brand['brand_depot_area']
            );
            $goodsInfo->brand['brand_depot_area'] = $depotAreaAndService['brandDepotArea'];
            $goodsInfo->extInfo['service'] = $depotAreaAndService['service'];
            $goodsInfo->extInfo['sendBy'] = $depotAreaAndService['sendBy'];
            $goodsInfo->extInfo['brandGoodsCount'] = Brand::find()
                ->joinWith('brandGoodsList')
                ->where([
                    Brand::tableName().'.brand_id' => $goodsInfo->brand_id,
                    Goods::tableName().'.is_on_sale' => 1
                ])->count();
            //国家icon
            foreach ($goodsInfo['goodsAttr'] as $attribute) {
                if ($attribute['attr_id'] == 165) {
                    $goodsInfo->extInfo['countryIcon'] = GoodsHelper::getCountryIcon(trim($attribute['attr_value']));
                    break;
                }
            }

            //  修正图片路径
            $goodsInfo->brand['brand_logo_two'] = ImageHelper::get_image_path($goodsInfo->brand['brand_logo_two']);
            $goodsInfo->brand['brand_logo'] = ImageHelper::get_image_path($goodsInfo->brand['brand_logo']);
            $goodsInfo->brand['brand_policy'] = ImageHelper::get_image_path($goodsInfo->brand['brand_policy']);
            $goodsInfo->goods_img = ImageHelper::get_image_path($goodsInfo->goods_img);
            $goodsInfo->original_img = ImageHelper::get_image_path($goodsInfo->original_img);

            //  修正商品详情图片路径
            $img_base_url = CacheHelper::getShopConfigParams('IMG_BASE_URL');
            $goodsInfo->goods_desc = str_replace(
                "\"/data/attached/image",
                "\"".$img_base_url['value']."/image",
                $goodsInfo->goods_desc
            );

            //  处理商品水印图片   ——当前未启用水印
            //  修正促销价格      ——当前商品表的活动价格 和 活动时间 没有做处理，未启用

            //  修正商品关键词、简单描述
            $goodsInfo->keywords = htmlspecialchars($goodsInfo->keywords);
            $goodsInfo->goods_brief = htmlspecialchars($goodsInfo->goods_brief);

            //  如果是按箱购买，计算起售数量的箱数
            if ($goodsInfo->buy_by_box && !empty($goodsInfo->number_per_box)) {
                $goodsInfo->extInfo['box_number'] = floor($goodsInfo->start_num / $goodsInfo->number_per_box);
            }

            //  【3】处理优惠活动
            //  处理 满减 活动
            $fullCulRs = OrderGroupHelper::processFullCutEventList($goodsInfo->extInfo['fullCulEventList']);
            $goodsInfo->extInfo = array_merge($goodsInfo->extInfo, $fullCulRs);
            //  处理 优惠券 活动
            $couponRs = OrderGroupHelper::processCouponEventLIst($goodsInfo->extInfo['couponEventList'], $userId, $couponId);
            $goodsInfo->extInfo = array_merge($goodsInfo->extInfo, $couponRs);

            //  如果用户没有选择优惠券，按 优惠幅度最大的计算
            $goodsInfo->extInfo['remarks'] = '';
            $chooseMaxCut = OrderGroupHelper::chooseMaxCutEvent($goodsInfo->extInfo, $couponId);
            $goodsInfo->extInfo = array_merge($goodsInfo->extInfo, $chooseMaxCut);

            $goodsInfo->extInfo['shotBrandDescCat'] = '';
            $goodsInfo->extInfo['shotBrandDescCheckIn'] = '';
            if (!empty($goodsInfo->brand->short_brand_desc)) {
                $shotBrandDesc = explode('|', $goodsInfo->brand->short_brand_desc);
                $goodsInfo->extInfo['shotBrandDescCat'] = !empty($shotBrandDesc[0]) ? $shotBrandDesc[0] : '';
                $goodsInfo->extInfo['shotBrandDescCheckIn'] = !empty($shotBrandDesc[1]) ? $shotBrandDesc[1] : '';
            }

        }

        Yii::warning('返回值 $goodsInfo = '.VarDumper::dumpAsString($goodsInfo), __METHOD__);
        return $goodsInfo;
    }

    /**
     * 获取当前购买商品的 实际价格   ——对应 梯度价格 和 会员等级折扣
     * @param $buyNum
     * @param float $userRankDiscount   [0, 1]
     * @return array
     */
    public function getCurrentPrice($buyNum, $userRankDiscount)
    {
        $goods_price = $this->shop_price;
        $minPrice = $this->min_price;
        $userRankSavePrice = 0.00;  //   会员等级带来的优惠金额

        //  如果有梯度价格，计算匹配的最低价
        if (!empty($this->volumePrice)) {
            foreach ($this->volumePrice as $item) {
                if ($buyNum >= $item['volume_number'] && $goods_price > $item['volume_price']) {
                    $goods_price = $item['volume_price'];
                }

                if ($minPrice > $item['volume_price']) {
                    $minPrice = $item['volume_price'];
                }
            }
        }

        //  如果使用全局会员折扣，计算会员折扣，折算实际价格
        if (!$this->discount_disable) {
            $userRankSavePrice = $goods_price * (1 - $userRankDiscount);
            $goods_price *= $userRankDiscount;
            $minPrice *= $userRankDiscount;
        }

        $goods_price = NumberHelper::price_format($goods_price);
        $minPrice = NumberHelper::price_format($minPrice);

        return [
            'goods_price' => $goods_price,
            'min_price' => $minPrice,
            'userRankSavePrice' => $userRankSavePrice,
        ];
    }

    public static function resetDepotAreaAndService($goodsId, $supplierUserId, $brandDepotArea)
    {
        //  临时修正信息，商品的发货地和服务方 根据 供应商优先、品牌商其次 取值
        if ($supplierUserId == 1257 || in_array($goodsId, [2713, 2714, 2722, 2724, 2725])) {
            $brandDepotArea = '深圳';
            $service = '小美诚品发货并提供售后服务';
            $sendBy = '小美诚品';
        } else {
            $service = '品牌方发货并提供售后服务';
            $sendBy = '品牌方';

            //  处理特例，如 某个别SKU 和同品牌的商品属于不同的供应商
            if (in_array($goodsId, [2557, 2558])) {
                $brandDepotArea = '青岛';
            }
        }

        return [
            'brandDepotArea' => $brandDepotArea,
            'service' => $service,
            'sendBy' => $sendBy,
        ];
    }

    /**
     * 获取商品的品牌信息
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['cat_id' => 'cat_id']);
    }

    /**
     * 获取商品的品牌信息
     * @return \yii\db\ActiveQuery
     */
    public function getExtCategory()
    {
        return $this->hasMany(Category::className(), [
            'cat_id' => 'cat_id'
        ])->viaTable(\common\models\GoodsCat::tableName(), [
            'goods_id' => 'goods_id',
        ]);
    }

    /**
     * 获取商品对应的品牌信息
     * @return \yii\db\ActiveQuery
     */
    public function getBrand()
    {
        return $this->hasOne(Brand::className(), ['brand_id' => 'brand_id']);
    }

    /**
     * 获取商品的会员价
     * @return \yii\db\ActiveQuery
     */
    public function getMemberPrice() {
        return $this->hasMany(MemberPrice::className(), ['goods_id' => 'goods_id']);
    }

    /**
     * 获取阶梯价
     * @return \yii\db\ActiveQuery
     */
    public function getVolumePrice() {
        return $this->hasMany(VolumePrice::className(), ['goods_id' => 'goods_id']);
    }

    /**
     * 获取商品的所有标签
     * @return $this
     */
    public function getTags() {
        return $this->hasMany(Tags::className(), ['id' => 'tag_id'])
            ->viaTable(GoodsTag::tableName(), ['goods_id' => 'goods_id']);
    }

    /**
     * 获取商品指定标签
     * @return $this
     */
    public function getGoodsTag() {
        return $this->hasMany(GoodsTag::className(), ['goods_id' => 'goods_id']);
    }

    /**
     * 获取指定id标签的商品 新品
     * @return $this
     */
    public function getTagNew() {
        return $this->hasMany(Tags::className(), ['id' => 'tag_id'])
            ->viaTable(GoodsTag::tableName(), ['goods_id' => 'goods_id'], function ($query) {
                $query->andOnCondition([GoodsTag::tableName().'.tag_id' => 1]);
            });
    }

    /**
     * 获取指定id标签的商品 直供
     * @return $this
     */
    public function getTagSupply() {
        return $this->hasMany(Tags::className(), ['id' => 'tag_id'])
            ->viaTable(GoodsTag::tableName(), ['goods_id' => 'goods_id'], function ($query) {
                $query->andOnCondition([GoodsTag::tableName().'.tag_id' => 1]);
            });
    }

    /**
     * 获取指定id标签的商品 满赠
     * @return $this
     */
    public function getTagGift() {
        return $this->hasMany(Tags::className(), ['id' => 'tag_id'])
            ->viaTable(GoodsTag::tableName(), ['goods_id' => 'goods_id'], function ($query) {
                $query->andOnCondition([GoodsTag::tableName().'.tag_id' => 3]);
            });
    }

    /**
     * 获取指定id标签的商品 混批
     * @return $this
     */
    public function getTagMixUp() {
        return $this->hasMany(Tags::className(), ['id' => 'tag_id'])
            ->viaTable(GoodsTag::tableName(), ['goods_id' => 'goods_id'])
            ->andOnCondition([Tags::tableName().'.id' => 4]);
    }

    /**
     * 获取指定id标签的商品 明星单品
     * @return $this
     */
    public function getTagStar() {
        return $this->hasOne(Tags::className(), ['id' => 'tag_id'])
            ->viaTable(GoodsTag::tableName(), ['goods_id' => 'goods_id'], function ($query) {
                $query->onCondition([GoodsTag::tableName(). '.tag_id' => 5]);
            });
    }

    /**
     * 获取指定id标签的商品 团采
     * @return $this
     */
    public function getTagGroup() {
        return $this->hasMany(Tags::className(), ['id' => 'tag_id'])
            ->viaTable(GoodsTag::tableName(), ['goods_id' => 'goods_id'])
            ->andOnCondition([Tags::tableName().'.id' => 6]);
    }

    /**
     * 获取指定id标签的商品 团采
     * @return $this
     */
    public function getGroupBuy() {
        return $this->hasOne(GoodsActivity::className(), ['goods_id' => 'goods_id']);
    }

    /**
     * 获取商品关联的活动信息 团采、秒杀 等
     * @return \yii\db\ActiveQuery
     */
    public function getGoodsActivity()
    {
        return $this->hasOne(GoodsActivity::className(), ['goods_id' => 'goods_id']);
    }

    /**
     * 获取商品是否有指定标签
     * @return $this
     */
    public function getTagsById($tag_id) {
        return $this->hasMany(Tags::className(), ['id' => 'tag_id'])
            ->andOnCondition(['tag_id' => $tag_id])
            ->viaTable('o_goods_tag', ['goods_id' => 'goods_id']);
    }

    /**
     * 获取不同会员级别的moq
     * @return \yii\db\ActiveQuery
     */
    public function getMoqs() {
        return $this->hasMany(Moq::className(), ['goods_id' => 'goods_id']);
    }

    /**
     * 获取的商品对应的订单信息
     * @return \yii\db\ActiveQuery
     */
    public function getOrderGoods()
    {
        return $this->hasMany(OrderGoods::className(), ['goods_id' => 'goods_id']);
    }

    /**
     * 获取的商品对应的订单信息
     * @return \yii\db\ActiveQuery
     */
    public function getPaidOrderGoods()
    {
        return $this->hasMany(OrderGoods::className(), ['goods_id' => 'goods_id']);
    }

    /**
     * 获取商品属性信息
     * @return \yii\db\ActiveQuery
     */
    public function getGoodsAttr()
    {
        return $this->hasMany(GoodsAttr::className(), ['goods_id' => 'goods_id']);
    }

    /**
     * 产地
     * @return $this
     */
    public function getGoodsAttrRegion() {
        return $this->hasOne(GoodsAttr::className(), [
            'goods_id' => 'goods_id',
        ])->onCondition([
            'goodsAttrRegion.attr_id' => 165,
        ]);
    }

    public function getGoodsAttrRegionWithOutJoin() {
        return $this->hasOne(GoodsAttr::className(), [
            'goods_id' => 'goods_id',
        ])->onCondition([
            'attr_id' => 165,
        ]);
    }

    public function getGoodsRegion() {
        return $this['goodsAttrRegion']['attr_value'];
    }

    /**
     * 功效
     * @return $this
     */
    public function getGoodsAttrEffect() {
        return $this->hasOne(GoodsAttr::className(), [
            'goods_id' => 'goods_id',
        ])->onCondition([
            'goodsAttrEffect.attr_id' => 211,
        ]);
    }

    public function getGoodsEffect() {
        return $this['goodsAttrEffect']['attr_value'];
    }



    /**
     * 小样配比
     * @return $this
     */
    public function getGoodsAttrSample() {
        return $this->hasOne(GoodsAttr::className(), [
            'goods_id' => 'goods_id',
        ])->onCondition([
            'goodsAttrSample.attr_id' => 212,
        ]);
    }

    public function getGoodsSample() {
        return $this['goodsAttrSample']['attr_value'];
    }



    /**
     * 获取供应商
     * @return \yii\db\ActiveQuery
     */
    public function getSupplierUser() {
        return $this->hasOne(Users::className(), ['user_id' => 'supplier_user_id']);
    }

    /**
     * 获取配送方式
     * @return \yii\db\ActiveQuery
     */
    public function getShipping() {
        return $this->hasOne(Shipping::className(), ['shipping_id' => 'shipping_id']);
    }

    /**
     * 获取商品的轮播图（相册）
     * @return \yii\db\ActiveQuery
     */
    public function getGoodsGallery()
    {
        return $this->hasMany(GoodsGallery::className(), ['goods_id' => 'goods_id']);
    }

    /**
     * 慎用，
     * @return $this
     */
    public function getCart()
    {
        return $this->hasOne(Cart::className(), ['goods_id' => 'goods_id']);
    }

    /**
     * 关联商品的扩展分类
     * @return \yii\db\ActiveQuery
     */
    public function getGoodsCat()
    {
        return $this->hasMany(GoodsCat::className(), ['goods_id' => 'goods_id']);
    }

    /**
     * 关联商品的分成比例（对服务商）
     * @return \yii\db\ActiveQuery
     */
    public function getPercentTotal()
    {
        return $this->hasOne(ServicerStrategy::className(), ['id' => 'servicer_strategy_id']);
    }

    /**
     * 关联商品有 is_double 字段，双向关联 实际上有两条记录
     * @return \yii\db\ActiveQuery
     */
    public function getLinkGoods()
    {
        return $this->hasMany(
            LinkGoods::className(),
            ['goods_id' => 'goods_id']
        );
    }
    /**
     * 获取SKU参与的活动ID
     */
    public function getEventToGoods()
    {
        return $this->hasMany(EventToGoods::className(), ['goods_id' => 'goods_id']);
    }

    /**
     * 获取SKU参与的活动
     */
    public function getEventList()
    {
        return $this->hasMany(Event::className(), ['event_id' => 'event_id'])
            ->via('eventToGoods');
    }

    /**
     * 获取满赠活动 --    这里应该是一对多的关系 给调用方判断是否有效
     * @return $this
     */
    public function getManzengEvent() {
        $now = DateTimeHelper::getFormatDateTimeNow();

        return $this->hasOne(Event::className(), [
            'event_id' => 'event_id',
        ])->viaTable(EventToGoods::tableName(), [
            'goods_id' => 'goods_id',
        ])->onCondition([
            'and',
            [
                'event_type' => Event::EVENT_TYPE_FULL_GIFT,
                'is_active' => Event::IS_ACTIVE
            ],
            ['<', 'manzengEvent.start_time', $now],
            ['>', 'manzengEvent.end_time', $now],
        ]);
    }

    public function getServicerStrategy() {
        return $this->hasOne(ServicerStrategy::className(), [
            'id' => 'servicer_strategy_id',
        ]);
    }

    public function getSupplyInfo() {
        return $this->hasOne(GoodsSupplyInfo::className(), [
            'goods_id' => 'goods_id',
        ]);
    }

    public function getSupplyPrice() {
        if (empty($this->supplyInfo)) {
            return 0;
        }
        return $this->supplyInfo->supply_price;
    }

    public function getProfit() {
        if (empty($this->supplyInfo)) {
            return '0.00';
        }
        return NumberHelper::price_format($this->shop_price - $this->supplyInfo->supply_price);
    }

    public function getGuideGoods() {
        return $this->hasOne(GuideGoods::className(), [
            'goods_id' => 'goods_id',
        ]);
    }

    /**
     * 格式化 商品属性信息
     * @param array $goodsAttr
     * @return mixed
     */
    public static function assignGoodsAttr($goodsAttr)
    {
        Yii::warning('goodsAttr = '. VarDumper::dumpAsString($goodsAttr), __METHOD__);
        if (is_array($goodsAttr)) {
            foreach ($goodsAttr as $attr) {
                if ($attr['attr_id'] == 165) {
                    $goodsAttrFormat['product_area'] = $attr['attr_value'];
                } elseif ($attr['attr_id'] == 211) {
                    $goodsAttrFormat['effect'] = $attr['attr_value'];
                } elseif ($attr['attr_id'] == 212) {
                    $goodsAttrFormat['sample'] = $attr['attr_value'];
                }
            }
        }
        return $goodsAttrFormat;
    }

    /**
     * 修正商品购买数量
     * @param obj/array $goods
     * @param int $buyNum 购买量 或 起订量
     * @return float
     */
    public static function resetBuyNum($goods, $buyNum)
    {
        Yii::warning(' 入参 $goods = '.VarDumper::dumpAsString($goods).', $buyNum = '.$buyNum, __METHOD__);
        //  修正起订量
        $start_num = GoodsHelper::roundBoxNumber($goods['start_num'], $goods['buy_by_box'], $goods['number_per_box']);
        if ($start_num != $goods->start_num) {
            $goods->start_num = $start_num;
            if (!$goods->save()) {
                Yii::warning(' 修正商品的起订数量 入库失败 $goods = '.json_encode($goods), __METHOD__);
            }
        }

        //  库存不足
        if ($goods['goods_number'] < $goods['start_num']) {
            return 0;
        } else {
            //  不能超过库存
            if ($buyNum > $goods['goods_number']) {
                $buyNum = $goods['goods_number'];
            }
            //  不能低于起订量
            if ($buyNum < $goods['start_num']) {
                $buyNum = $goods['start_num'];
            }
            //  如果是按箱购买，修正为整箱数量
            if ($goods['buy_by_box'] && $goods['number_per_box'] > 0) {
                $buyNum = GoodsHelper::roundBoxNumber($buyNum, $goods['buy_by_box'], $goods['number_per_box']);
            }
            Yii::warning(' 起订数量 $buyNum = '.$buyNum.', 商品库存 $goods.goods_number = '.$goods['goods_number'], __METHOD__);

            //  不能超过库存—— 只有按箱购买才会走到这一步
            if ($buyNum > $goods['goods_number']) {
                $buyNum -= $goods['number_per_box'];
            }

            return $buyNum;
        }
    }

    /**
     * 修正与订单计算相关商品信息
     * 【1】修正购买数量
     * 【2】修正价格
     * 【3】修正商品名称、单位、图片路径、格式化商品的属性信息、修正供应商ID、配送方式
     * 【4】修正配送方式，商品没有配送方式就取用品牌的，商品、品牌都没配置配送方式则使用默认配送方式
     * 【5】归类处理商品参与的活动
     * 【6】如果有满赠活动 修正商品的最大可购买数量，价格、折扣
     * @param $goodsInfo
     * @param $extensionCode
     * @param $userId
     * @param $goodsNum
     * @return mixed
     */
    public static function formatGoodsInfoForBuy($goodsInfo, $extensionCode, $userId, $goodsNum)
    {
        //  【1】修正购买数量
        if ($goodsInfo->buy_by_box == 1 && $goodsInfo->number_per_box > 0) {
            $goodsInfo->start_num = GoodsHelper::roundBoxNumber(
                $goodsInfo->start_num,
                $goodsInfo->buy_by_box,
                $goodsInfo->number_per_box
            );
        }
        //  没有传入商品数量，使用起售数量
        if (empty($goodsNum)) {
            $goodsNum = min($goodsInfo->start_num, $goodsInfo->goods_number);
        } else {
            $goodsNum = self::resetBuyNum($goodsInfo, $goodsNum);
        }
        //  按箱购买的商品计算箱数
        if ($goodsInfo->buy_by_box && $goodsInfo->number_per_box > 0) {
            $goodsInfo->extInfo['box_num'] = (int)($goodsNum / $goodsInfo->number_per_box);
        }

        //  【2】修正价格    ——如果商品使用全局折扣，在这里就修正商品的售价，避免在商品梯度价格显示时出错
        $userRankDiscount = Users::getUserRankDiscount($userId);
        $currentPrice = $goodsInfo->getCurrentPrice($goodsNum, $userRankDiscount);
        $goodsInfo->extInfo['goods_price'] = $currentPrice['goods_price'];
        $goodsInfo->min_price = $currentPrice['min_price'];
//        $goodsInfo->shop_price = $currentPrice['goods_price'];
        $goodsInfo->formatVolumePriceListForBuy($userRankDiscount);

        //  【3】修正商品名称、单位、图片路径、格式化商品的属性信息、修正供应商ID、配送方式
        $goodsInfo->goods_name = trim($goodsInfo->goods_name);
        $goodsInfo->measure_unit = $goodsInfo->measure_unit ?: '件';
        $goodsInfo->goods_thumb = ImageHelper::get_image_path($goodsInfo->goods_thumb);
        if (!empty($goodsInfo->goodsAttr)) {
            $goodsInfo->extInfo['goodsAttrFormat'] = self::assignGoodsAttr($goodsInfo->goodsAttr);
        }
        if (empty($goodsInfo->supplier_user_id) && !empty($goodsInfo->brand['supplier_user_id'])) {
            $goodsInfo->supplier_user_id = $goodsInfo->brand['supplier_user_id'];
        }

        //  【4】修正配送方式，商品没有配送方式就取用品牌的，商品、品牌都没配置配送方式则使用默认配送方式
        if ($extensionCode == 'integral_exchange') {
            $goodsInfo->shipping_id = Yii::$app->params['default_shipping_id'];
        } else {
            if ($goodsInfo->supplier_user_id == 1257) {
                //  小美直发商品 配送方式为 分区域满额包邮  要改成直接读商品的配送方式
                $goodsInfo->shipping_id = Shipping::getShippingIdByCode(Yii::$app->params['zhiFaDefaultShippingCode']);
            } elseif (empty($goodsInfo->shipping_id)) {
                $goodsInfo->shipping_id = !empty($goodsInfo->brand['shipping_id'])
                    ? $goodsInfo->brand['shipping_id']
                    : Shipping::getDefaultShippingId();
            } else {
                $goodsInfo->shipping_id = Yii::$app->params['default_shipping_id'];
            }
        }


        $goodsInfo->extInfo['shippingInfo'] = Shipping::find()
            ->where(['shipping_id' => $goodsInfo->shipping_id])
            ->one();

        //  【5】归类处理商品参与的活动——只有普通商品才处理，积分兑换商品不处理活动信息
        $now = date('Y-m-d H:i:s');
        $fullCulEventList       = [];   //  满减
        $couponEventList        = [];   //  优惠券
        $fullGiftEventList      = [];   //  满赠
        $wuliaoEventList        = [];   //  物料
        $signCouponEventList    = [];   //  领券

        $now = date('Y-m-d H:i:s');
        $allGoodsEvent = Event::find()
            ->where([
                Event::tableName().'.is_active' => Event::IS_ACTIVE,
                'event_type' => [Event::EVENT_TYPE_FULL_CUT, Event::EVENT_TYPE_COUPON],
                'effective_scope_type' => EVENT::EFFECTIVE_SCOPE_TYPE_ALL,
            ])->andWhere([
                'and',
                ['<=', 'start_time', $now],
                ['>=', 'end_time', $now],
            ])->all();

        //  非直发商品不参与直发活动
        if ($goodsInfo->supplier_user_id == 1257) {
            $zhiFaEvent = Event::find()
                ->where([
                    Event::tableName().'.is_active' => Event::IS_ACTIVE,
                    'event_type' => [Event::EVENT_TYPE_FULL_CUT, Event::EVENT_TYPE_COUPON],
                    'effective_scope_type' => EVENT::EFFECTIVE_SCOPE_TYPE_ZHIFA,
                ])->andWhere([
                    'and',
                    ['<=', 'start_time', $now],
                    ['>=', 'end_time', $now],
                ])->all();
        } else {
            $zhiFaEvent = [];
        }


        if (
            $extensionCode == 'general' &&
            (
                !empty($goodsInfo->eventList) ||
                !empty($goodsInfo->brand->eventList) ||
                !empty($zhiFaEvent) ||
                !empty($allGoodsEvent)
            )
        ) {

            //  满减/优惠券 对应 全局、直发、eventToBrand、eventToGoods 四种模式
            $list = [
                $goodsInfo->eventList,          //  eventToGoods
                $goodsInfo->brand->eventList,   //  eventToBrand
                $allGoodsEvent,                 //  全局
                $zhiFaEvent                     //  直发
            ];
            $eventList = OrderGroupHelper::uniqueEventList($list);
            foreach ($eventList as $event) {
                switch ($event->event_type) {
                    case Event::EVENT_TYPE_FULL_GIFT:
                        if (!empty($event->fullGiftRule)) {
                            //  有 有效的赠品才做作为有效的活动
                            $giftList = OrderGroupHelper::getGiftInfo($goodsInfo, $event, $goodsNum);
                            if ($giftList) {
                                $fullGiftEventList[$event->event_id]['event'] = $event;
                                $fullGiftEventList[$event->event_id]['giftList'] = $giftList;//  array 支持多个赠品
                                $goodsInfo->extInfo['giftList'] = $giftList;
                            }
                        }
                        break;
                    case Event::EVENT_TYPE_FULL_CUT:
                        if (!empty($event->fullCutRule)) {
                            $fullCulEventList[$event->event_id]['event'] = $event;
                            $fullCulEventList[$event->event_id]['sumPrice'] = $goodsInfo->extInfo['goods_price'] * $goodsNum;
                            $fullCulEventList[$event->event_id]['eventToGoods'][] = $goodsInfo->goods_id;
                            $fullCulEventList[$event->event_id]['eventToGoodsSelected'][] = $goodsInfo->goods_id;
                        }
                        break;
                    case Event::EVENT_TYPE_COUPON:
                        if (!empty($event->fullCutRule)) {
                            $couponEventList[$event->event_id]['event'] = $event;
                            $couponEventList[$event->event_id]['sumPrice'] = $goodsInfo->extInfo['goods_price'] * $goodsNum;
                            $couponEventList[$event->event_id]['eventToGoods'][] = $goodsInfo->goods_id;
                            $couponEventList[$event->event_id]['eventToGoodsSelected'][] = $goodsInfo->goods_id;
                        }
                        break;
                    case Event::EVENT_TYPE_WULIAO:
                        if (!empty($event->fullGiftRule)) {
                            $wuliao = OrderGroupHelper::getGiftInfo($goodsInfo, $event, $goodsNum);
                            //  有 有效的物料才做作为有效的活动
                            if (!empty($wuliao)) {
                                $wuliaoEventList[$event->event_id]['event'] = $event;   //  array 支持多个物料活动
                                $wuliaoEventList[$event->event_id]['wuliaoList'] = $wuliao; //  array 支持多个物料
                                $goodsInfo->extInfo['wuliaoList'] = $wuliao;
                            }
                        }
                        break;
                    default :
                        break;
                }
            }
        }
        $goodsInfo->extInfo['fullGiftEventList'] = $fullGiftEventList;
        $goodsInfo->extInfo['fullCulEventList'] = $fullCulEventList;
        $goodsInfo->extInfo['couponEventList'] = $couponEventList;
        $goodsInfo->extInfo['wuliaoEventList'] = $wuliaoEventList;

        //  【6】如果有满赠活动 赠品，价格、折扣
        if (
            !empty($goodsInfo->extInfo['giftInfo']) &&
            $goodsInfo->extInfo['giftInfo']['goods_id'] == $goodsInfo->goods_id
        ) {
            if ($goodsInfo->buy_by_box && $goodsInfo->number_per_box > 0) {
                $goodsInfo->extInfo['box_num'] = floor($goodsNum / $goodsInfo->number_per_box);
            }
        }


        if (!empty($currentPrice['userRankSavePrice'])) {
            $goodsInfo->extInfo['userRankSavePrice'] = $currentPrice['userRankSavePrice'];
        }

        //  计算商品当前价格对应的折扣
        if ($goodsInfo->market_price) {
            $goodsInfo->extInfo['discount'] = round( ($goodsInfo->min_price / $goodsInfo->market_price * 100) / 10, 1);
        } else {
            $goodsInfo->extInfo['discount'] = 10;
        }

        //  普通商品的最大可购买数量就是库存， 满赠 赠完即止，赠品不足不影响购买
        $goodsInfo->extInfo['goodsMaxCanBuy'] = $goodsInfo->goods_number;

        //  拼接url
        if ($extensionCode == 'general') {
            $goodsInfo->extInfo['pc_url'] = '/goods.php?id='.$goodsInfo->goods_id;
            $goodsInfo->extInfo['wechat_url'] = '/default/goods/index/id/'.$goodsInfo->goods_id.'.html';
        } elseif ($extensionCode == 'integral_exchange') {
            $goodsInfo->extInfo['pc_url'] = '/goods.php?id='.$goodsInfo->goods_id;
            $goodsInfo->extInfo['wechat_url'] = '/default/exchange/info/id/'.$goodsInfo->goods_id.'.html';
        }

        $goodsInfo->extInfo['buyGoodsNum'] = $goodsNum;    //  购买数量

        return $goodsInfo;
    }

    /**
     *  为团采、秒杀 商品获取 物料配比信息
     */
    public static function getWuliaoForActivityGoods($goodsInfo, $goodsNum)
    {
        $wuliaoEventList = [];
        $now = date('Y-m-d H:i:s');

        $allGoodsEvent = Event::find()
            ->where([
                'is_active' => Event::IS_ACTIVE,
                'event_type' => [Event::EVENT_TYPE_FULL_CUT, Event::EVENT_TYPE_COUPON, Event::EVENT_TYPE_WULIAO],
                'effective_scope_type' => EVENT::EFFECTIVE_SCOPE_TYPE_ALL,
            ])->andWhere(['<', 'start_time', $now])
            ->andWhere(['>', 'end_time', $now])
            ->all();

        //  满减/优惠券 对应 全局、直发、eventToBrand、eventToGoods 四种模式
        $list = [
            $goodsInfo->eventList,    //  eventToGoods
            $goodsInfo->brand->eventList,      //  eventToBrand
            $allGoodsEvent,             //  全局
        ];
        //  非直发商品不参与直发活动
        if ($goodsInfo->supplier_user_id == 1257) {
            $zhiFaEvent = Event::find()
                ->where([
                    'is_active' => Event::IS_ACTIVE,
                    'event_type' => [Event::EVENT_TYPE_FULL_CUT, Event::EVENT_TYPE_COUPON, Event::EVENT_TYPE_WULIAO],
                    'effective_scope_type' => EVENT::EFFECTIVE_SCOPE_TYPE_ZHIFA,
                ])->andWhere(['<', 'start_time', $now])
                ->andWhere(['>', 'end_time', $now])
                ->all();
            $list[] = $zhiFaEvent;      //  直发
        }
        $eventList = OrderGroupHelper::uniqueEventList($list);

        foreach ($eventList as $event) {
            if (
                $event->event_type == Event::EVENT_TYPE_WULIAO
                && $event->is_active == Event::IS_ACTIVE
                && !empty($event->getFullGiftRule())
            ) {
                $wuliao = OrderGroupHelper::getGiftInfo($goodsInfo, $event, $goodsNum);
                if (!empty($wuliao)) {
                    $wuliaoEventList[$event->event_id]['event'] = $event;   //  array 支持多个物料活动
                    $wuliaoEventList[$event->event_id]['wuliaoList'] = $wuliao; //  array 支持多个物料
                }
            }
        }

        return $wuliaoEventList;
    }

    /**
     * 取得商品优惠价格列表
     * @param $userRankDiscount 会员等级折扣
     * @return array
     */
    public function formatVolumePriceListForBuy($userRankDiscount)
    {
        $volume_price_list = VolumePrice::sort_volume_price_list($this->volumePrice);
        $revise_discount = $this->discount_disable ? 1 : $userRankDiscount;

        $volume_price_list_for_buy = VolumePrice::volume_price_list_format(
            $volume_price_list,
            $this->shop_price * $revise_discount,
            $revise_discount,
            $this->start_num,
            $this->goods_number
        );
        $this->extInfo['volumePriceListForBuy'] = $volume_price_list_for_buy;
        //  梯度价格，至少保留三个位置， array_pad($volume_price_list_for_buy, 3, [])无效，因为 count($volume_price_list_for_buy) > 3
        $volume_price_list_for_buy[] = [];
        $volume_price_list_for_buy[] = [];
        $volume_price_list_for_buy[] = [];
        $volume_price_list_for_buy = array_slice($volume_price_list_for_buy, 0, 3);

        $marketPrice = NumberHelper::price_format($this->market_price);
        $this->extInfo['volumePriceListForBuyWithMarketPrice'] = array_merge(
            $volume_price_list_for_buy,
            [
                [
                    'range' => '零售价',
                    'price' => $marketPrice,
                    'format_price' => $marketPrice,
                ]
            ]
        );
    }

    public function getBackendName() {
        return '('. $this->goods_id. ')'. $this->goods_name. '('. $this->goods_sn. ')';
    }

    /**
     * 获取满赠配置
     * @return \yii\db\ActiveQuery
     */
    public function getActivityManzeng() {
        return $this->hasOne(ActivityManzeng::className(), [
            'goods_id' => 'goods_id',
        ]);
    }

    public function getArrivalReminder() {
        return $this->hasMany(ArrivalReminder::className(), [
            'goods_id' => 'goods_id',
        ]);
    }

    /**
     * 分配商品详情页的活动数据
     * @param $goods
     * @return array
     */
    public static function assignEvent($goods)
    {
        Yii::warning('入参 $goods = '.VarDumper::export($goods), __METHOD__);

        $fullCutEvent = [];
        $nowDateTime = date('Y-m-d H:i:s');
        if (!empty($goods->extInfo['fullCulEventList'])) {

            foreach ($goods->extInfo['fullCulEventList'] as $event) {
                $eventObj = $event['event'];
                //  先判定活动生效 并且 $fullCutEvent 没值（每种活动只获取一个）
                if (
                    $eventObj->is_active == Event::IS_ACTIVE &&
                    $eventObj->start_time < $nowDateTime &&
                    $eventObj->end_time > $nowDateTime &&
                    empty($fullCutEvent)
                ) {
                    $fullCutEvent = $eventObj;
                }
            }
        }

        return [
            'fullCutEvent' => $fullCutEvent,            //  满减活动只支持一组
            'giftEventList' => !empty($goods->extInfo['fullGiftEventList']) ? $goods->extInfo['fullGiftEventList'] : [],      //  满赠活动支持多个赠品
            'wuliaoEventList' => !empty($goods->extInfo['wuliaoEventList']) ? $goods->extInfo['wuliaoEventList'] : [],  //  物料支持多个
            'giftPkgList' => !empty($goods->giftPkgList) ? $goods->giftPkgList : [],    //  礼包活动， 可以有多个
        ];
    }

    public function getGoodsLockStockList() {
        return $this->hasMany(GoodsLockStock::className(), [
            'goods_id' => 'goods_id',
        ]);
    }

    public function getLockCount() {
        $count = 0;
        foreach ($this->goodsLockStockList as $lockStock) {
            $count += $lockStock['lock_num'];
        }
        return $count;
    }

    //获取参与的礼包活动
    public function getGiftPkgList() {
        return $this->hasMany(GiftPkg::className(), [
            'id' => 'gift_pkg_id',
        ])->viaTable(GiftPkgGoods::tableName(), [
            'goods_id' => 'goods_id',
        ]);
    }

    public function getAttrRegion() {
        if (!empty($this->goodsAttr)) {
            foreach ($this->goodsAttr as $attr) {
                if ($attr['attr_id'] == 165) {
                    return $attr['attr_value'];
                }
            }
        }
        return '';
    }

    /**
     * 获取商品的关联商品
     *
     * @param int $isOnSale 1 只获取上架的商品， 0 不限制
     * @param array $extensionCode  extension 的类型数组 ['general', 'integral_exchange']
     * @return array
     */
    public function getSpuGoodsList($userId, $isOnSale = 1, $extensionCode = ['general'])
    {
        $sortedSpuGoodsList = [];
        if (!empty($this->spu_id)) {
            $spuGoodsListQuery = self::find()
                ->joinWith([
                    'arrivalReminder' => function ($query) use ($userId) {
                        return $query->andOnCondition([
                            'user_id' => $userId,
                            'status' => ArrivalReminder::NOT_ARRIVAL,
                        ]);
                    }
                ])->where(['spu_id' => $this->spu_id]);
            if ($isOnSale) {
                $spuGoodsListQuery->andWhere([
                    'is_on_sale' => self::IS_ON_SALE,
                    'is_delete' => self::IS_NOT_DELETE
                ]);
            }
            if (!empty($extensionCode)) {
                $spuGoodsListQuery->andWhere(['extension_code' => $extensionCode]);
            }

            $spuGoodsList = $spuGoodsListQuery->all();

            $mainGoods = [];
            foreach ($spuGoodsList as $goods) {
                if ($goods->goods_id == $this->goods_id) {
                    $mainGoods = $goods;
                } else {
                    $sortedSpuGoodsList[] = $goods;
                }
            }

            array_unshift($sortedSpuGoodsList, $mainGoods);
        } else {
            $sortedSpuGoodsList[] = self::find()->alias('goods')->joinWith([
                'arrivalReminder' => function ($query) use ($userId) {
                    return $query->andOnCondition([
                        'user_id' => $userId,
                        'status' => ArrivalReminder::NOT_ARRIVAL,
                    ]);
                }
            ])->where(['goods.goods_id' => $this->goods_id])->one();
        }

        return $sortedSpuGoodsList;
    }

    /**
     * 关联SPU
     * @return \yii\db\ActiveQuery
     */
    public function getSpu()
    {
        return $this->hasOne(Spu::className(), ['id' => 'spu_id']);
    }

    /**
     * 不加入购物车的待结算商品
     * @param $goodsGroup
     * @return array
     */
    public static function checkGoodsList($goodsGroup, $userId)
    {
        $checkList = [];
        if (!empty($goodsGroup) && is_array($goodsGroup)) {
            foreach ($goodsGroup as $item) {
                if ($item['goods_id'] > 0 && $item['goods_number'] > 0) {
                    $checkList[$item['goods_id']] = $item['goods_number'];
                }
            }

            if (!empty($checkList)) {
                $goodsList = Goods::find()
                    ->select([
                        'goods_id', 'goods_sn', 'goods_name', 'market_price', 'shop_price', 'discount_disable',
                        'start_num', 'goods_number', 'buy_by_box', 'number_per_box', 'is_real'
                    ])->where([
                        'goods_id' => array_keys($checkList),
                        'is_on_sale' => Goods::IS_ON_SALE,
                        'is_delete' => Goods::IS_NOT_DELETE,
                    ])->all();

                if (!empty($goodsList)) {
                    $resetGoodsGroup = [];
                    $totalNum = 0;
                    $totalAmount = 0.00;

                    $userDiscount = Users::getUserRankDiscount($userId);
                    foreach ($goodsList as $goods) {
                        //  计算商品的最大可购买数量
                        if ($goods->buy_by_box && $goods->number_per_box > 0) {
                            $buyNumMax = floor($goods->goods_number / $goods->number_per_box) * $goods->number_per_box;
                        } else {
                            $buyNumMax = $goods->goods_number;
                        }

                        //  修正商品的购买数量
                        if ($goods->buy_by_box  && $goods->number_per_box > 0) {
                            $buyNumber = round($checkList[$goods->goods_id] / $goods->number_per_box) * $goods->number_per_box;
                        } else {
                            $buyNumber = $checkList[$goods->goods_id];
                        }

                        if ($buyNumber > $buyNumMax) {
                            $buyNumber = $buyNumMax;
                        }

                        //  修正商品价格
                        if ($goods->discount_disable) {
                            $goodsPrice = $goods->shop_price;
                        } else {
                            $goodsPrice = $goods->shop_price * $userDiscount;
                        }

                        $resetGoodsGroup[] = [
                            'goodsId' => $goods->goods_id,
                            'goodsNum' => $buyNumber,
                            'goodsPrice' => $goodsPrice,
                            'buyNumMax' => $buyNumMax,
                        ];
                        $totalNum += $buyNumber;
                        $totalAmount += $buyNumber * $goodsPrice;
                    }

                    $rs = [
                        'code' => 0,
                        'msg' => '',
                        'data' => [
                            'resetGoodsGroup' => $resetGoodsGroup,
                            'totalNum' => $totalNum,
                            'totalAmount' => NumberHelper::price_format($totalAmount),
                        ]
                    ];
                } else {
                    $rs = [
                        'code' => 0,
                        'msg' => '当前选中的商品都已下架',
                        'data' => []
                    ];
                }
            } else {
                $rs = [
                    'code' => 0,
                    'msg' => '当前没有商品被选中',
                    'data' => [
                        'goodsGroup' => [],
                        'totalNum' => 0,
                        'totalAmount' => 0.00
                    ]
                ];
            }
        } else {
            $rs = [
                'code' => 1,
                'msg' => '缺少必要参数',
                'data' => []
            ];
        }

        return $rs;
    }

    public function getArrayData($discount) {
        $user_discount = ($this['discount_disable'] == 1) ? 1 : $discount;
        $min_price = NumberHelper::price_format($this['min_price'] * $user_discount);
        $result = [
            'goods_id' => $this['goods_id'],
            'goods_name' => $this['goods_name'],
            'goods_thumb' => ImageHelper::get_image_path($this['goods_thumb']),
            'min_price' => $min_price,
            'start_num' => $this['start_num'],
            'sale_count' => $this['sale_count'],
            'goods_number' => $this['goods_number'],
        ];

        if (!empty($this['buy_by_box'])) {
            $result['box_num'] = $this['number_per_box'];
        }

        return $result;
    }
}
