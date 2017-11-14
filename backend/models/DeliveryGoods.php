<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/22 0022
 * Time: 17:44
 */

namespace backend\models;


class DeliveryGoods extends \common\models\DeliveryGoods
{

    public function getGoods()
    {
        return $this->hasOne(Goods::className(), ['goods_id' => 'goods_id']);
    }

}