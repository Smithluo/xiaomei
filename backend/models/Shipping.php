<?php
/**
 * Created by PhpStorm.
 * User: clark
 * Date: 2016/12/20
 * Time: 15:58
 */

namespace backend\models;

class Shipping extends \common\models\Shipping
{
    /**
     * 获取 shipping_id => shipping_desc 的映射
     * @return array
     */
    public static function getShippingIdDescMap()
    {
        $rs = self::find()->select(['shipping_id', 'shipping_desc'])->all();

//        $map = array_column($rs, 'shipping_desc', 'shipping_id');

        $map[] = '未设置将默认使用品牌的配送方式';
        foreach ($rs as $item) {
            $map[$item->shipping_id] = $item->shipping_desc;
        }

        return $map;
    }

    /**
     * 获取 shipping_code => shipping_desc 的映射
     * @return array
     */
    public static function getShippingCodeDescMap()
    {
        $rs = self::find()->select(['shipping_code', 'shipping_desc'])->asArray()->all();

        return array_column($rs, 'shipping_desc', 'shipping_code');
    }

    /**
     * 获取 shipping_name => shipping_desc 的映射
     * @return array
     */
    public static function getShippingCodeNameMap()
    {
        $rs = self::find()
            ->select(['shipping_code', 'shipping_name'])
            ->where(['enabled' => 1])
            ->asArray()
            ->all();

        return array_column($rs, 'shipping_name', 'shipping_code');
    }

    /**
     * 通过配送方式的code 获取 配送方式的名称
     * @param $shippingCode
     * @return mixed|string
     */
    public static function getShippingNameByCode($shippingCode)
    {
        $rs = Shipping::find()->select(['shipping_name'])->where(['shipping_code' => $shippingCode])->one();

        if (!empty($rs) && !empty($rs->shipping_name)) {
            return $rs->shipping_name;
        } else {
            return '';
        }
    }
}