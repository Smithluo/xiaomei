<?php
/**
 * Created by PhpStorm.
 * User: clark
 * Date: 2016/11/25
 * Time: 20:32
 */

namespace api\modules\v1\models;


class TouchPayment extends \common\models\payment\TouchPayment
{

    /**
     * 取得支付方式信息
     * @param   int     $pay_id     支付方式id
     * @return  array   支付方式信息
     */
    public static function paymentInfo($pay_id, $pay_code = '') {
        $query = self::find()->where(['enabled' => 1]);

        if ($pay_id > 0) {
            return $query->andWhere(['pay_id' => $pay_id])->one();
        } elseif ($pay_code) {
            return $query->andWhere(['pay_code' => $pay_code])->one();
        }
    }
}