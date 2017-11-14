<?php

namespace common\models;

use common\helper\CacheHelper;
use common\helper\DateTimeHelper;
use common\helper\GoodsHelper;
use common\helper\ImageHelper;
use common\helper\NumberHelper;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "o_goods_activity".
 *
 * @property integer $act_id
 * @property string $act_name
 * @property string $act_desc
 * @property integer $act_type
 * @property integer $goods_id
 * @property integer $start_num
 * @property integer $limit_num
 * @property integer $match_num
 * @property string $old_price
 * @property string $act_price
 * @property string $production_date
 * @property string $show_banner
 * @property string $qr_code
 * @property string $note
 * @property integer $product_id
 * @property string $goods_name
 * @property string $goods_list
 * @property integer $start_time
 * @property integer $end_time
 * @property integer $is_hot
 * @property integer $is_finished
 * @property string $ext_info
 * @property integer $sort_order
 * @property integer $shipping_code
 * @property integer $order_expired_time
 * @property string $desc
 * @property integer $buy_by_box
 * @property integer $number_per_box
 * @property integer $biz_type
 *
 * @property array $extInfo
 */
class GoodsActivity extends \yii\db\ActiveRecord
{
    const ACT_TYPE_SNATCH       = 0;
    const ACT_TYPE_GROUP_BUY    = 1;
    const ACT_TYPE_GAT_AUCTION  = 2;
    const ACT_TYPE_FLASH_SALE   = 3;
    const ACT_TYPE_PACKAGE      = 4;

    const STATUS_PRE_START  = 0;
    const STATUS_UNDER_WAY  = 1;
    const STATUS_FINISHED   = 2;
    const STATUS_SETTLED    = 3;
    const STATUS_FAIL       = 4;

    const IS_HOT_NO     = 0;
    const IS_HOT_YES    = 1;

    const BIZ_TYPE_XMCP     = 0;
    const BIZ_TYPE_JOINT    = 1;

    //  活动类型
    public static $act_type_map = [
//        self::ACT_TYPE_SNATCH         => '夺宝奇兵',
        self::ACT_TYPE_GROUP_BUY      => '团采聚汇',
//        self::ACT_TYPE_GAT_AUCTION    => '拍卖',
        self::ACT_TYPE_FLASH_SALE     => '限量秒杀',
        self::ACT_TYPE_PACKAGE        => '超值礼包',
    ];

    //  活动状态
    public static $is_finished_map = [
        self::STATUS_PRE_START      => '未开始',
        self::STATUS_UNDER_WAY      => '进行中',
        self::STATUS_FINISHED       => '已结束',
        self::STATUS_SETTLED        => '已处理',
        self::STATUS_FAIL           => '活动失败',
    ];

    //  是否热门推荐（相当于置顶）
    public static $is_hot_map = [
        self::IS_HOT_NO     => '不推荐',
        self::IS_HOT_YES    => '推荐'
    ];

    //  是否热门推荐（相当于置顶）
    public static $buyByBoxMap = [
        0 => '不按箱',
        1 => '按箱',
    ];

    public static $extensionCodeActTypeMap = [
        OrderInfo::EXTENSION_CODE_GROUPBUY    => self::ACT_TYPE_GROUP_BUY,
        OrderInfo::EXTENSION_CODE_FLASHSALE   => self::ACT_TYPE_FLASH_SALE,
    ];

    //  活动类型对应的 exetension_code
    public static $actTypeExtensionCodeMap = [
        self::ACT_TYPE_GROUP_BUY    => OrderInfo::EXTENSION_CODE_GROUPBUY,
        self::ACT_TYPE_FLASH_SALE   => OrderInfo::EXTENSION_CODE_FLASHSALE,
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_goods_activity';
    }

    /**
     * 扩展信息 $extInfo = [
     *  'goodsAttrFormat'   => array    格式化的商品属性信息
     *  'pc_url'            => string   PC端的页面链接
     *  'wechat_url'        => string   微信端的页面链接
     * ]
     */
    public $extInfo;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'act_name', 'act_desc', 'act_type', 'goods_id', 'goods_name', 'start_time', 'buy_by_box', 'number_per_box',
                    'end_time', 'is_finished', 'ext_info', 'start_num', 'limit_num', 'match_num',
                    'old_price', 'act_price', 'production_date', 'shipping_code', 'order_expired_time'
                ],
                'required'
            ],
            ['show_banner', 'required', 'on' => 'insert'],
            [['act_desc', 'ext_info', 'note'], 'string'],
            [
                [
                    'act_type', 'goods_id', 'start_num', 'limit_num', 'match_num', 'product_id',
                    'is_hot', 'is_finished', 'sort_order', 'buy_by_box', 'number_per_box', 'biz_type',
                ],
                'integer'
            ],
            [['old_price', 'act_price'], 'number'],
            [['act_name', 'goods_name', 'sample'], 'string', 'max' => 255],
            ['desc', 'string', 'max' => 50],
            ['goods_list', 'default', 'value' => ''],
//            ['production_date', 'default', 'value' => '2018-01-01 00:00:00'],
            [
                ['show_banner', 'qr_code', 'goods_list'],
                'image',
                'extensions' => 'jpg, jpeg, gif, png',
                'on' => ['insert', 'update']
            ],
            ['sort_order', 'default', 'value' => 0],

            ['shipping_code', 'default', 'value' => 'fpd'],
            ['order_expired_time', 'default', 'value' => 172800],

            [['buy_by_box', 'number_per_box', 'start_num'], 'checkStartNum', 'message' => '请检查参与的活动的商品的起售数量'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'act_id' => '活动ID',
            'act_name' => '活动名称',
            'act_desc' => '活动描述',
            'act_type' => '活动类型',
            'goods_id' => '商品ID',
            'start_num' => '起售数量',
            'limit_num' => '限购数量',
            'match_num' => '达成数量',
            'old_price' => '原价',
            'note' => '备注',
            'act_price' => '活动价',
            'production_date' => '商品保质期至',
            'show_banner' => '展示图',
            'qr_code' => '二维码',
            'product_id' => 'Product ID',
            'goods_name' => '商品名称',
            'goods_list' => '商品列表(图)',
            'start_time' => '开始时间',
            'end_time' => '结束时间',
            'is_hot' => '热门推荐',
            'is_finished' => '状态',
            'ext_info' => '扩展信息',
            'sort_order' => '排序值',
            'shipping_code' => '配送方式',
            'order_expired_time' => '订单有效期(s)',
            'sample' => '物料配比', //  商品属性的物料配比只用于商品普通购买，团采/秒杀 活动的物料配比按活动的配置执行
            'desc' => '简短描述，显示在首页活动区',
            'buy_by_box' => '是否按箱购买',
            'number_per_box' => '发货箱规',
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => \mongosoft\file\UploadImageBehavior::className(),
                'attribute' => 'show_banner',
                'scenarios' => ['insert', 'update'],
                'path' => '@imgRoot/goods_activity/',
                'url' => Yii::$app->params['shop_config']['img_base_url'].'/goods_activity',
                'thumbs' => [],
            ],
            [
                'class' => \mongosoft\file\UploadImageBehavior::className(),
                'attribute' => 'qr_code',
                'scenarios' => ['insert', 'update'],
                'path' => '@imgRoot/goods_activity',
                'url' => Yii::$app->params['shop_config']['img_base_url'].'/goods_activity',
                'thumbs' => [],
            ],
            [
                'class' => \mongosoft\file\UploadImageBehavior::className(),
                'attribute' => 'goods_list',
                'scenarios' => ['insert', 'update'],
                'path' => '@imgRoot/goods_activity',
                'url' => Yii::$app->params['shop_config']['img_base_url'].'/goods_activity',
                'thumbs' => [],
            ]
        ];
    }

    /**
     * 校验起售数量
     */
    public function checkStartNum()
    {
        if ($this->act_type != self::ACT_TYPE_GROUP_BUY) {
        if (empty($this->start_num)) {
            $this->addError('start_num', '起售数量无效');
        }
        //  考虑创建的场景
        if (!empty($this->goods_id)) {
            $goods = Goods::find()
                ->select(['buy_by_box', 'number_per_box'])
                ->where(['goods_id' => $this->goods_id])
                ->one();
            if (!empty($goods)) {
                if ($this->buy_by_box) {
                    $mod = $this->start_num % $this->number_per_box;
                    if ($mod) {
                        $this->addError('start_num', '活动是按箱购买的，活动的起售数量应该是 活动【发货箱规】的整数倍');
                    }
                }
            } else {
                $this->addError('goods_id', '参与活动的商品不存在');
            }
        }
    }
    }

    /**
     *  获取活动信息详情—— 团采、秒杀 商品不参与 满减、优惠券、满赠
     * @param int $actId 活动ID
     * @param int $buyNum 购买数量
     * @param int $userId
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function getActivityInfo($actId, $buyNum = 0, $userId = 0)
    {
        $gmtNow = DateTimeHelper::getFormatGMTTimesTimestamp();
        $gaTb = self::tableName();
        $now = date('Y-m-d H:i:s');

        $activityInfo = self::find()
//            ->joinWith('comment')     //  评论暂时未启用
//            ->joinWith('bonus_type')  //  红包暂时未启用
//            ->joinWith('moq')         //  会员等级对应起订量 废弃
//            ->joinWith('member_price')//  会员等级对应价格 废弃
//            ->joinWith('volumePrice') //  团采不使用梯度价格
//            ->joinWith('category')    //  未使用
            ->joinWith([
                'goods',
                'goods.brand',
                'goods.brand.eventList brandEventList' => function($eventQuery) use ($now) {
                    $eventQuery->andOnCondition([
                            'brandEventList.is_active' => Event::IS_ACTIVE,
                            'brandEventList.event_type' => Event::EVENT_TYPE_WULIAO,    //  团采秒杀活动 可以配物料，不能配满赠、满减、优惠券
                        ])
                        ->andOnCondition([
                            'and',
                            ['<', 'brandEventList.start_time', $now],
                            ['>', 'brandEventList.end_time', $now]
                        ]);
                },
                'goods.eventList goodsEventList' => function($eventQuery) use ($now) {
                    $eventQuery->andOnCondition([
                        'goodsEventList.is_active' => Event::IS_ACTIVE,
                        'goodsEventList.event_type' => Event::EVENT_TYPE_WULIAO,    //  团采秒杀活动 可以配物料，不能配满赠、满减、优惠券
                    ])
                        ->andOnCondition([
                            'and',
                            ['<', 'goodsEventList.start_time', $now],
                            ['>', 'goodsEventList.end_time', $now]
                        ]);
                },

                'brand',
                'goods.goodsAttr',
                'shipping'
            ])
            ->where([
                $gaTb.'.act_id' => $actId,
            ])->andWhere([
                '>', $gaTb.'.end_time', $gmtNow
            ])->one();

        if (!empty($activityInfo)) {

            //  START 如果是团采活动，则修团采的 起售数量、箱规、是否按箱、销售价格、配送方式 参与活动的商品的配置信息
            if ($activityInfo->act_type == self::ACT_TYPE_GROUP_BUY) {
                $updateData = [
                    'start_num' => $activityInfo->goods->start_num,
                    'act_price' => $activityInfo->goods->shop_price,
                    'buy_by_box' => $activityInfo->goods->buy_by_box,
                    'number_per_box' => $activityInfo->goods->number_per_box,
                ];
                if ($activityInfo->goods->supplier_user_id == 1257) {
                    $updateData['shipping_code'] = Yii::$app->params['zhiFaDefaultShippingCode']; //  默认直发团采的配送方式为 小美直发满额包邮
                } else {
                    $shippingId = $activityInfo->goods->brand->shipping_id;
                    $shippingCode = Shipping::getShippingCodeById($shippingId);
                    $updateData['shipping_code'] = $shippingCode; //  默认非直发团采的配送方式为 到付
                }

                GoodsActivity::updateAll($updateData, ['act_id' => $activityInfo->act_id]);
                $activityInfo->setAttributes($updateData);
            }
            //  END 如果是团采活动，则修团采的 起售数量、箱规、是否按箱、销售价格、配送方式 参与活动的商品的配置信息

            //  修正商品单位
            $activityInfo->goods['measure_unit'] = $activityInfo->goods['measure_unit'] ?: '件';

            //  修正供应商ID
            if (empty($activityInfo->goods['supplier_user_id']) && !empty($activityInfo->brand['supplier_user_id'])) {
                $activityInfo->goods['supplier_user_id'] = $activityInfo->brand['supplier_user_id'];
            }

            //  修正商品的发货地和服务方
            $depotAreaAndService = Goods::resetDepotAreaAndService(
                $activityInfo->goods_id,
                $activityInfo->goods['supplier_user_id'],
                $activityInfo->brand['brand_depot_area']
            );
            $activityInfo->brand['brand_depot_area'] = $depotAreaAndService['brandDepotArea'];
            $activityInfo->extInfo['service'] = $depotAreaAndService['service'];

            //  修正图片路径
            $activityInfo->brand['brand_logo_two'] = ImageHelper::get_image_path($activityInfo->brand['brand_logo_two']);
            $activityInfo->qr_code = ImageHelper::getNewGoodsActivityImg($activityInfo->qr_code);
            $activityInfo->show_banner = ImageHelper::getNewGoodsActivityImg($activityInfo->show_banner);

            //  修正商品详情图片路径
            $img_base_url = CacheHelper::getShopConfigParams('IMG_BASE_URL');
            $goodsDescFormat = str_replace(
                "\"/data/attached/image",
                "\"".$img_base_url['value']."/image",
                $activityInfo->goods->goods_desc
            );
            $activityInfo->goods->goods_desc = $goodsDescFormat;
            $activityInfo->extInfo['goods_desc'] = $goodsDescFormat;
            //  处理商品水印图片   ——当前未启用水印

            //  格式化商品的属性信息
            if (!empty($activityInfo->goodsAttr)) {
                $activityInfo->extInfo['goodsAttrFormat'] = Goods::assignGoodsAttr($activityInfo['goods']['goodsAttr']);
            }

            //  格式化时间
            $endTimestampCn = DateTimeHelper::getFormatCNTimesTimestamp($activityInfo->end_time);
            $startTimestampCn = DateTimeHelper::getFormatCNTimesTimestamp($activityInfo->start_time);
            $activityInfo->extInfo['endTimestampCn'] = $endTimestampCn;
            $activityInfo->extInfo['startTimestampCn'] = $startTimestampCn;
            $activityInfo->extInfo['formatedStartDate'] = date('Y-m-d H:i', $startTimestampCn);
            $activityInfo->extInfo['formatedEndDate'] = date('Y-m-d H:i', $endTimestampCn);
            $activityInfo->extInfo['formatedEndTime'] = date('Y-m-d H:i:s', $endTimestampCn);

            //  处理url
            $activityInfo->extInfo['pc_url'] = '/group_buy.php?id='.$activityInfo->act_id;
            $activityInfo->extInfo['wechat_url'] = '/default/groupbuy/info/id/'.$activityInfo->act_id.'.html';

            //  格式化时间信息
            $activityInfo->production_date = substr($activityInfo->production_date, 0, 10);
            $start_time_cn = DateTimeHelper::getFormatCNTimesTimestamp($activityInfo->start_time);
            $activityInfo->extInfo['format_start_time'] = date('Y年m月d日', $start_time_cn);
            $end_time_cn = DateTimeHelper::getFormatCNTimesTimestamp($activityInfo->end_time);
            $activityInfo->extInfo['format_end_time'] = date('Y年m月d日', $end_time_cn);

            //  团采的扩展信息
            if (!empty($activityInfo->ext_info)) {
                $ext_info = unserialize($activityInfo->ext_info);
                $activityInfo->extInfo = array_merge($activityInfo->extInfo, $ext_info);
            }

            //  活动库存
            $activityInfo->extInfo['stock'] = $activityInfo->goods['goods_number'];
            $activityInfo->goods['start_num'] = $activityInfo->start_num;

            $extensionCode = self::$actTypeExtensionCodeMap[$activityInfo->act_type];
            $activityInfo->extInfo['extensionCode'] = $extensionCode;

            //  计算活动进度
            $saleCount = $activityInfo->getActivitySaleCount($extensionCode);
            $activityInfo->extInfo['saleCount'] = $saleCount;
//            if (!empty($activityInfo->match_num)) {
//                $activityInfo->extInfo['progress'] = round($saleCount / $activityInfo->match_num * 100);
//            }

            /**
             * 判定团采、秒杀状态
             * a)即将开始  ——当前时间【不在】活动时段内
             * b)进行中    ——当前时间【在】活动时段内 && 库存 > 0
             * c)已售罄    ——当前时间【在】活动时段内 && 库存 <= 0
             */
            if (
                $activityInfo->start_time < $gmtNow &&
                $activityInfo->end_time > $gmtNow &&
                $activityInfo->goods['goods_number'] >= $activityInfo->start_num
            ) {
                $activityInfo->extInfo['activityState'] = '正在进行中';
                $activityInfo->extInfo['statusDesc'] = 'onGoing';  //  进行中
                $activityInfo->extInfo['activityStateCode'] = 1;
                $activityInfo->extInfo['lastTime'] = DateTimeHelper::getFormatCNTimesTimestamp($activityInfo->end_time);
                $activityInfo->extInfo['canBuy'] = true;

                if (!empty(self::$actTypeExtensionCodeMap[$activityInfo->act_type])) {


                    //  团采的库存 取 （活动总库存 - 活动已付款数据） 与 商品实际库存 的最小值
                    if ($activityInfo->act_type == self::ACT_TYPE_FLASH_SALE) {
                        $activityInfo->extInfo['stock'] = min(
                            $activityInfo->goods['goods_number'],
                            $activityInfo->match_num - $saleCount
                        );

                        if ($activityInfo->extInfo['stock'] <= 0) {
                            $activityInfo->extInfo['stock'] = 0;
                            $activityInfo->extInfo['lastTime'] = 0;
                            $activityInfo->extInfo['canBuy'] = false;
                        }
                    }

                    $goodsMaxCanBuyArr = $activityInfo->getActivityGoodsMaxCanBuy($extensionCode, $userId, $buyNum);
                    if (!empty($goodsMaxCanBuyArr)) {
                        $activityInfo->extInfo['goodsMaxCanBuy'] = $goodsMaxCanBuyArr['goodsMaxCanBuy'];
                        $activityInfo->extInfo['limitMsg'] = $goodsMaxCanBuyArr['limitMsg'];
                        $activityInfo->extInfo['currentUserBuyCount'] = $goodsMaxCanBuyArr['currentUserBuyCount'];
                        $buyNum = $goodsMaxCanBuyArr['buyNum'];
                    }


                    //  秒杀商品要计算当前是否可以购买  考虑限购条件,修正最大可购买数量
                    if ($activityInfo->act_type == self::ACT_TYPE_FLASH_SALE) {
                        //如果当前要购买的数量和已销售数量超出了总数，就把剩下的给这个订单
                        if ($buyNum + $saleCount > $activityInfo->match_num) {
                            $buyNum = $activityInfo->match_num - $saleCount;
                        }
                    }
                }
                $activityInfo->extInfo['buyNum'] = $buyNum;
            }
            elseif ($activityInfo->end_time < $gmtNow || $activityInfo->goods['goods_number'] < $activityInfo->start_num) {
                $activityInfo->extInfo['activityState'] = '已结束';
                $activityInfo->extInfo['statusDesc'] = 'sellOut';  //  已售罄
                $activityInfo->extInfo['activityStateCode'] = 2;
                $activityInfo->extInfo['lastTime'] = 0;
                $activityInfo->extInfo['canBuy'] = false;
            } 
            elseif ($activityInfo->start_time > $gmtNow) {
                $activityInfo->extInfo['activityState'] = '即将开始';
                $activityInfo->extInfo['statusDesc'] = 'startEve';  //  即将开始
                $activityInfo->extInfo['activityStateCode'] = 3;
                $activityInfo->extInfo['lastTime'] = 0;
                $activityInfo->extInfo['canBuy'] = false;

                $activityInfo->extInfo['startTimeCn'] = DateTimeHelper::getFormatCNTimesTimestamp($activityInfo->start_time);
                $activityInfo->extInfo['activityState'] = date('m-d H:i', $activityInfo->extInfo['startTimeCn']);
            }

            if ($activityInfo->act_type == GoodsActivity::ACT_TYPE_GROUP_BUY) {
                $activityInfo->extInfo['actTypeName'] = '团采聚惠';
            } elseif ($activityInfo->act_type == GoodsActivity::ACT_TYPE_FLASH_SALE) {
                $activityInfo->extInfo['actTypeName'] = '秒杀';
            }


            if (isset($activityInfo->extInfo['goodsMaxCanBuy']) && isset($activityInfo->extInfo['stock'])) {
                $activityInfo->extInfo['goodsMaxCanBuy'] = min(
                    $activityInfo->extInfo['goodsMaxCanBuy'],
                    $activityInfo->extInfo['stock']
                );
            }

            //  物料配比    应在 修正当前购买数量后计算
            $wuliaoEventList = Goods::getWuliaoForActivityGoods($activityInfo->goods, $buyNum);
            $activityInfo->extInfo['wuliaoEventList'] = $wuliaoEventList;
        }

        return $activityInfo;
    }

    /**
     * 获取活动的销量
     * @param string $extensionCode in_array(['group_buy', 'flash_sale'])
     * @param int $userId
     * @return int|mixed
     */
    public function getActivitySaleCount($extensionCode, $userId = 0)
    {
        $oiTb = OrderInfo::tableName();
        $ogTb = OrderGoods::tableName();

        $query = OrderGoods::find()
            ->joinWith('orderInfo')
            ->andOnCondition(['goods_id' => $this->goods_id])
            ->where([
                'and',
                [$ogTb.'.extension_code' => $extensionCode],
                ['>', $oiTb.'.add_time', $this->start_time],
                ['<', $oiTb.'.add_time', $this->end_time],
            ]);

        //  秒杀的进度只计算已支付的订单，团采的进度计算所有订单
        if ($extensionCode == OrderInfo::EXTENSION_CODE_FLASHSALE) {
            $query->andOnCondition([
                $oiTb.'.pay_status' => OrderInfo::PAY_STATUS_PAYED
            ]);

            if ($userId) {
                $query->andWhere([$oiTb.'.user_id' => $userId]);
            }
        }

        $count = $query->sum(OrderGoods::tableName().'.goods_number');

        return $count > 0 ? $count : 0;
    }

    /**
     * 获取 团采/秒杀 活动的商品
     * @return \yii\db\ActiveQuery
     */
    public function getGoods()
    {
        return $this->hasOne(Goods::className(), ['goods_id' => 'goods_id']);
    }

    /**
     * 获取 团采/秒杀 活动的商品
     * @return \yii\db\ActiveQuery
     */
    public function getGoodsAttr()
    {
        return $this->hasOne(GoodsAttr::className(), ['goods_id' => 'goods_id'])
            ->via('goods');
    }

    /**
     * @return $this
     */
    public function getBrand()
    {
        return $this->hasOne(Brand::className(), ['brand_id' => 'brand_id'])
            ->via('goods');
    }


    /**
     * 获取 团采/秒杀 活动的订单
     * @return \yii\db\ActiveQuery
     */
    public function getOrderInfo()
    {
        return $this->hasMany(OrderInfo::className(), ['extension_id' => 'act_id']);
    }

    /**
     * 获取 团采/秒杀 活动的订单商品
     * @return \yii\db\ActiveQuery
     */
    public function getOrderGoods()
    {
        return $this->hasMany(OrderGoods::className(), ['order_id' => 'order_id'])
            ->via('orderInfo');
    }

    /**
     * 获取 团采/秒杀 活动的配送方式
     * @return \yii\db\ActiveQuery
     */
    public function getShipping()
    {
        return $this->hasOne(Shipping::className(), ['shipping_code' => 'shipping_code']);
    }

    /**
     * 判断活动谁可以访问
     * @param $activityInfo
     * @param $preview
     * @return int
     */
    public static function checkCanAccess($activityInfo, $preview)
    {
        $canAccess = true;
        $gmtNow = DateTimeHelper::getFormatGMTTimesTimestamp();

        //  没有获取到活动信息，返回首页
        if (empty($activityInfo)) {
            $canAccess = false;
        } elseif (!$preview) {
            //  非预览，已结束的活动，返回首页
            if ($activityInfo->is_finished > 1) {
                $canAccess = false;
            }
            //  非预览，不显示预告：不在团采时段内的活动，返回首页 ($activityInfo->end_time < $gmtNow || $activityInfo->start_time > $gmtNow)
            //  当前显示预告
            elseif ($activityInfo->act_type == GoodsActivity::ACT_TYPE_GROUP_BUY && $activityInfo->end_time < $gmtNow) {
                $canAccess = false;
            }
            //  非预览，已结束的秒杀活动，返回首页
            elseif ($activityInfo->act_type == GoodsActivity::ACT_TYPE_FLASH_SALE && $activityInfo->end_time < $gmtNow) {
                $canAccess = false;
            }
        }

        return $canAccess;
    }

    /**
     * 修正商品购买数量
     * @param int $buyNum 购买量 或 起订量
     * @param int $buyNum
     * @return float|int
     */
    public function resetBuyNum($buyNum)
    {
        Yii::warning(' 入参 act_id = '.$this->act_id.', start_num = '.$this->start_num.
            ' buy_by_box = '.$this->buy_by_box.', number_per_box = '.$this->number_per_box.
            ', $buyNum = '.$buyNum, __METHOD__);
        //  修正起订量
        $start_num = GoodsHelper::roundBoxNumber($this->start_num, $this->buy_by_box, $this->number_per_box);
        if ($start_num != $this->start_num) {
            $this->start_num = $start_num;
            if (!$this->save()) {
                Yii::warning(' 修正活动的的起订数量 入库失败 $this = '.json_encode($this), __METHOD__);
            }
        }

        //  库存不足
        if ($this->goods->goods_number < $this->start_num) {
            return 0;
        } else {
            //  不能低于起订量
            if ($buyNum < $this->start_num) {
                $buyNum = $this->start_num;
            }

            //  不能超过库存
            if ($buyNum > $this->goods->goods_number) {
                $buyNum = $this->goods->goods_number;
            }

            //  如果是按箱购买，修正为整箱数量
            if ($this->buy_by_box && $this->number_per_box > 0) {
                $buyNum = GoodsHelper::roundBoxNumber($buyNum, $this->buy_by_box, $this->number_per_box);
            }
            Yii::warning(' 起订数量 $buyNum = '.$buyNum.', 商品库存 $goods.goods_number = '.$this->goods->goods_number, __METHOD__);

            //  不能超过库存—— 只有按箱购买才会走到这一步
            if ($this->act_type == self::ACT_TYPE_GROUP_BUY && $buyNum > $this->limit_num) {
                $buyNum -= $this->number_per_box;
            }

            return $buyNum;
        }
    }

    /**
     * 计算团采、秒杀活动的 最大可购买数量 、 秒杀活动的已购买数量、 修正当前要购买的数量
     * 计算团采、秒杀活动的 最大可购买数量 、 秒杀活动的已购买数量、 修正当前要购买的数量
     * @param $extensionCode
     * @param $userId
     * @param int $buyNum
     * @return array
     */
    public function getActivityGoodsMaxCanBuy($extensionCode, $userId, $buyNum = 0)
    {
        $goodsMaxCanBuy = $this->limit_num;

        //  修正购买数量  没有传入购物数量则默认为起售数量
        if (empty($buyNum)) {
            $buyNum = $this->start_num;
        }

        $currentUserBuyCount = $this->getActivitySaleCount($extensionCode, $userId);
        //  秒杀商品要计算当前是否可以购买  考虑限购条件,修正最大可购买数量
        if ($this->act_type == self::ACT_TYPE_FLASH_SALE && $userId) {
            //  修正秒杀的限购数量   $this->limit_num = 0 表示不限购
            if (!empty($this->limit_num)) {
                if ($currentUserBuyCount == 0) {
                    //  单次购买量超出活动的限制数量
                    if ($buyNum > $this->limit_num) {
                        $buyNum = $this->limit_num;
                        $goodsMaxCanBuy = $this->limit_num;
                        $limitMsg = '本次活动的最大购买数量为'.$this->limit_num;
                    }
                } else{
                    //  多次购买量超出团购活动的限制数量
                    if ($currentUserBuyCount >= $this->limit_num) {
                        $goodsMaxCanBuy = 0;
                        $limitMsg = '对不起，您已购买过该活动的最大购买数量！';
                    } elseif ($buyNum + $currentUserBuyCount > $this->limit_num) {
                        $goodsMaxCanBuy = $this->limit_num - $currentUserBuyCount;
                        $buyNum = $goodsMaxCanBuy;
                        $limitMsg = '对不起，本次活动的最大购买数量为'.$this->extInfo['goodsMaxCanBuy'];
                    }
                }
            }

        }
        $buyNum = $this->resetBuyNum($buyNum);

        return [
            'goodsMaxCanBuy' => $goodsMaxCanBuy,
            'limitMsg' => $limitMsg,
            'currentUserBuyCount' => $currentUserBuyCount,
            'buyNum' => $buyNum,
        ];
    }

    /**
     * 获取当前生效的 团采、秒杀 活动对应的商品
     * @param array $type 默认 空数组，获取全部活动
     * @return array
     */
    public static function aliveActivityGoodsActMap($type = [])
    {
        $now = DateTimeHelper::gmtime();
        $activityQuery = self::find()
            ->select(['act_id', 'goods_id'])
            ->where(['<=', 'start_time', $now])
            ->andWhere(['>=', 'end_time', $now]);
        if (!empty($type)) {
            $activityQuery->andWhere(['act_type' => $type]);
        }
        $activity = $activityQuery->indexBy('goods_id')->all();

        if (!empty($activity)) {
            $list = ArrayHelper::getColumn($activity, 'act_id', true);

            $goodsIds = array_keys($list);
            GoodsTag::deleteAll(['tag_id' => 6]);
            foreach ($goodsIds as $goodsId) {
                $tagRecord = GoodsTag::find()
                    ->where([
                        'goods_id' => $goodsId,
                        'tag_id' => 6,  //  团采标
                    ])->one();

                if (empty($tagRecord)) {
                    $goodsTag = new GoodsTag();
                    $goodsTag->goods_id = $goodsId;
                    $goodsTag->tag_id = 6;
                    if (!$goodsTag->save()) {
                        Yii::warning('团采商品打标失败 goods_id = '.$goodsId, __METHOD__);
                    }
                }
            }
        } else {
            $list = [];
        }

        return $list;
    }

    /**
     * 获取当前生效的 团采活动的 商品、单词购买上限
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function aliveGroupBuyList()
    {
        $now = DateTimeHelper::gmtime();
        return self::find()
            ->select(['act_id', 'goods_id', 'limit_num'])
            ->where(['act_type' => self::ACT_TYPE_GROUP_BUY])
            ->andWhere(['<=', 'start_time', $now])
            ->andWhere(['>=', 'end_time', $now])
            ->indexBy('goods_id')
            ->asArray()
            ->all();
    }

    /**
     * 计算指定商品是否存在当前生效的团采活动
     */
    public static function aliveGroupBuyId($goodsId) {
        $groupBuyId = 0;
        $now = DateTimeHelper::gmtime();
        $act = self::find()
            ->select(['act_id'])
            ->where([
                'act_type' => self::ACT_TYPE_GROUP_BUY,
                'goods_id' => $goodsId
            ])->andWhere(['<=', 'start_time', $now])
            ->andWhere(['>=', 'end_time', $now])
            ->one();

        if (!empty($act)) {
            $groupBuyId = $act->act_id;
        }

        return $groupBuyId;
    }

    /**
     * 动态插入模板页面的团采进度
     * @param $arr
     * @return string
     */
    public static function insertActivityProgress($arr)
    {
        $progress = 0;
        if (!empty($arr['id'])) {
            $actId = intval($arr['id']);
            if (is_numeric($arr['id'])) {
                $activityProgress = self::getGroupBuyProgress($actId);
                if (!empty($activityProgress['group_progress'])) {
                    $progress = number_format($activityProgress['group_progress'], 0, '.', '');
                } else {
                    Yii::warning('活动进度获取失败，$progress = '.json_encode($progress), __METHOD__);
                }
            }
        } else {
            Yii::warning('错误请求，缺少必要参数。 $arr = '.json_encode($arr), __METHOD__);
        }

        return $progress;
    }

    /**
     * 计算团采活动的进度
     * @param $actId 团采活动ID
     * @return array
     */
    public static function getGroupBuyProgress($actId)
    {
        $saleCount = 0;
        $progress = 0;
        $restrictAmount = 0;

        $GoodsActivity = self::find()
            ->joinWith([
                'goods goods',
                'goods.tags'
            ])->where([
                'act_id' => $actId,
                'act_type' => [
                    self::ACT_TYPE_GROUP_BUY,
                    self::ACT_TYPE_FLASH_SALE
                ]
            ])->one();

        if (empty($GoodsActivity)) {
            Yii::warning('错误请求，活动不存在 actId = '.$actId, __METHOD__);
        } elseif ($GoodsActivity->match_num <= 0) {
            Yii::warning('活动配置不正确 actId = '.$actId.', match_num = '.$GoodsActivity->match_num, __METHOD__);
        } else {
            //  计算活动进度
            $restrictAmount = $GoodsActivity->match_num;
            $saleCount = $GoodsActivity->getActivitySaleCount('group_buy');
            if (!empty($GoodsActivity->match_num)) {
                $progress = NumberHelper::price_format($saleCount / $restrictAmount) * 100;
            }
        }

        return [
            'valid_goods' => $saleCount,
            'restrict_amount' => $restrictAmount,
            'group_progress' => $progress,
        ];
    }
}
