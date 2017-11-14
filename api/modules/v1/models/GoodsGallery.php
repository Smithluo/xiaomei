<?php
/**
 * Created by PhpStorm.
 * User: clark
 * Date: 2017/3/31
 * Time: 15:47
 */

namespace api\modules\v1\models;


use common\helper\ImageHelper;

class GoodsGallery extends \common\models\GoodsGallery
{

    public function fields()
    {
        return [
            'img_id' => function($model){
                return (int)$model->img_id;
            },
            'goods_id' => function($model){
                return (int)$model->goods_id;
            },
            'img_url' => function($model){
                return ImageHelper::get_image_path($model->img_url);
            },
            'img_desc' => function($model){
                return (string)$model->img_desc;
            },
            'thumb_url' => function($model){
                return ImageHelper::get_image_path($model->thumb_url,  true);
            },
            'img_original' => function($model){
                return ImageHelper::get_image_path($model->img_original);
            },
        ];
    }
}