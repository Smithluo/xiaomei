<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 7/23/16
 * Time: 10:20 AM
 */

namespace brand\models;

use \Yii;
use common\helper\SessionHelper;
use common\helper\DateTimeHelper;

class OrderInfo extends \common\models\OrderInfo
{
    /**
     * 获取待处理订单总数、距离现在最近的订单支付时间
     * 按·品牌商拆单之后，检索订单需要挑选order_info.supplier_user_id对应的所有订单
     * @return array
     */
    public static function getToBeOrders($order_cs_status = '', $start_time = '')
    {
        $session = Yii::$app->session;
        SessionHelper::getUserBrandList();
        if (empty($order_cs_status)) {
            $order_status_to_be_shipped = ['order_status' => self::$order_status_show];
        } elseif (in_array($order_cs_status, array_keys(self::$order_cs_status))) {
            $order_status_to_be_shipped = self::$order_cs_status[$order_cs_status];
        } else {
            return [
                'count' => 0
            ];
        }
        $supplier_user_id = Yii::$app->user->identity->getId();
        if (isset($start_time) && is_numeric($start_time)) {
            $query = static::find()->select('pay_time')
                ->andFilterWhere([
                    'or',
                    ['supplier_user_id' => $supplier_user_id],
                    ['brand_id' => $session->get('user_brand_list')]
                ])->andFilterWhere($order_status_to_be_shipped)
                ->andFilterWhere(['>', 'pay_time', $start_time])
                ->asArray()
                ->all();
        } else {
            $query = static::find()->select('pay_time')
                ->andFilterWhere([
                    'or',
                    ['supplier_user_id' => $supplier_user_id],
                    ['brand_id' => $session->get('user_brand_list')]
                ])->andFilterWhere($order_status_to_be_shipped)
                ->asArray()
                ->all();
        }

        $count = count($query);
        if ($count) {
            $pay_time_array = array_column($query, 'pay_time');
            $last_time = DateTimeHelper::getLastTime(max($pay_time_array));

            return [
                'count' => $count,
                'last_time' => $last_time
            ];
        } else {
            return [
                'count' => 0
            ];
        }
    }

}