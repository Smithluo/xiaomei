<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 9/11/16
 * Time: 9:58 PM
 */

namespace common\helper;

use api\modules\v1\models\Goods;
use api\modules\v1\models\GoodsAttr;

class FunctionHelper
{

    /**
     * 商品筛选条件 对应的方法
     * @param $key      筛选维度
     * @param $value    传递的匹配条件
     * @return array    结果集
     */
    public static function goodsSelectParams($key, $value)
    {
        switch ($key) {
            case 'brand_id':
                $rs = Goods::getBrandGoodsMap($value);
                break;
            case 'sub_cat_id':
                $rs = Goods::getCatGoodsMap($value);
                break;
            case 'tag':
                $rs = Goods::getTagGoodsMap($value);
                break;
            case 'origin':
                $rs = GoodsAttr::getAttrGoodsMap(165, $value);
                break;
            case 'effect':
                $rs = GoodsAttr::getAttrGoodsMap(211, $value);
                break;
            default :
                $rs = [];
                break;
        }

        return $rs;
    }
}