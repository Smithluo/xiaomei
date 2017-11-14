<?php

namespace api\modules\v1\models;

use api\helper\OrderHelper;
use common\helper\DateTimeHelper;
use \Yii;
use api\modules\v1\models\EventToGoods;

use common\helper\TextHelper;
use common\helper\ImageHelper;
use common\helper\NumberHelper;
use common\helper\CacheHelper;
use api\modules\v1\models\Category;
use common\models\GoodsTag;
use brand\models\VolumePrice;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/8 0008
 * Time: 14:07
 */
class Goods extends \common\models\Goods
{
    public $discount;
    public $goods_price;
    public $gifts;
    public $volume_price_list_for_buy;
    public $start_total_price;  //  起订量对应的 总价
    public $user_rank_discount; //  会员等级对应的全局折扣
    public $revise_discount;    //  修正后的折扣
    public $url;                //  商品链接
    public $pc_url;             //  PC站商品链接
    public $m_url;              //  微信站商品链接
    public $show_tag_array;     //  要显示的标签数组(已按标签类型分组)
    public $showTagMap;         //  要显示的标签数组(未分组)
    public $show_tag_sort;      //  商品的标签权重总和
    public $discountPrice;


    /**
     * 格式化输出数据
     *
     * 商品名称只输出完整的，需要截取长度的在调用端处理
     *
     * @return array
     */
    public function fields()
    {
        return [
            'volumePrice',
            'category',
            'brand',
            'moqs',
            'goodsGallery',

            'discount_disable' => function($model){
                return (int)$model->discount_disable;
            },
            'user_rank_discount' => function ($model) {
                return $this->getUserRankDiscount();
            },  //  修正会员对应的等级价格
            'revise_discount' => function ($model) {
                return $this->reviseDiscount($model->discount_disable);
            },  //  修正当前商品对应会员等级的折扣

            'goods_id' => function($model){
                return (int)$model->goods_id;
            },
            'goods_name',
            'goods_sn',
            'goods_img' => function($model) {
                return ImageHelper::get_image_path($model->goods_img);
            },
            'goods_thumb' => function($model) {
                return ImageHelper::get_image_path($model->goods_thumb, true);
            },
            'original_img' => function($model) {
                return ImageHelper::get_image_path($model->original_img);
            },
            'goods_number' => function($model){
                return (int)$model->goods_number;
            },
            'start_num' => function($model){
                return $this->formatStartNum($model);
            },
            'shop_price' => function($model) {
                return $this->formatShopPrice($model->extension_code, $model->shop_price, $model->discount_disable);
            },
            'goods_price' => function($model) {
                return $this->getFormatGoodsPrice($model->shop_price, $model->discount_disable, $model->extension_code);
            },
            'market_price' => function($model) {
                return NumberHelper::price_format($model->market_price);
            },


            'min_price' => function() {
                return $this->formatMinPrice();
            },
            'discount' => function() {
                return $this->formatDiscount();
            },

            'measure_unit' => function($model){
                return $model->measure_unit ?: '件';
            },

            'sort_order',
            'complex_order',
            'tags',
            'buy_by_box' => function($model){
                return (int)$model->buy_by_box;
            },
            'number_per_box' => function($model){
                return (int)$model->number_per_box;
            },
            'supplier_user_id' => function($model){
                return (int)$model->supplier_user_id;
            },
            'need_rank' => function($model){
                return (int)$model->need_rank;
            },
            'extension_code',
            'need_rank',
            'goods_desc' => function ($model) {
                return TextHelper::formatRichText($model->goods_desc);
            },

            'goodsAttr' => function($model){
                return $this->formatGoodsAttr($model->goodsAttr);
            },

            'gifts',
            'groupBuy',
            'volume_price_list_for_buy' => function ($model) {
                return $this->formatVolumePriceListForBuy($model);
            },
            'start_total_price' => function($model){
                return $this->formatStartTotalPrice($model);
            },
            'url' => function($model){
                return $this->getPcUrl($model->goods_id, $model->extension_code);
            },
            'pc_url' => function($model){
                return $this->getPcUrl($model->goods_id, $model->extension_code);
            },
            'm_url' => function($model){
                return $this->getWechatUrl($model->goods_id, $model->extension_code);
            },

            'showTagMap' => function($model) {
                return $this->getShowTagArray($model->tags);
            },  //  处理标签的显示逻辑
            'sale_count' => function ($model) {
                return intval($model->sale_count);
            },
        ];
    }

    /**
     * 获取品牌 对应的 商品列表
     * @param int $brand_id
     * @return array
     */
    public static function getBrandGoodsMap($brand_id = 0)
    {
        $brand_goods_map = [];
        $g_tb = Goods::tableName();
        $query = Goods::find()->select(['goods_id', $g_tb.'.brand_id'])
            ->joinWith('brand')
            ->where([
                'is_on_sale' => Goods::IS_ON_SALE,
                'is_delete' => Goods::IS_NOT_DELETE,
            ]);

        if ($brand_id) {
            $query->andWhere(['brand_id' => $brand_id]);
        }

        $result = $query->all();
        if ($result && is_array($result)) {
            foreach ($result as $item) {
                $brand_goods_map[$item->brand_id]['goods_id'][] = $item->goods_id;
                $brand_goods_map[$item->brand_id]['name'] = $item->brand['brand_name'];
            }
        }

        return $brand_goods_map;
    }

    /**
     * 获取二级分类 对应的 商品列表
     * @param int $cat_id
     * @return array
     */
    public static function getCatGoodsMap($cat_id = 0)
    {
        $cat_goods_map = [];
        $cat_tb = Category::tableName();
        $query = Goods::find()->select(['goods_id', $cat_tb.'.cat_id'])
            ->joinWith('category')
            ->where([
                'is_on_sale' => Goods::IS_ON_SALE,
                'is_delete' => Goods::IS_NOT_DELETE,
            ]);

        if ($cat_id && is_numeric($cat_id)) {
            $query->andWhere(['cat_id' => $cat_id]);
        }

        $result = $query->all();
        if ($result && is_array($result)) {
            foreach ($result as $item) {
                $cat_goods_map[$item->cat_id]['goods_id'][] = $item->goods_id;
                $cat_goods_map[$item->cat_id]['name'] = $item->category['cat_name'];
            }
        }

        return $cat_goods_map;
    }

    /**
     * 获取活动/标签 对应的 商品列表
     * @param string $tag   标签不支持组合选择  ['new', 'star', 'gift', ...]
     * @return array
     */
    public static function getTagGoodsMap($tag = '')
    {
        $tag_goods_map = [];
        $gt_tb = GoodsTag::tableName();
        $g_tb = Goods::tableName();
        $query = Goods::find()
            ->joinWith('tags')
            ->where([
                $g_tb.'.is_on_sale' => Goods::IS_ON_SALE,
                $g_tb.'.is_delete' => Goods::IS_NOT_DELETE,
            ]);


        if ($tag != '') {
            $tag_map = Tags::$tag_name_map;
            $tag_id = $tag_map[$tag];
            $query->andWhere([$gt_tb.'.tag_id' => $tag_id]);
        }

        $result = $query->all();

        if ($result && is_array($result)) {
            foreach ($result as $item) {
                if (isset($item) && !empty($item->tags)) {
                    foreach ($item->tags as $tag) {
                        $tag_goods_map[$tag['id']]['goods_id'][] = $item->goods_id;
                        $tag_goods_map[$tag['id']]['name'] = $tag['name'];
                    }
                }
            }
        }

        return $tag_goods_map;
    }

    /**
     * 获取立即购买的商品信息
     *
     * @param $goodsId      立即购买的商品ID
     * @param $goodsNumber  立即购买的商品数量
     * @param $userRank     用户等级， 对应起售数量和 折扣
     *
     * @return array    [code = 0 | 1, 'goods_price' = string, gift= []]
     */
    public static function getGoodsForBuy($goodsId, $goodsNumber, $userRank)
    {
        $goods = Goods::find()
            ->joinWith('category')
            ->joinWith('brand')
            ->joinWith('moqs')
            ->joinWith('volumePrice')
            ->where([
                'o_goods.goods_id' => $goodsId,
                'o_goods.is_on_sale' => Goods::IS_ON_SALE,
                'o_goods.is_delete' => Goods::IS_NOT_DELETE,
            ])->one();

        if ($goods) {
            $gift = Event::getGiftForSingleGoods($goodsId, $goodsNumber);

            //  如果有moq 修正用户的起售数量
            if (!empty($goods['moqs'])) {
                foreach ($goods['moqs'] as $moq) {
                    if ($moq['user_rank'] == $userRank) {
                        $goods->start_num = $moq['moq'];
                        if ($goods->start_num > $goodsNumber) {
                            \Yii::trace(__CLASS__.' | '.__FUNCTION__.' | 当前商品的起售数量为'.$goods->start_num.' | goodsId:'.$goodsId.' | goodsNumber:'.$goodsNumber);
                            return [
                                'code' => 1,
                                'msg' => '当前商品的起售数量为'.$goods->start_num
                            ];   //  表示商品不可购买
                        }
                    }
                }
            }

            $brandId = 0;
            $brandName = '';
            if (!empty($goods['brand'])) {
                $brandId = $goods['brand']['brand_id'];
                $brandName = $goods['brand']['brand_name'];

                if (!$goods->shipping_id) {
                    $goods->shipping_id = $goods['brand']['shipping_id'];
                }
            }

            //  修正价格、折扣
            $goods_price = self::getGoodsPriceForBuy($goodsId, $goodsNumber, $userRank);
            Yii::trace('修正价格、折扣 $goodsId= '.$goodsId.', $goodsNumber = '.$goodsNumber.
                ', $userRank = '.$userRank.' $goods_price = '.$goods_price);

            $goods_total_price = bcmul($goods_price, $goodsNumber, 2);
            if ($goods->extension_code == self::INTEGRAL_EXCHANGE) {
                $goods_price = (int)$goods_price;
                $goods_total_price = (int)$goods_total_price;
            } else {
                $goods_total_price = NumberHelper::price_format($goods_total_price);
            }

            //  获取配送方式的名称
            $shipping_fee_format = OrderHelper::getShippingCode($goods);

            return [
                'code' => 0,
                'goods_id' => (int)$goodsId,
                'goods_sn' => $goods->goods_sn,
                'need_rank' => $goods->need_rank,
                'goods_name' => $goods->goods_name,
                'goods_number' => (int)$goodsNumber,
                'goods_price' => $goods_price,  //  计算优惠活动之前的结算价格
                'pay_price' => $goods_price,    //  计算优惠活动之后的实际购买价格
                'goods_thumb' => ImageHelper::get_image_path($goods->goods_thumb),  //  缩略图
                'gift' => $gift,  //  赠品
                'brand_id' => (int)$brandId,  //  品牌ID
                'brand_name' => $brandName,  //  品牌名称
                'supplier_user_id' => (int)$goods->supplier_user_id,  //  供应商ID
                'is_gift' => OrderGoods::IS_GIFT_NO,  //  是否赠品
                'shipping_id' => (int)$goods->shipping_id,  //  配送方式
                'goods_weight' => $goods->goods_weight,  //  配送方式
                'is_real' => (int)$goods->is_real,  //  是否是真实商品
                'shipping_code' => $goods->shipping_code,  //  运费code
                'market_price' => $goods->market_price,  //  市场价
                'shipping_fee_format' => $shipping_fee_format,
                'goods_number_max' => (int)$goods->goods_number,
                'goods_total_price' => $goods_total_price,
                'extension_code' => $goods->extension_code,
                'selected' => 1,     //  购物车中的状态是被勾选
            ];
        } else {
            \Yii::trace(__CLASS__.' | '.__FUNCTION__.' | 当前商品已下架 | goodsId:'.$goodsId.' | goodsNumber:'.$goodsNumber);
            return [
                'code' => 2,
                'msg' => '当前商品已下架'
            ];   //  表示商品不可购买
        }
    }

    /**
     * 获取商品团拼信息 用于直接购买
     *
     * 团拼  不考虑商品信息中的梯度价格 、会员等级对应的起售数量、全局等级折扣
     *      只考虑团拼的起售数量 梯度 和 商品信息的按箱购买
     *
     * @param $goodsId
     * @param $goodsNumber
     *        $userRank 用户等级对应的起售数量  在这里暂时不做校验，统一用活动里的商品起售数量
     * @return array
     */
    public static function getGroupGoodsForBuy($goodsId, $goodsNumber)
    {
        $goods = Goods::find()
            ->joinWith('groupBuy')
            ->joinWith('category')
            ->joinWith('brand')
            ->joinWith('moqs')
            ->joinWith('volumePrice')
            ->where(['o_goods.goods_id' => $goodsId])
            ->one();

        if ($goods) {
            if (empty($goods['groupBuy'])) {
                \Yii::trace(__CLASS__.' | '.__FUNCTION__.' | 当前商品不是团购商品 | goodsId:'.$goodsId.' | goodsNumber:'.$goodsNumber);
                return [
                    'code' => 1,
                    'msg' => '当前商品不是团购商品',
                ];
            }

            $brandId = 0;
            $brandName = '';
            if (!empty($goods['brand'])) {
                $brandId = $goods['brand']['brand_id'];
                $brandName = $goods['brand']['brand_name'];
                if (!$goods->shipping_id) {
                    $goods->shipping_id = $goods['brand']['shipping_id'];
                }
            }

            $gift = Event::getGiftForSingleGoods($goodsId, $goodsNumber);

            //  修正价格
            $goods_price = $goods['groupBuy']['act_price'];
            if (!empty($goods['groupBuy']['ext_info'])) {
                $extInfo = unserialize($goods['groupBuy']['ext_info']);
                if (!empty($extInfo['price_ladder'])) {

                    foreach ($extInfo['price_ladder'] as $item) {
                        if ($goodsNumber >= $item['amount'] && $goods_price > $item['price']) {
                            $goods_price = $item['price'];
                        }
                    }
                }
            }
            //  获取配送方式的名称
            $shipping_fee_format = OrderHelper::getShippingCode($goods);

            if ($goods->extension_code == self::INTEGRAL_EXCHANGE) {
                $goods_price = (int)$goods_price;
            }

            return [
                'code' => 0,
                'goods_id' => (int)$goodsId,
                'goods_sn' => $goods->goods_sn,
                'goods_name' => $goods->goods_name,
                'goods_number' => $goodsNumber,
                'goods_price' => $goods_price,  //  计算优惠活动之前的结算价格
                'pay_price' => $goods_price,    //  计算优惠活动之后的实际购买价格
                'goods_thumb' => ImageHelper::get_image_path($goods->goods_thumb),  //  缩略图
                'gift' => $gift,  //  赠品
                'brand_id' => (int)$brandId,  //  品牌ID
                'brand_name' => $brandName,  //  品牌名称
                'supplier_user_id' => (int)$goods->supplier_user_id,  //  供应商ID
                'is_gift' => OrderGoods::IS_GIFT_NO,  //  是否赠品
                'shipping_id' => (int)$goods->shipping_id,  //  配送方式
                'goods_weight' => $goods->goods_weight,  //  配送方式
                'is_real' => (int)$goods->is_real,  //  是否是真实商品
                'shipping_code' => $goods->shipping_code,  //  运费code
                'market_price' => $goods->market_price,  //  市场价
                'shipping_fee_format' => $shipping_fee_format,
                'goods_number_max' => (int)$goods->goods_number,
                'selected' => 1,     //  购物车中的状态是被勾选
            ];
        } else {
            \Yii::trace(__CLASS__.' | '.__FUNCTION__.' | 没要找到团购活动 | goodsId:'.$goodsId.' | goodsNumber:'.$goodsNumber);
            return [
                'code' => 1,
                'msg' => '参数错误',
            ];
        }
    }

    /**
     * 校验用户折扣
     * @param int $discount
     * @param int $discount_disable [0, 1]
     * @return int
     */
    public static function checkDiscount($discount, $discount_disable)
    {
        //  商品参与全局折扣 并且 有设置会员等级折扣才使用 会员折扣
        if ($discount_disable == 1) {
            $discount = 1;
        }
        //  修正传参，如果传入折扣为0，则修正为1，没有通过折扣免费的场景
        elseif (!empty($discount)) {
            $discount = 1;
        }

        return $discount;
    }

    /**
     * 格式化单个商品的信息--【废弃】--转入 fields中处理
     *
     * @param $goods    商品基础信息
     * @param $user_rank_discount   用户等级折扣
     * @return mixed
     */
    public static function formartGoodsInfo($goods, $user_rank_discount)
    {
        //  修正折扣
        $revise_discount = Goods::checkDiscount($user_rank_discount, $goods['discount_disable']);

        if ($goods['extension_code'] == self::INTEGRAL_EXCHANGE) {
            $goods['shop_price'] = (int)$goods['shop_price'];
        } else {
            $goods['shop_price'] = NumberHelper::price_format($goods['shop_price']);
        }

        // 取得商品优惠价格列表
        $volume_price_list = VolumePrice::sort_volume_price_list($goods['volumePrice']);
        $goods['volume_price_list_for_buy'] = VolumePrice::volume_price_list_format(
            $volume_price_list,
            $goods['shop_price'],
            $revise_discount,
            $goods['start_num'],
            $goods['goods_number']
        );
        $count = count($goods['volume_price_list_for_buy']);
        if ($count < 3) {
            array_pad($goods['volume_price_list_for_buy'], 3, []);
        }
        $goods['volume_price_list_for_buy'] = array_slice($goods['volume_price_list_for_buy'], 0, 3);

        //  修正图片
        $goods['goods_thumb'] = ImageHelper::get_image_path($goods['goods_thumb']);
        $goods['goods_img'] = ImageHelper::get_image_path($goods['goods_img']);
        $goods['original_img'] = ImageHelper::get_image_path($goods['original_img']);
        $goods['goods_desc'] = TextHelper::formatRichText($goods['goods_desc']);

        if ($goods['brand']) {
            $goods['brand']['brand_id'] = intval($goods['brand']['brand_id']);
            $goods['brand']['brand_logo'] = ImageHelper::get_image_path($goods['brand']['brand_logo']);
            $goods['brand']['brand_logo_two'] = ImageHelper::get_image_path($goods['brand']['brand_logo_two']);
            $goods['brand']['brand_policy'] = ImageHelper::get_image_path($goods['brand']['brand_policy']);
        }

        if (!empty($goods['goodsGallery'])) {
            foreach ($goods['goodsGallery'] as &$item) {
                unset($item['img_id']);
                unset($item['goods_id']);
                unset($item['img_original']);
                unset($item['img_desc']);

                $item['img_url'] = ImageHelper::get_image_path($item['img_url']);
                $item['thumb_url'] = ImageHelper::get_image_path($item['thumb_url']);
            }
        }

        $goodsAttr['region'] = '';
        $goodsAttr['effect'] = '';
        $goodsAttr['sample'] = '';
        if (!empty($goods['goodsAttr'])) {

            foreach ($goods['goodsAttr'] as $attr) {
                switch ($attr['attr_id']) {
                    case 165:
                        $goodsAttr['region'] = $attr['attr_value'];
                        break;
                    case 211:
                        $goodsAttr['effect'] = $attr['attr_value'];
                        break;
                    case 212:
                        $goodsAttr['sample'] = $attr['attr_value'];
                        break;
                    default :
                        break;
                }
            }
        }
        $goods['goodsAttr'] = $goodsAttr;

        return $goods;
    }

    /**
     * 格式化商品列表数据
     * @param $goods_result
     * @param $params
     * @return array
     */
    public static function formartGoodsList($goods_result, $params)
    {
        $user_rank_discount = !empty($params['user_rank_discount']) ? $params['user_rank_discount'] : 1;
        $user_rank = !empty($params['user_rank']) ? $params['user_rank'] : 1;
        $goods_list = [];
        if ($goods_result) {

            foreach ($goods_result as $goods) {
                if ($goods['market_price'] == 0) {
                    continue;
                }

                $goods['goods_id'] = intval($goods['goods_id']);

                $revise_discount = Goods::checkDiscount($user_rank_discount, $goods['discount_disable']);
                \Yii::trace('goods_id:'.$goods['goods_id'].', | discount_disable:'.$goods['discount_disable'].', | user_rank_discount:'.$user_rank_discount.', | revise_discount:'.$revise_discount);
                $goods['min_price'] *= $revise_discount;
                $goods['shop_price'] *= $revise_discount;

                if (!empty($goods['measure_unit'])) {
                    $goods['measure_unit'] = $goods['measure_unit'];
                } else {
                    $goods['measure_unit'] = '件';
                }

                //  格式化参数
                $goods['goods_thumb'] = ImageHelper::get_image_path($goods['goods_thumb'], true);
//                $goods['goods_img'] = ImageHelper::get_image_path($goods['goods_img'], false);
//                $goods['original_img'] = ImageHelper::get_image_path($goods['original_img'], false);
                $goods['market_price'] = NumberHelper::price_format($goods['market_price']);

                if ($goods['market_price']) {
                    $goods['discount'] = bcdiv($goods['min_price'], $goods['market_price'], 2) * 100 / 10;
                } else {
                    $goods['discount'] = 0.00;
                }

                $start_total_price = bcmul($goods['shop_price'], $goods['start_num'], 2);
                if ($goods['extension_code'] == self::INTEGRAL_EXCHANGE) {
                    $goods['shop_price'] = (int)$goods['shop_price'];
                    $goods['start_total_price'] = (int)$start_total_price;
                    $goods['min_price'] = (int)$goods['min_price'];
                } else {
                    $goods['shop_price'] = NumberHelper::price_format($goods['shop_price']);
                    $goods['start_total_price'] = NumberHelper::price_format($start_total_price);
                    $goods['min_price'] = NumberHelper::price_format($goods['min_price']);
                }

                if ($goods['extension_code'] == 'integral_exchange') {
                    $goods['url'] = 'exchange.php?act=info&id='.$goods['goods_id'];
                    $goods['pc_url'] = $goods['url'];
                    $goods['m_url'] = '/default/exchange/info/id/'.$goods['goods_id'].'.html';
                } else {
                    $goods['url'] = 'goods.php?id='.$goods['goods_id'];
                    $goods['pc_url'] = $goods['url'];
                    $goods['m_url'] = '/default/goods/index/id/'.$goods['goods_id'].'.html';
                }

                if(!empty($goods['moqs']) && isset($user_rank) && $user_rank > 0) {
                    foreach($goods['moqs'] as $moq) {
                        if($moq['user_rank'] == $user_rank) {
                            if(isset($moq['moq']) && $moq['moq'] > 0) {
                                $goods['start_num'] = $moq['moq'];
                            }
                            break;
                        }
                    }
                }

                //  截字
                if (!empty($params['goods_name_length'])) {
                    $goods['short_name'] = $params['goods_name_length'] > 0
                        ? TextHelper::sub_str($goods['goods_name'], $params['goods_name_length'])
                        : $goods['goods_name'];
                }

                // 取得商品优惠价格列表
                if (!empty($goods['volumePrice'])) {
                    $volume_price_list = VolumePrice::sort_volume_price_list($goods['volumePrice']);
                }
                else {
                    $volume_price_list = [];
                }
                $goods['volume_price_list_for_buy'] = VolumePrice::volume_price_list_format(
                    $volume_price_list,
                    $goods['shop_price'],
                    $revise_discount,
                    $goods['start_num'],
                    $goods['goods_number']
                );

                $count = count($goods['volume_price_list_for_buy']);
                if ($count < 3) {
                    array_push($goods['volume_price_list_for_buy'], [], [], []);
                }
                $goods['volume_price_list_for_buy'] = array_slice($goods['volume_price_list_for_buy'], 0, 3);

                //  处理标签的显示逻辑
                $show_tag_array = [];
                $showTagMap = [];
                $goods['show_tag_sort'] = 0;
                if ((!isset($goods['tags']) || !$goods['tags']) && isset($goods['tagsOnly']) && $goods['tagsOnly']) {
                    $goods['tags'] = $goods['tagsOnly'];
                }
                if (isset($goods['tags']) && $goods['tags']) {
                    usort($goods['tags'], function ($a, $b){
                        if ($a['sort'] == $b['sort']) {
                            return 0;
                        } else {
                            return $a['sort'] > $b['sort'] ? -1 : 1;
                        }
                    });
                    $showTagRs = Tags::formartTags($goods['tags']);
                    $show_tag_array = $showTagRs['show_tag_array'];
                    $showTagMap = $showTagRs['showTagMap'];

                    foreach ($show_tag_array as $arr) {
                        $goods['show_tag_sort'] += array_sum(array_column($arr, 'sort'));
                    }
                }
                $goods['show_tag_array']    = $show_tag_array;
                $goods['showTagMap']        = $showTagMap;

                //  数据格式化
                $goods['discount'] = NumberHelper::discount_format($goods['discount']);

                if ($goods && !empty($goods['discount'])) {
                    $goods_list[] = (array)$goods;
                }
                \Yii::trace('goods:'.json_encode((array)$goods));
            }

        }

        return $goods_list;
    }

    /**
     * 批量获取商品的缩略图
     *
     * @param $goodsIdList
     * @return array
     */
    public static function getThumbMap($goodsIdList)
    {
        $goodsInfo = Goods::find()->select(['goods_id', 'goods_thumb'])
            ->where(['goods_id' => $goodsIdList])
            ->asArray()
            ->all();
        $goodsThumbMap = array_column($goodsInfo, 'goods_thumb', 'goods_id');

        return $goodsThumbMap;
    }

    /**
     * 根据用户等级获取商品价格
     * @param $id   商品ID
     * @param $num  购买数量
     * @param $userRank 会员等级
     * @return string   价格
     */
    public static function getGoodsPriceForBuy($id, $num, $userRank)
    {
        $goods = self::find()
            ->joinWith('volumePrice')
            ->where(['o_goods.goods_id' => $id])
            ->one();

        if ($goods) {
            $price = $goods->shop_price;

            if (!empty($goods['volumePrice'])) {
                $volumePrice = $goods['volumePrice'];

                foreach ($volumePrice as $item) {
                    if ($num >= $item['volume_number'] && $price > $item['volume_price']) {
                        $price = $item['volume_price'];
                    }
                }
            }

            if ($userRank > 1 && !$goods->discount_disable) {
                $userRankMap = CacheHelper::getUserRankCache();
                foreach ($userRankMap as $rank) {
                    if ($rank['rank_id'] == $userRank) {
                        $price = $price * $rank['discount'] / 100;
                    }
                }
            }

            return NumberHelper::price_format($price);
        } else {
            return 0;
        }

    }

    public function getCart()
    {
        return $this->hasOne(Cart::className(), ['goods_id' => 'goods_id'])
            ->andOnCondition(['user_id' => \Yii::$app->user->identity->user_id]);
    }

    public function getTags()
    {
        return $this->hasMany(Tags::className(), ['id' => 'tag_id'])
            ->viaTable(GoodsTag::tableName(), ['goods_id' => 'goods_id']);
    }

    public function getExtCategory()
    {
        return $this->hasMany(Category::className(), [
            'cat_id' => 'cat_id'
        ])->viaTable(GoodsCat::tableName(), [
            'goods_id' => 'goods_id',
        ]);
    }

    /**
     * 格式化商品价格
     * @param $extension_code
     * @param $shop_price
     * @return int|string
     */
    public function formatShopPrice($extension_code, $shop_price, $discount_disable)
    {
        if ($extension_code == self::INTEGRAL_EXCHANGE) {
            $rs = (int)$shop_price;
        } else {
            //  如果商品使用全局折扣，在这里就修正商品的售价，避免在商品梯度价格显示时出错
            $revise_discount = $this->reviseDiscount($discount_disable);
            $shop_price *= $revise_discount;
            $rs = NumberHelper::price_format($shop_price);
        }

        return $rs;
    }

    /**
     * 获取用户等级对应的折扣
     * @return float|int
     */
    public function getUserRankDiscount()
    {
        $discount = 1.0;
        if (isset(Yii::$app->user->identity)) {
            $userRank = Yii::$app->user->identity['user_rank'];
            $user_rank_map = CacheHelper::getUserRankCache();

            $discount = $user_rank_map[$userRank]['discount'] / 100.0;
        }

        return $discount;
    }

    /**
     * 修正起售数量
     * @param $model
     * @return int
     */
    public function formatStartNum($model)
    {
        if(!empty($model->moqs)) {
            $token = Yii::$app->request->getAuthUser();
            if (!empty($token)) {
                $user = Users::findIdentityByAccessToken($token);

                if (!empty($user->user_rank)) {
                    foreach($model->moqs as $moq) {
                        if($moq['user_rank'] == $user->user_rank) {
                            if(isset($moq['moq']) && $moq['moq'] > 0) {
                                return (int)$moq['moq'];
                            }
                        }
                    }
                }
            }
        }

        return (int)$model->start_num;
    }

    /**
     * 获取商品价格
     * @param $shop_price
     * @param $discount_disable
     * @param $extension_code
     * @return string
     */
    public function getFormatGoodsPrice($shop_price, $discount_disable, $extension_code)
    {
        //  部分商品不使用全局会员等级折扣，需要先修正全局折扣，再计算当前用户对应的商品价格
//        $user_rank_discount = $this->getUserRankDiscount();
//        $revise_discount = Goods::checkDiscount($user_rank_discount, $discount_disable);
//        $shopPrice = $this->formatShopPrice($extension_code, $shop_price, $discount_disable);
//        return NumberHelper::price_format($shopPrice * $revise_discount);
        return $shop_price;
    }

    /**
     * 格式化 起订数量对应的 价格
     * @param $model
     * @return int|string
     */
    public function formatStartTotalPrice($model)
    {
        $goods_price = $this->getFormatGoodsPrice($model->shop_price, $model->discount_disable, $model->extension_code);

        $start_num = $this->formatStartNum($model);
        $start_total_price = bcmul($goods_price, $start_num, 2);
        if ($model->extension_code == self::INTEGRAL_EXCHANGE) {
            $start_total_price = (int)$start_total_price;
        } else {
            $start_total_price = NumberHelper::price_format($start_total_price);
        }

        return $start_total_price;
    }

    /**
     * 修正商品的用户折扣
     * @param $discountDisable
     * @return int
     */
    public function reviseDiscount($discountDisable)
    {
        if ($discountDisable) {
            return 1.0;
        } else {
            return $this->getUserRankDiscount();
        }
    }

    /**
     * 获取 修正折扣后的商品最低价格
     * @return string
     */
    public function formatMinPrice()
    {
        if ($this->extension_code == self::INTEGRAL_EXCHANGE) {
            $rs = (int)$this->min_price;
        } else {
            //  如果商品使用全局折扣，在这里就修正商品的售价，避免在商品梯度价格显示时出错
            $revise_discount = $this->reviseDiscount($this->discount_disable);
            $this->min_price *= $revise_discount;
            $rs = NumberHelper::price_format($this->min_price);
        }

        return $rs;
    }

    /**
     * 获取 修正过的最低价对应的折扣
     * @return string
     */
    public function formatDiscount()
    {
        if (!empty($this->market_price)) {
            $discount = NumberHelper::price_format($this->min_price / $this->market_price) * 100 / 10;
        } else {
            $discount = 0.00;
        }
        return NumberHelper::discount_format($discount);
    }

    /**
     * 格式化商品属性
     * @param $modelGoodsAttr
     * @return mixed
     */
    public function formatGoodsAttr($modelGoodsAttr)
    {
        $goodsAttr['region'] = '';
        $goodsAttr['effect'] = '';
        $goodsAttr['sample'] = '';
        if (!empty($modelGoodsAttr)) {

            foreach ($modelGoodsAttr as $attr) {
                switch ($attr['attr_id']) {
                    case 165:
                        $goodsAttr['region'] = $attr['attr_value'];
                        break;
                    case 211:
                        $goodsAttr['effect'] = $attr['attr_value'];
                        break;
                    case 212:
                        $goodsAttr['sample'] = $attr['attr_value'];
                        break;
                    default :
                        break;
                }
            }
        }

        return $goodsAttr;
    }

    /**
     * 取得商品优惠价格列表
     * @param $model
     * @return array
     */
    public function formatVolumePriceListForBuy($model)
    {
        $volume_price_list = VolumePrice::sort_volume_price_list($model->volumePrice);
//        $shop_price = $this->formatShopPrice($model->extension_code, $model->shop_price, $model->discount_disable);
        if ($model->discount_disable) {
            $revise_discount = 1;
        } else {
            $revise_discount = $this->reviseDiscount($model->discount_disable);
        }

        $start_num = $this->formatStartNum($model);
        $volume_price_list_for_buy = VolumePrice::volume_price_list_format(
            $volume_price_list,
            $model->shop_price,
            $revise_discount,
            $start_num,
            $model->goods_number
        );
        $count = count($volume_price_list_for_buy);
        if ($count < 3) {
            array_pad($volume_price_list_for_buy, 3, []);
        }
        return array_slice($volume_price_list_for_buy, 0, 3);
    }

    /**
     * 获取 pc端的商品详情页链接
     * @param $goods_id
     * @param $extension_code
     * @return string
     */
    public function getPcUrl($goods_id, $extension_code)
    {
        if ($extension_code == 'integral_exchange') {
            return 'exchange.php?act=info&id='.$goods_id;
        } else {
            return 'goods.php?id='.$goods_id;
        }
    }

    /**
     * 获取 微信端的商品详情页链接
     * @param $goods_id
     * @param $extension_code
     * @return string
     */
    public function getWechatUrl($goods_id, $extension_code)
    {
        if ($extension_code == 'integral_exchange') {
            return '/default/exchange/info/id/'.$goods_id.'.html';
        } else {
            return '/default/goods/index/id/'.$goods_id.'.html';
        }
    }

    /**
     * 处理标签的显示逻辑
     * @param $tags
     * @param $tagsOnly
     * @return array
     */
    public function getShowTagArray($tags)
    {
        $showTagArray = [
            'show_tag_sort' => 0,

            'mingxing' => 0,
            'xinpin' => 0,
            'zhifa' => 0,
            'manzeng' => 0,
            'manjian' => 0,

            //  团采、混批暂时不显示图标
            'tauncan' => 0,
            'hunpi' => 0,
            'coupon' => 0,
        ];

        if (!empty($tags)) {
            foreach ($tags as $tag) {
                if ($tag['enabled'] == Tags::TAG_ENABLE) {
                    $showTagArray['show_tag_sort'] += $tag['sort'];

                    switch ($tag['id']) {
                        case 1:
                            $showTagArray['xinpin'] = 1;
                            break;
                        case 2:
                            $showTagArray['zhifa'] = 1;
                            break;
                        case 3:
                            $showTagArray['manzeng'] = 1;
                            break;
                        case 5:
                            $showTagArray['mingxing'] = 1;
                            break;
                        case 7:
                            $showTagArray['manjian'] = 1;
                            break;
                        case 8:
                            $showTagArray['coupon'] = 1;
                            break;
                        case 4:
                        case 6:
                        default :
                            break;
                    }
                }
            }
        }

        return $showTagArray;
    }

    public function getBrand()
    {
        return $this->hasOne(Brand::className(), [
            'brand_id' => 'brand_id'
        ]);
    }
}