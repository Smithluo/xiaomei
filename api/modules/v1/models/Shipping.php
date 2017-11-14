<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/27 0027
 * Time: 21:14
 */

namespace api\modules\v1\models;

use Yii;

class Shipping extends \common\models\Shipping
{
    public function getShippingAreas() {
        return $this->hasMany(ShippingArea::className(), ['shipping_id' => 'shipping_id']);
    }

    /**
     * 获取包邮的shipping_id   code 是固定配的，id是可能变化的
     * @return int|mixed
     */
    public static function getFreeShippingId()
    {
        $freeShippingCode = Yii::$app->params['free_shipping_code'];
        $rs = self::find()->select('shipping_id')->where(['shipping_code' => $freeShippingCode])->one();

        if ($rs) {
            return $rs->shipping_id;
        } else {
            return 0;
        }
    }


    /**
     * 获取商品的配送方式,如果获取不到，返回默认配送方式
     *
     * @return int|mixed
     */
    public static function getShippingCodeById($shippingId)
    {
        $rs = self::find()->select('shipping_code')->where(['shipping_id' => $shippingId])->one();

        if ($rs) {
            return $rs->shipping_code;
        } else {
            return Yii::$app->params['default_shipping_code'];
        }
    }

}