<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/14
 * Time: 10:33
 */

namespace brand\models;

use \Yii;
use common\helper\NumberHelper;

class VolumePrice extends \common\models\VolumePrice
{

    /**
     * 获取商品的梯度价格
     * @param $goods_id
     * @param string $price_type
     * @return array
     */
    public static function get_volume_price_list($goods_id, $price_type = '1') {
        $volume_price = array();

        $res = self::find()->select(['volume_number', 'volume_price'])
            ->where([
                'goods_id' => $goods_id,
                'price_type' => $price_type,
            ])->orderBy(['volume_number' => SORT_ASC])
            ->all();

        foreach ($res as $item) {
            $volume_price[] = [
                'number' => $item->volume_number,
                'price' => $item->volume_price,
            ];
        }
        return $volume_price;
    }

}