<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/11 0011
 * Time: 9:34
 */

namespace api\modules\v2\models;

use common\helper\ImageHelper;
use common\helper\TextHelper;

class Brand extends \common\models\Brand
{
    public function fields()
    {
        return [
            'brand_id' => function($model){
                return intval($model->brand_id);
            },
            'brand_name' => function($model){
                return (string)$model->brand_name;
            },
            'brand_depot_area' => function($model){
                return (string)$model->brand_depot_area;
            },
            'brand_logo' => function($model){
                return ImageHelper::get_image_path($model->brand_logo);
            },
            'brand_logo_two' => function($model){
                return ImageHelper::get_image_path($model->brand_logo_two);
            },
            'brand_bgcolor' => function($model){
                return (string)$model->brand_bgcolor;
            },
            'brand_policy' => function($model){
                return ImageHelper::get_image_path($model->brand_policy);
            },
            'brand_desc' => function($model){
                return TextHelper::formatRichText($model->brand_desc);
            },
            'brand_desc_long' => function($model){
                return TextHelper::formatRichText($model->brand_desc_long);
            },
            'short_brand_desc' => function($model){
                return (string)$model->short_brand_desc;
            },
            'site_url' => function($model){
                return (string)$model->site_url;
            },
            'sort_order' => function($model){
                return intval($model->sort_order);
            },
            'is_show' => function($model){
                return intval($model->is_show);
            },
            'album_id' => function($model){
                return intval($model->album_id);
            },
            'brand_tag' => function($model){
                return intval($model->brand_tag);
            },
            'servicer_strategy_id' => function($model){
                return intval($model->servicer_strategy_id);
            },
            'supplier_user_id' => function($model){
                return intval($model->supplier_user_id);
            },
            'shipping_id' => function($model){
                return intval($model->shipping_id);
            },
            'discount' => function($model){
                return (string)$model->discount;
            },
            'turn_show_time' => function($model){
                return (string)$model->turn_show_time;
            },
            'country' => function($model){
                return (string)$model->country;
            },

        ];
    }

    public function extraFields()
    {
        return [
            'touchBrand',
        ];
    }

    public function getTouchBrand()
    {
        return $this->hasOne(TouchBrand::className(), ['brand_id' => 'brand_id']);
    }
}