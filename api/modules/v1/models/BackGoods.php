<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/11 0011
 * Time: 11:58
 */

namespace api\modules\v1\models;


use common\helper\ImageHelper;
use common\helper\NumberHelper;

class BackGoods extends \common\models\BackGoods
{
    public function fields()
    {
        return [
            'goods_id' => function ($model) {
                return (int)$model->goods_id;
            },
            'goods_name',
            'goods_thumb' => function ($model) {
                return ImageHelper::get_image_path($model->goods->goods_thumb);
            },
            'send_number' => function ($model) {
                return (int)$model->send_number;
            },
            'goods_price' => function ($model) {
                return NumberHelper::price_format($model->goods_price);
            },
            'total' => function ($model) {
                return NumberHelper::price_format($model->goods_price * $model->send_number);
            },
        ];
    }
}