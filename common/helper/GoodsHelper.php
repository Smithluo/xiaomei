<?php
/**
 * Created by PhpStorm.
 * User: clark
 * Date: 2016/12/21
 * Time: 18:00
 */

namespace common\helper;


use backend\models\LinkGoods;
use common\models\Event;
use common\models\Goods;
use common\models\GoodsActivity;
use common\models\GoodsAttr;
use common\models\VolumePrice;
use Yii;

class GoodsHelper
{
    /**
     * 生成goods_sn
     *
     * 前缀 + 补0 + (当前最大商品id + 1) 判定是否重复，如果重复，生成次数++
     * @param int $times    第几次生成 goods_sn
     * @return string
     */
    public static function makeGoodsSn($times = 1)
    {
        $length = 6;
        $snPrefixConfig = CacheHelper::getShopConfigParams('sn_prefix');
        $snPrefix = $snPrefixConfig['value'];
        $goodsIdMax = Goods::find()->max('goods_id');

        $goodsSn = $snPrefix.str_repeat('0', $length - strlen($goodsIdMax)).($goodsIdMax + $times);
        //  判重
        if (Goods::find()->where(['goods_sn' => $goodsSn])->one()) {
            self::makeGoodsSn($times++);
        } else {
            return $goodsSn;
        }
    }

    /**
     * 获取商品的最低售价
     * @param $goods_id
     * @return mixed
     */
    public static function getMinPrice($goods_id)
    {
        $rs = Goods::find()->select(['goods_id', 'shop_price'])
            ->where([
                'goods_id' => $goods_id
            ])->asArray()
            ->one();
        $min_price = $rs['shop_price'];

        $volume_price_sql_rs = VolumePrice::find()->select('volume_price')
            ->where([
                'goods_id' => $goods_id
            ])->asArray()
            ->all();
        if ($volume_price_sql_rs) {
            $volume_price_list = array_column($volume_price_sql_rs, 'volume_price');
            $min_volume_price = min($volume_price_list);
            $min_price = min($min_price, $min_volume_price);
        }

        return $min_price;
    }

    /**
     * 根据用户等级批量获取商品价格
     *
     * @param $goodsMap [
     *      'goods_id'      => $goods_id,
     *      'goods_number'  => $goods_number,
     * ]
     * @param $userRank
     * @return array    ['goods_id' => $goods_price]
     */
    public static function getGoodsPriceMapForBuy($goodsMap, $userRank)
    {
        $priceList = [];
        $goodsIdList = array_column($goodsMap, 'goods_id');
        $goodsNumMap = array_column($goodsMap, 'goods_number', 'goods_id');
        $goodsList = Goods::find()
            ->joinWith('volumePrice')
            ->where(['o_goods.goods_id' => $goodsIdList])
            ->all();

        if ($goodsList) {
            foreach ($goodsList as $goods) {
                $price = self::getFinalPrice($goods, $goodsNumMap[$goods->goods_id], $userRank);

                $priceList[$goods->goods_id] = NumberHelper::price_format($price);
            }

            return $priceList;
        } else {
            return [];
        }

    }

    /**
     * 获取商品的结算价格
     * @param $goods    obj Model
     * @param $buyNum   int
     * @param $userRank int
     * @return string
     */
    public static function getFinalPrice($goods, $buyNum, $userRank)
    {
        $goodsPrice = $goods->shop_price;
        $volumePrice = $goods->volumePrice;
        if (!empty($volumePrice)) {

            foreach ($volumePrice as $item) {
                if ($buyNum >= $item['volume_number'] && $goodsPrice > $item['volume_price']) {
                    $goodsPrice = $item['volume_price'];
                }
            }
        }

        if ($userRank > 1 && !$goods->discount_disable) {
            $userRankMap = CacheHelper::getUserRankCache();
            foreach ($userRankMap as $rank) {
                if ($rank['rank_id'] == $userRank) {
                    $goodsPrice = $goodsPrice * $rank['discount'] / 100;
                }
            }
        }

        return NumberHelper::price_format($goodsPrice);
    }

    /**
     * 获取订单商品对应的物料配比
     * @param $goodsIdList
     * @param $attrId
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getGoodsAttrInfo($goodsIdList, $attrId)
    {
        $goodsAttr = GoodsAttr::find()
            ->select([Goods::tableName().'.goods_id', 'goods_name', 'attr_value', 'attr_id'])
            ->joinWith('goods')
            ->where([
                GoodsAttr::tableName().'.goods_id' => $goodsIdList,
                GoodsAttr::tableName().'.attr_id' => $attrId,
            ])->andWhere([
                '>', GoodsAttr::tableName().'.attr_value', ''
            ])->asArray()
            ->all();

        return $goodsAttr;
    }

    /**
     * 获取商品所属的区域列表
     * @return array
     */
    public static function getGoodsRegionList()
    {
        $list = [];

        $rs = GoodsAttr::find()
            ->joinWith('goods')
            ->select(['attr_value', 'count(*) AS cnt'])
            ->where([
                'attr_id' => 165,
                Goods::tableName().'.is_on_sale' => Goods::IS_ON_SALE,
                Goods::tableName().'.is_delete' => Goods::IS_NOT_DELETE,
            ])->andWhere([
                '!=', 'attr_value', ''
            ])->groupBy('attr_value')
            ->orderBy(['cnt' => SORT_DESC])
            ->asArray()
            ->all();
        if ($rs) {
            $list = array_column($rs, 'attr_value');
        }

        return $list;
    }

    /**
     * 根据传入的商品列表，组合要显示的代码结构
     *
     * @param $goodslist    数据来源， api/v1/goods/list  $rs['items']
     * @return string       html代码段
     */
    public static function asyncGoodsListForWechat($goodslist, $divClassName = 'goodsInfo')
    {
        $htmlStr = '';
        foreach ($goodslist as $key => $goods) {
            $discount = $goods['discount'] ? $goods['discount'].' 折' : '';
            $tagsStr = '';
            if ($goods['showTagMap']) {
                foreach ($goods['showTagMap'] as $tag) {
                    $tagsStr .= $tag['mCode'];
                }
            }

            switch ($divClassName) {
                case 'goodsInfo':
                    $htmlStr .=
                        '<div class="goodsInfo">'.
                            '<div class="goodsImg">'.
                                '<img data-original="'.$goods['goods_img'].'" class="lazy">'.
                                '<a href="'.$goods['m_url'].'"></a>'.
                            '</div>'.
                            '<div class="goodsIntro">'.
                                '<div class="goodsTitle">'.
                                    '<span>'.$goods['goods_name'].'</span>'.
                                '</div>'.
                                '<div class="goodsSum"><span>￥ '.$goods['min_price'].'</span></div>'.
                                '<div class="goodsMark">'.
                                    '<span class="discount">'.$discount.'</span>'.
                                    $tagsStr.
                                '</div>'.
                            '</div>'.
                        '</div>';
                    break;
                case 'goodsList':
                    $htmlStr .=
                        '<div class="goodsList">'.
                            '<div class="goodsImg">'.
                                '<a href="'.$goods['m_url'].'">'.
                                    '<img data-original="'.$goods['goods_img'].'" class="lazy">'.
                                '</a>'.
                            '</div>'.
                            '<div class="goodsInfo">'.
                                '<div class="goodsTitle">'.
                                    '<span>'.$goods['goods_name'].'</span>'.
                                '</div>'.
                                '<div class="price">'.
                                    '<span>￥ '.$goods['min_price'].'</span>'.
                                    '<p>'.$discount.'</p>'.
                                '</div>'.
                            '</div>'.
                        '</div>';
                    break;
                default :
                    $htmlStr .=
                        '<div class="goodsInfo">'.
                            '<div class="goodsImg">'.
                                '<img data-original="'.$goods['goods_img'].'" class="lazy">'.
                                '<a href="'.$goods['m_url'].'"></a>'.
                            '</div>'.
                            '<div class="goodsIntro">'.
                                '<div class="goodsTitle">'.
                                    '<span>'.$goods['goods_name'].'</span>'.
                                '</div>'.
                                '<div class="goodsSum"><span>￥ '.$goods['min_price'].'</span></div>'.
                                '<div class="goodsMark">'.
                                    '<span class="discount">'.$discount.'</span>'.
                                    $tagsStr.
                                '</div>'.
                            '</div>'.
                        '</div>';
                    break;

            }

        }

        return $htmlStr;
    }


    /**
     * 根据传入的标签数组  返回标签字符串
     * $tags    array
     * @return  string
     */
    public static function tags($tags)
    {
        $icons ='';
        if(empty($tags)) {
            return $icons;
        } else {
            foreach($tags as $tag) {
                if($tag['enabled'] == 1) {
                    $name = $tag['name'];
                    switch ($name){
                        case '新品上市':
                            $icons .= '<i class="xinpin"></i>';
                            break;
                        case '小美直发':
                            $icons .= '<i class="zhifa"></i>';
                            break;
                        case '满赠':
                            $icons .= '<i class="manzeng"></i>';
                            break;
                        case '满减':
                            $icons .= '<i class="manjian"></i>';
                            break;
                        case '明星单品':
                            $icons .= '<i class="mingxing"></i>';
                            break;
                        case '优惠券':
                            $icons .= '<i class="coupon"></i>';
                            break;
                        default :
                            break;
                    }
                } else {
                    $icons .= '';
                }
            }
           \Yii::warning('icons : '.$icons,__METHOD__);
            return $icons;
        }
    }

    /**
     * 微信端的 标签样式
     * @param $tags
     * @return string
     */
    public static function WxTags($tags)
    {
        $icons = '';
        if(empty($tags)) {
            return $icons;
        } else {
            foreach($tags as $tag) {
                if($tag['enabled'] == 1) {
                    $name = $tag['name'];
                    switch ($name){
                        case '新品上市':
                            $icons .= '<span class="com_icon xm_new"></span>';
                            break;
                        case '满赠':
                            $icons .= '<span class="com_icon xm_zeng"></span>';
                            break;
                        case '满减':
                            $icons .= '<span class="com_icon xm_jian"></span>';
                            break;
                        case '明星单品':
                            $icons .= '<span class="com_icon xm_star"></span>';
                            break;
                        case '优惠券':
                            $icons .= '<span class="com_icon xm_quan"></span>';
                            break;
                        default :
                            break;
                    }
                } else {
                    $icons .= '';
                }
            }
            \Yii::warning('icons : '.$icons,__METHOD__);
            return $icons;
        }
    }
    /**
     *  根据传入的开始时间 返回 对应的时间标签状态
     *  @param      int     $startTime
     *  @param      int     $endTime
     *  @return string
     */
    public static function timeStatus($startTime,$endTime)
    {
        if(DateTimeHelper::getFormatCNTimesTimestamp($startTime) > time()){
            return '活动时间开始时间：'.DateTimeHelper::getFormatCNDate($startTime);
        }
        elseif(DateTimeHelper::getFormatCNTimesTimestamp($endTime) < time()){
            return '活动已经结束';
        }else
        {
            return '活动正在进行';
        }
    }

    /**
     * 根据传入的 是否按箱购买 起批量 装箱数 返回起批量
     * @Time 2017-3-20 15:20:00
     * @Author Eddie
     * @param $buyByBox
     * @param $startNum
     * @param $NumPerBox
     * @return mixed
     */
    public static function startNum($buyByBox,$startNum,$NumPerBox)
    {
        if( $buyByBox == 0 )
        {
            return  $startNum;
        }
        else
        {
            return max($startNum,$NumPerBox);
        }
    }

    /**
     * 根据传入的商品库存数量和商品的起批量 返回是否已经卖完
     * @Time 2017-3-20 15:20:00
     * @Author Eddie
     * @param $goodsNum
     * @param $startNum
     * @return bool
     */
    public static function isSaleOUt($goodsNum,$startNum)
    {
        if( $goodsNum < $startNum ) {
            return true;
        }
        else {
            return false ;
        }
    }

    /**
     * @param $goods
     * @param $countries
     * @param $desc
     * @param $event_name
     * @param $isTc
     * @param $activityStatus
     * @param $GoodsActivity
     * @return array
     */
    public static function activityGoodsInfo($goods, $countries, $desc, $event_name, $isTc, $activityStatus, $GoodsActivity = [])
    {
        $goodsNameSubNum = 15;
        $eventNameSubNum = 9;

        $user_discount = ($goods['discount_disable'] == 1 ) ? 1 : $_SESSION['discount'];

        $icons = GoodsHelper::tags($goods['tags']);

        $startNum = GoodsHelper::startNum($goods['buy_by_box'],$goods['start_num'],$goods['number_per_box']);

        $isSaleOut = GoodsHelper::isSaleOUt($goods['goods_number'],$goods['start_num']);

        $rs = [
            'act_id' => 0,
            'goods_id' => $goods['goods_id'],
            'goods_name' => $goods['goods_name'],
            'brief' => $goods['goods_brief'],
            'goods_name_msub' => msubstr($goods['goods_name'], $goodsNameSubNum),
            'start_num' => $goods['start_num'],
            'start_number' => $startNum,
            'country' => $countries[$goods['goods_id']]['attr_value'],
            'goods_thumb' => ImageHelper::get_image_path($goods['goods_thumb']),
            'goods_price' => NumberHelper::price_format($user_discount * $goods['min_price']),
            'market_price' => NumberHelper::price_format($goods['market_price']),
            'goods_desc' => $desc,
            'event_name' => $event_name,
            'event_desc' => $desc,
            'event_name_sub' => msubstr($event_name, $eventNameSubNum),
            'goods_number' => $goods['goods_number'],
            'is_tc' => $isTc, //是否是团采
            'pc_url' => '/goods.php?id='. $goods['goods_id'],
            'm_url' => '/default/goods/index/id/'.$goods['goods_id'].'.html',
            'goods_time' => $activityStatus,
            'number_per_box' => $goods['buy_by_box'] == 1 ? $goods['number_per_box'] : 1,
            'buy_by_box' => $goods['buy_by_box'],
            'icon' => $icons,
            'is_sale_out' =>$isSaleOut,
            'measure_unit' => empty($goods['measure_unit']) ? '件' : $goods['measure_unit'],
        ];


        /**
         * 判定秒杀状态
         * a)即将开始  ——当前时间【不在】活动时段内
         * b)进行中    ——当前时间【在】活动时段内 && 库存 > 0
         * c)已售罄    ——当前时间【在】活动时段内 && 库存 <= 0
         */
        $extInfo = [];
        $gmtNow = DateTimeHelper::getFormatGMTTimesTimestamp();

        if (!empty($GoodsActivity)) {
            //  修正团采的起订量等
            $rs['act_id'] = $GoodsActivity->act_id;
            $rs['pc_url'] = $isTc ? '/group_buy.php?id='.$GoodsActivity->act_id : '/goods.php?id=' . $goods['goods_id'];


            $startNum = GoodsHelper::startNum($GoodsActivity->buy_by_box, $GoodsActivity->start_num, $GoodsActivity->number_per_box);
            $rs['start_num'] = $startNum;
            $rs['start_number'] = $startNum;

            $isSaleOut = GoodsHelper::isSaleOUt($goods['goods_number'], $startNum);
            $rs['is_sale_out'] = $isSaleOut;


            if (
                $GoodsActivity->start_time < $gmtNow &&
                $GoodsActivity->end_time > $gmtNow &&
                $GoodsActivity->goods['goods_number'] >= $rs['start_num']
            ) {
                $extInfo['activityState'] = '正在进行中';
                $extInfo['statusDesc'] = 'onGoing';  //  进行中
                $extInfo['activityStateCode'] = 1;
                $extInfo['lastTime'] = DateTimeHelper::getFormatCNTimesTimestamp($GoodsActivity->end_time);
                $extInfo['canBuy'] = true;
            }
            elseif ($GoodsActivity->goods['goods_number'] < $rs['start_num'] || $GoodsActivity->end_time < $gmtNow) {
                $extInfo['activityState'] = '已结束';
                $extInfo['statusDesc'] = 'sellOut';  //  已售罄
                $extInfo['activityStateCode'] = 2;
                $extInfo['lastTime'] = 0;
                $extInfo['canBuy'] = false;
            }
            elseif ($GoodsActivity->start_time > $gmtNow) {
                $extInfo['activityState'] = '即将开始';
                $extInfo['statusDesc'] = 'startEve';  //  即将开始
                $extInfo['activityStateCode'] = 3;
                $extInfo['lastTime'] = 0;
                $extInfo['canBuy'] = false;

                $extInfo['startTimeCn'] = DateTimeHelper::getFormatCNTimesTimestamp($GoodsActivity->start_time);
                $extInfo['lastTimeMap'] = DateTimeHelper::getLastTimeMap($extInfo['startTimeCn']);
            } else {

            }
        }


        $rs['extInfo'] = $extInfo;
        return $rs;
    }

     /**
     * 返回按箱购买商品的可购买数量
     * 按照装箱数四舍五入，不足一箱算一箱
     * @param $goods_number
     * @param $number_per_box
     * @return float
     */
    public static function roundBoxNumber($goods_number, $buy_by_box, $number_per_box) {
        if ($buy_by_box == 0) {
            return $goods_number;
        } else {
            $box_num = round($goods_number / $number_per_box);
            //不足一箱返回一箱的数量
            if ($box_num < 1) {
                $box_num = 1;
            }

            return $box_num * $number_per_box;
        }
    }

    /**
     * 获取通用购物组件参数
     * @param $goods
     * @param int $goBuy
     * @return string
     */
    public static function getBuyData($goods, $userDiscount = 1.0, $goBuy = 0, $extensionCode= '') {
        $result = 'xm_gobuy='. $goBuy;

        if (isset($goods['goods_name'])) {
            $result .= '&goodsName='. $goods['goods_name'];
        }
        if (isset($goods['goods_id'])) {
            $result .= '&goodsId='. $goods['goods_id'];
        }
        if (isset($goods['start_num'])) {
            $result .= '&min_num='. $goods['start_num'];
        }
        if (isset($goods['goods_number'])) {
            $result .= '&max_num='. $goods['goods_number'];
        }
        if (!empty($goods['buy_by_box']) && !empty($goods['number_per_box'])) {
            $result .= '&box_num='. $goods['number_per_box'];
        }
        if (isset($goods['min_price'])) {
            $result .= '&price='. NumberHelper::price_format($goods['min_price'] * $userDiscount);
        }
        if (isset($goods['market_price'])) {
            $result .= '&market_price='. $goods['market_price'];
        }
        if (isset($goods['measure_unit'])) {
            $result .= '&unit='. $goods['measure_unit'];
        }
        if (isset($goods['goods_thumb'])) {
            $result .= '&img='. ImageHelper::get_image_path($goods['goods_thumb']);
        }

        //  加入购物车 不需要传值， 购买方式 有：general、general_buy_now、group_buy、flash_sale、integral_exchange
        if (!empty($extensionCode)) {
            $result .= '&extensionCode='.$extensionCode;
        }

        return $result;
    }

    /**
     * 能够出售的商品列表
     * 主要是给下拉列表用
     * @return array
     */
    public static function CanSaleGoods()
    {
        $goodsList = Goods::find()
            ->where([
                'is_on_sale' => 1,
                'is_delete' => 0,
            ])->asArray()->all();

        return  array_column($goodsList, 'goods_name', 'goods_id');
    }

    /**
     * @param $country
     * @return bool|string
     * 获取国家图标
     */
    public static function getCountryIcon($country)
    {
        if(!empty($country) ) {
            $country  = trim($country);
            $path = Yii::getAlias('@imgRoot/country_icons/'. $country. '.png');
            if(file_exists($path)) {
                return ImageHelper::get_image_path('/data/attached/country_icons/'. $country .'.png');
            } else {
                return '';
            }
        } else {
            return '';
        }
    }

    /**
     * @param $tags
     * @return string
     * 直发icon
     */
    public static function zhifaIcon($tags)
    {
        $icon = '';
        if(empty($tags)) {
            return $icon;
        } else {
            foreach($tags as $tag) {
                if($tag['enabled'] == 1) {
                    $name = $tag['name'];
                    if($name == '小美直发') {
                        $icon = '<span class="xm_self">直发</span>';
                    }
                } else {
                    $icon= '';
                }
            }
            \Yii::warning('icons : '.$icon,__METHOD__);
            return $icon;
        }
    }

    public static function fullGiftGoodsList()
    {
        $giftGoodsList = [];
        $countries = GoodsAttr::find()
            ->where(['attr_id' => 165])
            ->indexBy('goods_id')
            ->asArray()
            ->all();

        //满赠
        $fullGiftEvent = Event::find()->where([
            'event_type' => Event::EVENT_TYPE_FULL_GIFT,
            'is_active' => Event::IS_ACTIVE,
        ])->joinWith([
            'goods goods',
            'goods.tags'
        ])->andWhere([
            '>', 'end_time', date('Y-m-d H:i:s', time())
        ])->orderBy([
            new \yii\db\Expression('FIELD (goods.goods_number, 0)'),     //库存为0的排到后面
            'sort_order' => SORT_DESC,
            'event_id' => SORT_DESC,
        ])->all();

        foreach ($fullGiftEvent as $giftActivity) {
            foreach ($giftActivity->goods as $goods) {
                if ($goods['is_on_sale'] == 0 || $goods['is_delete'] == 1) {
                    continue;
                }

                $activityStatus = GoodsHelper::timeStatus($giftActivity->start_time, $giftActivity->end_time);
                $giftGoodsList[] = GoodsHelper::activityGoodsInfo(
                    $goods,
                    $countries,
                    $giftActivity->event_desc,
                    $giftActivity->event_name,
                    false,
                    $activityStatus
                );
            }
        }

        return $giftGoodsList;
    }

    public static function getAllFullGiftGoods() {
        $fullGiftGoods = \common\models\Goods::find()->alias('goods')->joinWith([
            'eventList eventList' => function ($query) {
                $query->onCondition([
                    'event_type' => Event::EVENT_TYPE_FULL_GIFT,
                    'is_active' => 1,
                ])->andOnCondition([
                    '<',
                    'eventList.start_time',
                    \common\helper\DateTimeHelper::getFormatCNDateTime(\common\helper\DateTimeHelper::gmtime()),
                ])->andOnCondition([
                    '>',
                    'eventList.end_time',
                    \common\helper\DateTimeHelper::getFormatCNDateTime(\common\helper\DateTimeHelper::gmtime()),
                ]);
            },
        ])->where([
            'goods.is_on_sale' => 1,
            'goods.is_delete' => 0,
        ])->andWhere([
            '>',
            'eventList.event_id',
            0,
        ])->orderBy([
            'eventList.sort_order' => SORT_DESC,
            'goods.sort_order' => SORT_DESC,
            'goods.complex_order' => SORT_DESC,
        ])->groupBy('goods.goods_id')->all();

        return $fullGiftGoods;
    }
}