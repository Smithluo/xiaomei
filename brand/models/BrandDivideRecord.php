<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/16
 * Time: 10:29
 */

namespace brand\models;

use Yii;
use common\models\OrderInfo as BaseOrderInfo;

class BrandDivideRecord extends \common\models\BrandDivideRecord
{

    /**
     * 获取可提取总金额 已终结的订单的分成
     *
     * @return mixed
     */
    public static function totalActive()
    {
        $order_tb_name = OrderInfo::tableName();
        $brand_tb_name = self::tableName();
        return self::find()
            ->leftJoin($order_tb_name, $order_tb_name.'.order_id = '.$brand_tb_name.'.order_id')
            ->where([
                $brand_tb_name.'.brand_id' => Yii::$app->session->get('user_brand_list'),
                $brand_tb_name.'.status' => self::BRAND_DIVIDE_RECORD_STATUS_UNTRACTED
            ])->andWhere([
                $order_tb_name.'.order_status' => OrderInfo::ORDER_STATUS_REALLY_DONE
            ])->sum($brand_tb_name.'.divide_amount');
    }

    /**
     * 获取交易中总金额
     * 给品牌商只显示已分单的订单对应的记录，只付款，我方未确认分单的订单不给品牌商看到，也不给品牌商看到分成
     * @return mixed
     */
    public static function totalFrozen($start_time = '')
    {
        $order_tb_name = OrderInfo::tableName();
        $brand_tb_name = self::tableName();

        if ($start_time && is_numeric($start_time)) {
            return self::find()
                ->leftJoin($order_tb_name, $order_tb_name.'.order_id = '.$brand_tb_name.'.order_id')
                ->where([$brand_tb_name.'.status' => self::BRAND_DIVIDE_RECORD_STATUS_UNTRACTED])
                ->andWhere(['!=', $order_tb_name.'.order_status', OrderInfo::ORDER_STATUS_REALLY_DONE])
                ->andWhere(['>', $order_tb_name.'.pay_time', $start_time])
                ->andWhere([
                    'or',
                    [$brand_tb_name.'.brand_id' => Yii::$app->session->get('user_brand_list')],
                    [$brand_tb_name.'.supplier_user_id' => Yii::$app->user->identity->getId()],
                ])
                ->sum($brand_tb_name.'.divide_amount');
        } else {
            return self::find()
                ->leftJoin($order_tb_name, $order_tb_name.'.order_id = '.$brand_tb_name.'.order_id')
                ->where([
                    $brand_tb_name.'.brand_id' => \Yii::$app->session->get('user_brand_list'),
                    $brand_tb_name.'.status' => self::BRAND_DIVIDE_RECORD_STATUS_UNTRACTED
                ])->andWhere(['!=', $order_tb_name.'.order_status', OrderInfo::ORDER_STATUS_REALLY_DONE])
                ->sum($brand_tb_name.'.divide_amount');
        }

    }

    /**
     * 根据order_id获取分成金额
     * @param $order_id
     * @return mixed
     */
    public static function getDivideAmountByOrderId($order_id)
    {
        $rs = self::find()
            ->select('divide_amount')
            ->where(['order_id' => $order_id])
            ->one();

        return $rs ? $rs->divide_amount : 0;
    }

    /**
     * 根据订单id获取分成记录id
     * @param $order_id
     * @return mixed
     */
    public static function getRecordIdByOrderId($order_id)
    {
        $rs = self::find()->select('id')
            ->where(['order_id' => $order_id])
            ->one();

        return $rs->id ?: false;
    }

    public function getOrderInfo(){
        return $this->hasOne(BaseOrderInfo::tableName(), ['order_id' => 'order_id']);
    }
}