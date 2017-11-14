<?php

namespace api\modules\v1\models;

use common\models\Category;
use common\models\GoodsTag;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/8 0008
 * Time: 14:07
 */
class IndexSpecConfig extends \common\models\IndexSpecConfig
{

    public $discount;
    public $goods_price;

    public function fields()
    {
        return [
            'goods_id',
            'goods_name',
            'goods_thumb' => function($model) {
                return "http://m.xiaomei360.com/". $model->goods_thumb;
            },
            'goods_number' => function($model){
                return (int)$model->goods_number;
            },  //
            'goods_price' => function($model){
                return NumberHelper::price_format($model->goods_price);
            },  //
            'market_price' => function($model){
                return NumberHelper::price_format($model->market_price);
            },  //
            'discount' => function($model){
                return NumberHelper::discount_format($model->discount);
            },  //
        ];
    }

    public function extraFields()
    {
        return ['goods'];
    }
}