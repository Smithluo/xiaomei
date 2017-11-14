<?php

namespace api\modules\v2\models;

use common\helper\ImageHelper;
use Yii;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/7 0007
 * Time: 10:20
 */
class Goods extends \common\models\Goods
{
    public function fields()
    {
        return [
            'discount_disable' => function($model){
                return (int)$model->discount_disable;
            },
            'user_rank_discount' => function ($model) {
                return $this->getUserRankDiscount();
            },  //  修正会员对应的等级价格
            'goods_id',
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
        ];
    }

    public function extraFields()
    {
        return [
            'volumePrice',
            'category',
            'brand',
            'moqs',
            'goodsGallery',
            'tags',
            'gifts',
            'groupBuy',
        ];
    }

    public function getBrand()
    {
        return $this->hasOne(Brand::className(), [
            'brand_id' => 'brand_id',
        ]);
    }

    public function getVolumePrice()
    {
        return $this->hasMany(VolumePrice::className(), [
            'goods_id' => 'goods_id',
        ]);
    }

    public function getCategory()
    {
        return $this->hasOne(Category::className(), [
            'cat_id' => 'cat_id',
        ]);
    }

    public function getMoqs()
    {
        return $this->hasMany(Moq::className(), [
            'goods_id' => 'goods_id',
        ]);
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
     * 修正商品的用户折扣
     * @param $discountDisable
     * @return int
     */
    public function reviseDiscount()
    {
        if ($this->discount_disable == 1) {
            return 1.0;
        }
        return $this->getUserRankDiscount();
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
     * 格式化商品价格
     * @param $extension_code
     * @param $shop_price
     * @return int|string
     */
    public function formatShopPrice($extension_code, $shop_price)
    {
        if ($extension_code == self::INTEGRAL_EXCHANGE) {
            $rs = (int)$shop_price;
        } else {
            //  如果商品使用全局折扣，在这里就修正商品的售价，避免在商品梯度价格显示时出错
            $revise_discount = $this->reviseDiscount();
            $shop_price *= $revise_discount;
            $rs = NumberHelper::price_format($shop_price);
        }

        return $rs;
    }
}