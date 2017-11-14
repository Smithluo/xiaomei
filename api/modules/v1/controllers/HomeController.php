<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/9 0009
 * Time: 11:45
 */

namespace api\modules\v1\controllers;

use api\helper\OrderHelper;
use api\modules\v1\models\Brand;
use api\modules\v1\models\GoodsActivity;
use api\modules\v1\models\IndexHotBrand;
use api\modules\v1\models\IndexHotGoods;
use api\modules\v1\models\IndexSpecConfig;
use api\modules\v1\models\IndexStarGoodsTabConf;
use api\modules\v1\models\TouchAd;
use api\modules\v1\models\Users;
use common\helper\CacheHelper;
use common\helper\DateTimeHelper;
use common\helper\GoodsHelper;
use common\helper\ImageHelper;
use common\helper\NumberHelper;
use common\models\Ad;
use common\models\AppAd;
use common\models\FashionGoods;
use common\models\Goods;
use common\models\GoodsCollection;
use common\models\GuideType;
use common\models\IndexActivity;
use common\models\IndexCategory;
use common\models\IndexGoodBrands;
use common\models\IndexGroupBuy;
use common\models\IndexPaihangFloor;
use common\models\IndexZhifa;
use common\models\NewArrivedGoods;
use common\models\SeasonGoods;
use common\models\ZhifaBrand;
use common\models\ZhifaGoods;
use yii\helpers\ArrayHelper;
use Yii;

class HomeController extends BaseActiveController
{
    public $modelClass = 'api\modules\v1\models\IndexSpecConfig';

    /**
     * app首页接口
     * @return array
     */
    public function actionIndex()    {
        $data = [];

        //顶部banner -- start
        $adList = AppAd::find()->where([
            'position_id' => 1,
            'enable' => 1,
        ])->andWhere([
            '<',
            'start_time',
            DateTimeHelper::getFormatDateTimeNow(),
        ])->andWhere([
            '>',
            'end_time',
            DateTimeHelper::getFormatDateTimeNow(),
        ])->orderBy([
            'sort_order' => SORT_DESC,
        ])->all();

        $banners = [];
        foreach ($adList as $ad) {
            $item = [
                'route' => $ad['route'],
                'params' => $ad['params'],
            ];
            $banners[] = $item;
        }

        $data['banner'] = $banners;
        //顶部banner -- end

        //商品分类 -- start
        $adList = AppAd::find()->where([
            'position_id' => 2,
            'enable' => 1,
        ])->andWhere([
            '<',
            'start_time',
            DateTimeHelper::getFormatDateTimeNow(),
        ])->andWhere([
            '>',
            'end_time',
            DateTimeHelper::getFormatDateTimeNow(),
        ])->orderBy([
            'sort_order' => SORT_DESC,
        ])->all();

        $catLogoList = [];
        foreach ($adList as $ad) {
            $item = [
                'title' => $ad['title'],
                'route' => $ad['route'],
                'params' => $ad['params'],
            ];
            $catLogoList[] = $item;
        }

        $data['cat_logo_list'] = $catLogoList;
        //商品分类 --end

        $time = DateTimeHelper::getFormatGMTTimesTimestamp();
        //团采聚惠 --start
        $groupBuyLists = IndexGroupBuy::find()->joinWith([
            'goodsActivity goodsActivity',
            'goodsActivity.goods',
        ])->where([
            '>=',
            'goodsActivity.end_time',
            $time,
        ])->orderBy([
            IndexGroupBuy::tableName().'.sort_order' => SORT_DESC,
        ])->limit(3)->all();
        $groupBuyGoods =[];
        foreach($groupBuyLists as $groupBuy)
        {
            $groupBuyGoods[] =[
                'act_id' => $groupBuy->goodsActivity['act_id'],
                'title' => $groupBuy->title,
                'market_price' => $groupBuy->goodsActivity['old_price'],
                'min_price' => $groupBuy->goodsActivity['act_price'],
                'goods_thumb' => ImageHelper::get_image_path($groupBuy->goodsActivity->goods['goods_thumb']),
                'start_time' => $groupBuy->goodsActivity['start_time'],
                'end_time' => $groupBuy->goodsActivity['end_time'],
            ];
        }
        $data['group_buy_list'] = $groupBuyGoods;
        //团采聚惠 --end

        //活动特惠 --start
        $adList = AppAd::find()->where([
            'position_id' => 3,
            'enable' => 1,
        ])->andWhere([
            '<',
            'start_time',
            DateTimeHelper::getFormatDateTimeNow(),
        ])->andWhere([
            '>',
            'end_time',
            DateTimeHelper::getFormatDateTimeNow(),
        ])->orderBy([
            'sort_order' => SORT_DESC,
        ])->all();

        $activityList = [];
        foreach ($adList as $ad) {
            $item = [
                'title' => $ad['title'],
                'desc' => $ad['desc'],
                'route' => $ad['route'],
                'params' => $ad['params'],
            ];
            $activityList[] = $item;
        }

        $data['activity_list'] = $activityList;
        //活动特惠 --end

        //优选品牌 --start
        $goodBrands = IndexGoodBrands::find()->with([
            'brand'
        ])->orderBy([
            IndexGoodBrands::tableName().'.sort_order' => SORT_DESC
        ])->limit(7)->all();
        $goodBrandsInfo =[];

        foreach($goodBrands as $goodBrand) {
            $goodBrandsInfo[] =[
                'brand_id' => $goodBrand->brand['brand_id'],
                'brand_name' => $goodBrand->title,
                'brand_logo' => $goodBrand->getUploadUrl('index_logo'),
                'brand_thumb' => ImageHelper::get_image_path($goodBrand->brand['brand_logo_two']),
            ];
        }
        $data['goods_brands'] = $goodBrandsInfo;
        //优选品牌 --end

        //小美直发 -- start
        $adList = AppAd::find()->where([
            'position_id' => 1,
            'enable' => 1,
        ])->andWhere([
            '<',
            'start_time',
            DateTimeHelper::getFormatDateTimeNow(),
        ])->andWhere([
            '>',
            'end_time',
            DateTimeHelper::getFormatDateTimeNow(),
        ])->orderBy([
            'sort_order' => SORT_DESC,
        ])->all();

        $zhifaList = [];
        foreach ($adList as $ad) {
            $item = [
                'route' => $ad['route'],
                'params' => $ad['params'],
            ];
            $zhifaList[] = $item;
        }

        $data['zhifa'] = $zhifaList;
        //小美直发 -- end

        //选品专辑 -- start
        $goodsCollList = GoodsCollection::find()->alias('coll')->joinWith([
            'itemList itemList',
            'itemList.goods goods' => function ($query) {
                $query->onCondition([
                    'is_on_sale' => 1,
                    'is_delete' => 0,
                ]);
            },
        ])->where([
            'coll.is_show' => 1,
            'coll.is_hot' => 1,
        ])->orderBy([
            'coll.sort_order' => SORT_DESC,
        ])->limit(2)->all();

        $collDataList = [];
        foreach ($goodsCollList as $coll) {
            $item = [
                'title' => $coll['title'],
                'goods_count' => $coll->getItemCount(),
            ];
            $goodsDataList = [];
            foreach ($coll['itemList'] as $goodsItem) {
                if (count($goodsDataList) >= 2) {
                    break;
                }
                if (!empty($goodsItem['goods'])) {
                    $goods = $goodsItem['goods'];
                    $goodsDataList[] = [
                        'goods_id' => $goods['goods_id'],
                        'goods_thumb' => ImageHelper::get_image_path($goods['goods_thumb']),
                    ];
                }
            }
            $item['goods_list'] = $goodsDataList;
            $collDataList[] = $item;
        }
        $data['coll_list'] = $collDataList;
        //选品专辑 -- end

        //应季好货 --start
        $seasonGoods = SeasonGoods::find()->joinWith([
            'goods goods'
        ])->where(['is_show' => SeasonGoods::IS_SHOW])
            ->andWhere([
                'goods.is_on_sale' => 1,
            ])
            ->andWhere([
                'goods.is_delete' => 0,
            ])
            ->orderBy([
                'sort_order' => SORT_DESC
            ])->limit(4)->all();
        $seasonGoodsInfo =[];
        foreach($seasonGoods as $seasonGood) {
            $seasonGoodsInfo[] = [
                'goods_id' =>$seasonGood->goods['goods_id'],
                'index_name' => $seasonGood->name,
                'index_desc' => $seasonGood->desc,
                'index_logo' => ImageHelper::get_image_path($seasonGood->goods['goods_thumb']),
            ];
        }
        $data['season_goods'] = $seasonGoodsInfo;
        //应季好货 --end

        //商品分类tab --start
        $tabsData = [];
        $categories = CacheHelper::getAllCategoryLeaves();

        foreach ($categories as $category) {
            $subCats = '';
            foreach ($category['leaves'] as $subCat) {
                $subCats .= $subCat['cat_id']. ',';
            }
            if (!empty($subCats)) {
                $subCats = substr($subCats, 0, -1);
            }

            $tabsData[] = [
                'cat_name' => $category['cat_name'],
                'catIdList' => $subCats,
            ];
        }

        $data['tabs_cat'] = $tabsData;
        //商品分类tab --end

        //区域tab --start
        $brandList = Brand::getAllBrandAreaName();
        $data['tabs_area'] = ArrayHelper::getColumn($brandList, 'brand_area');
        //区域tab --end

        return $data;
    }

    /**
     * 直发页接口
     * @return mixed
     */
    public function actionZhifa() {
        //顶部banner -- start
        $adList = AppAd::find()->where([
            'position_id' => 1,
            'enable' => 1,
        ])->andWhere([
            '<',
            'start_time',
            DateTimeHelper::getFormatDateTimeNow(),
        ])->andWhere([
            '>',
            'end_time',
            DateTimeHelper::getFormatDateTimeNow(),
        ])->orderBy([
            'sort_order' => SORT_DESC,
        ])->all();

        $banners = [];
        foreach ($adList as $ad) {
            $item = [
                'route' => $ad['route'],
                'params' => $ad['params'],
            ];
            $banners[] = $item;
        }

        $data['banner'] = $banners;
        //顶部banner -- end

        //中间4个广告位 -- start
        $adList = AppAd::find()->where([
            'position_id' => 1,
            'enable' => 1,
        ])->andWhere([
            '<',
            'start_time',
            DateTimeHelper::getFormatDateTimeNow(),
        ])->andWhere([
            '>',
            'end_time',
            DateTimeHelper::getFormatDateTimeNow(),
        ])->orderBy([
            'sort_order' => SORT_DESC,
        ])->all();

        $centerAdList = [];
        foreach ($adList as $ad) {
            $item = [
                'route' => $ad['route'],
                'params' => $ad['params'],
            ];
            $centerAdList[] = $item;
        }

        $data['ad_list_4'] = $centerAdList;
        //中间4个广告位 -- end


        //品牌列表 --start
        $zhifaBrandList = ZhifaBrand::find()->joinWith([
            'brand brand'
        ])->orderBy([
            ZhifaBrand::tableName(). '.sort_order' => SORT_DESC,
        ])->where([
            'brand.is_show' => 1,
        ])->limit(13)->all();

        $brandList = [];
        foreach ($zhifaBrandList as $item) {
            $descList = explode('|', $item['brand']['short_brand_desc']);
            $descTop = $descList[0];
            $descBottom = '';
            if (count($descList) > 1) {
                $descBottom = $descList[1];
            }
            $brandList[] = [
                'brand_id' => $item['brand_id'],
                'brand_logo' => \common\helper\ImageHelper::get_image_path($item['brand']['brand_logo']),
                'desc_top' => $descTop,
                'desc_bottom' => $descBottom,
            ];
        }
        $data['brand_list'] = $brandList;
        //品牌列表 --end


        //优惠券 --start
        $couponEventList = \common\models\Event::find()->with([
            'fullCutRule',
        ])->where([
            'event_type' => \common\models\Event::EVENT_TYPE_COUPON,
        ])->andWhere([
            'effective_scope_type' => \common\models\Event::EFFECTIVE_SCOPE_TYPE_ZHIFA,
        ])->andWhere([
            'receive_type' => \common\models\Event::RECEIVE_TYPE_DRAW,
        ])->andWhere([
            'is_active' => 1,
        ])->andWhere([
            '<',
            'start_time',
            \common\helper\DateTimeHelper::getFormatCNDateTime(DateTimeHelper::gmtime()),
        ])->andWhere([
            '>',
            'end_time',
            \common\helper\DateTimeHelper::getFormatCNDateTime(DateTimeHelper::gmtime()),
        ])->all();

        $ruleDataList = [];
        foreach ($couponEventList as $couponEvent) {
            $rules = $couponEvent['fullCutRule'];
            foreach ($rules as $rule) {
                if (count($ruleDataList) >= 4) {
                    break 2;
                }
                $item = [
                    'rule_id' => $rule['rule_id'],
                    'cut' => intval($rule['cut']),
                    'above' => intval($rule['above']),
                    'end_time' => \common\helper\DateTimeHelper::getFormatDate($couponEvent['end_time']),
                    'sub_type' => $couponEvent['sub_type'],
                    'desc' => $couponEvent['event_desc'],
                ];
                $ruleDataList[] = $item;
            }
        }
        $data['rule_data_list'] = $ruleDataList;
        //优惠券 --end


        //最新到货 --start
        $newArrivedList = NewArrivedGoods::find()->joinWith([
            'goods goods'
        ])->where([
            'goods.is_on_sale' => 1,
        ])->andWhere([
            'goods.is_delete' => 0,
        ])->orderBy([
            new \yii\db\Expression('FIELD (goods.goods_number, 0)'),     //库存为0的排到后面
            NewArrivedGoods::tableName() . '.sort_order' => SORT_DESC,
        ])->limit(5)->all();


        if (!isset(Yii::$app->user->identity)) {
            $token = Yii::$app->request->getAuthUser();
            if (!empty($token)) {
                $userModel = Users::findIdentityByAccessToken($token);
                Yii::$app->user->login($userModel);
            }
        }
        //默认折扣
        $userDiscount = 1.0;
        if (!empty($userModel)) {
            $user_rank_map = CacheHelper::getUserRankCache();
            if (!empty($user_rank_map[$userModel->user_rank]['discount'])) {
                $userDiscount = $user_rank_map[$userModel->user_rank]['discount'] / 100.0;
            }
        }

        $goodsList = [];

        foreach ($newArrivedList as $item) {
            $goods = $item['goods'];
            $user_discount = ($goods['discount_disable'] == 1) ? 1 : $userDiscount;
            $min_price = NumberHelper::price_format($goods['min_price'] * $user_discount);

            $goodsItem = [
                'goods_id' => $goods['goods_id'],
                'goods_name' => $goods['goods_name'],
                'goods_thumb' => \common\helper\ImageHelper::get_image_path($goods['goods_thumb']),
                'price' => $min_price,
                'start_num' => $goods['start_num'],
            ];

            if (!empty($goods['buy_by_box'])) {
                $goodsItem['box_num'] = $goods['number_per_box'];
            }

            $goodsList[] = $goodsItem;
        }

        $data['new_arrived_goods_list'] = $goodsList;
        //最新到货 --end


        //中部商品区(清仓特卖，热批热卖，有物有料) --start
        $zhifaGoodsList = ZhifaGoods::find()->joinWith([
            'goods goods',
        ])->where([
            'goods.is_on_sale' => 1,
            'goods.is_delete' => 0,
        ])->orderBy([
            ZhifaGoods::tableName() . '.sort_order' => SORT_DESC,
        ])->all();

        $qingcangList = [];
        $hotList = [];
        $wuliaoList = [];
        foreach ($zhifaGoodsList as $item) {
            $goods = $item['goods'];
            $user_discount = ($goods['discount_disable'] == 1) ? 1 : $userDiscount;
            $min_price = NumberHelper::price_format($goods['min_price'] * $user_discount);

            $goodsItem = [
                'goods_id' => $goods['goods_id'],
                'goods_name' => $goods['goods_name'],
                'goods_thumb' => \common\helper\ImageHelper::get_image_path($goods['goods_thumb']),
                'price' => $min_price,
                'start_num' => $goods['start_num'],
            ];
            switch ($item['type']) {
                case ZhifaGoods::TYPE_QINGCANG:
                    $qingcangList[] = $goodsItem;
                    break;
                case ZhifaGoods::TYPE_HOT:
                    $hotList[] = $goodsItem;
                    break;
                case ZhifaGoods::TYPE_WULIAO:
                    $wuliaoList[] = $goodsItem;
                    break;
            }
        }

        $qingcangList = array_chunk($qingcangList, 4);
        $hotList = array_chunk($hotList, 4);
        $wuliaoList = array_chunk($wuliaoList, 4);

        $data['qingcang_list'] = $qingcangList;
        $data['hot_list'] = $hotList;
        $data['wuliao_list'] = $wuliaoList;

        //中部商品区(清仓特卖，热批热卖，有物有料) --end

        return $data;
    }

    /**
     * 选品指南接口
     * @return mixed
     */
    public function actionGuide_goods()
    {
        if (!isset(Yii::$app->user->identity)) {
            $token = Yii::$app->request->getAuthUser();
            if (!empty($token)) {
                $userModel = Users::findIdentityByAccessToken($token);
                Yii::$app->user->login($userModel);
            }
        }
        //默认折扣
        $userDiscount = 1.0;
        if (!empty($userModel)) {
            $user_rank_map = CacheHelper::getUserRankCache();
            if (!empty($user_rank_map[$userModel->user_rank]['discount'])) {
                $userDiscount = $user_rank_map[$userModel->user_rank]['discount'] / 100.0;
            }
        }

        $guideGoodsList = GuideType::find()
            ->joinWith([
                'guideGoods',
                'guideGoods.goods'
            ])
            ->orderBy([
                GuideType::tableName() . '.sort_order' => SORT_DESC
            ])
            ->all();

        $guideList = [];
        foreach ($guideGoodsList as $guide) {
            $guideGoods = [];

            foreach ($guide->guideGoods as $goods) {
                if ($goods['goods']['is_on_sale'] == 0 || $goods['goods']['is_delete'] == 1) {
                    continue;
                }
                $user_discount = ($goods['goods']['discount_disable'] == 1) ? 1 : $userDiscount;
                if (count($guideGoods) < 6) {
                    $guideGoods[] = [
                        'goods_id' => $goods['goods']['goods_id'],
                        'min_price' => $goods['goods']['min_price'] * $user_discount,
                        'goods_thumb' => ImageHelper::get_image_path($goods['goods']['goods_thumb']),
                        'original_img' => ImageHelper::get_image_path($goods['goods']['original_img']),
                    ];
                }
            }
            $guideList[] = [
                'id' => $guide->id,
                'title' => $guide->title,
                'desc' => $guide->desc,
                'css' => GuideType::$guide_css_map[$guide->id],
                'bg' => GuideType::$guide_bg_map[$guide->id],
                'goods' => $guideGoods,
            ];
        }
        $data['guide_list'] = $guideList;
        return $data;
    }

    /**
     * 热销排行
     * @return array
     */
    public function actionRank_list()
    {
        if (!isset(Yii::$app->user->identity)) {
            $token = Yii::$app->request->getAuthUser();
            if (!empty($token)) {
                $userModel = Users::findIdentityByAccessToken($token);
                Yii::$app->user->login($userModel);
            }
        }
        //默认折扣
        $userDiscount = 1.0;
        if (!empty($userModel)) {
            $user_rank_map = CacheHelper::getUserRankCache();
            if (!empty($user_rank_map[$userModel->user_rank]['discount'])) {
                $userDiscount = $user_rank_map[$userModel->user_rank]['discount'] / 100.0;
            }
        }

        $rankList = IndexPaihangFloor::find()->with([
            'paihangGoods',
            'paihangGoods.goods',
        ])->orderBy([
            IndexPaihangFloor::tableName() . '.sort_order' => SORT_DESC,
            'id' => SORT_DESC,
        ])->all();

        $floorDataList = [];
        foreach ($rankList as $rank) {
            $floorData['id'] = $rank['id'];
            $floorData['title'] = $rank['title'];
            $floorData['desc'] = $rank['description'];
            $floorData['image'] = $rank->getUploadUrl('image');

            $goodsList = [];
            foreach ($rank['paihangGoods'] as $index => $goods) {
                $user_discount = ($goods['goods']['discount_disable'] == 1) ? 1 : $userDiscount;
                $price = NumberHelper::price_format($user_discount * $goods['goods']['min_price']);

                $item = [
                    'goods_id' => $goods['goods_id'],
                    'goods_name' => $goods['goods']['goods_name'],
                    'goods_thumb' => \common\helper\ImageHelper::get_image_path($goods['goods']['goods_thumb']),
                    'min_price' => $price,
                ];
                $goodsList[] = $item;

                if ($index >= 2) {
                    break;
                }
            }
            $floorData['goods_list'] = $goodsList;

            $floorDataList[] = $floorData;
        }

        return ['rank_list' => $floorDataList];
    }
}