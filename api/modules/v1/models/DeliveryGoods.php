<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/10 0010
 * Time: 20:48
 */

namespace api\modules\v1\models;


use common\helper\ImageHelper;

class DeliveryGoods extends \common\models\DeliveryGoods
{
    public function fields()
    {
        return [
            'goods_id',
            'send_number',
            'goods_thumb' => function($model) {
                return ImageHelper::get_image_path($model->goods->goods_thumb);
            },
        ];
    }
}