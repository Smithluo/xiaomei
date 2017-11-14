<?php

namespace common\helper;

use common\models\OrderGroup;
use common\models\OrderInfo;
use yii\db\Query;
use common\models\ServicerDivideRecord;

class ServicerDivideHelper {

    /**
     * 查询所有分成余额
     * 可以设置订单的状态作为条件：
     * 已确认：所有分成的总额
     * 真实完成：可提取金额
     * 已分单、已完成：交易中的收入
     * @param $servicer_user_ids
     * @return mixed
     */
    public static function getTotalDivideAmount($servicer_user_ids, $order_status = OrderInfo::ORDER_STATUS_CONFIRMED) {
        $query = new Query();
        $amounts = $query->select('servicer_user_id, SUM(divide_amount) AS total_amount')
            ->from(ServicerDivideRecord::tableName())
            ->leftJoin(OrderInfo::tableName(), [ServicerDivideRecord::tableName().'.order_id' => OrderInfo::tableName().'.order_id', OrderInfo::tableName().'.order_status'=>$order_status, OrderInfo::tableName().'.pay_status'=>OrderInfo::PAY_STATUS_PAYED])
            ->where('money_in_record_id=""')
            ->andWhere(['servicer_user_id'=>$servicer_user_ids])
            ->all();

        return $amounts;
    }

    /**
     * 查询所有分成余额
     * 可以设置订单的状态作为条件：
     * 已确认：所有分成的总额
     * 真实完成：可提取金额
     * 已分单、已完成：交易中的收入
     * @param $servicer_parent_ids
     * @param int $order_status
     * @return array
     */
    public static function getParentTotalDivideAmount($servicer_parent_ids, $order_status = OrderInfo::ORDER_STATUS_CONFIRMED) {
        $query = new Query();
        $amounts = $query->select('parent_servicer_user_id, SUM(parent_divide_amount) AS total_amount')
            ->from(ServicerDivideRecord::tableName())
            ->leftJoin(OrderInfo::tableName(), ServicerDivideRecord::tableName().'.order_id='.OrderInfo::tableName().'.order_id')
            ->where('money_in_record_id=0')
            ->andWhere(['parent_servicer_user_id'=>$servicer_parent_ids])
            ->andWhere([OrderInfo::tableName().'.order_status'=>$order_status, OrderInfo::tableName().'.pay_status'=>OrderInfo::PAY_STATUS_PAYED])
            ->all();

        return $amounts;
    }

    /**
     * 查询所有一级服务商应分成的总额，是加上了二级服务商的金额的，因为现在提取操作只能在一级服务商做，所以一级服务商需要把二级服务商的分成一起提取出来
     * @param $servicer_parent_ids
     * @param int $order_status
     */
    public static function getAllTotalDivideAmount($servicer_parent_ids, $order_status = OrderInfo::ORDER_STATUS_CONFIRMED) {
        $query = new Query();
        $amounts = $query->select('parent_servicer_user_id, SUM(parent_divide_amount) AS total_parent, SUM(divide_amount) AS total_child, SUM(parent_divide_amount) + SUM(divide_amount) AS total_all')
            ->from(ServicerDivideRecord::tableName())
            ->leftJoin(OrderInfo::tableName(), ServicerDivideRecord::tableName().'.order_id='.OrderInfo::tableName().'.order_id')
            ->where('money_in_record_id=0')
            ->andWhere(['parent_servicer_user_id'=>$servicer_parent_ids])
            ->andWhere([OrderInfo::tableName().'.order_status'=>$order_status, OrderInfo::tableName().'.pay_status'=>OrderInfo::PAY_STATUS_PAYED])
            ->all();

//        $divideRecords = ServicerDivideRecord::find()->joinWith([
//            'orderInfo orderInfo',
//        ])->where([
//            'money_in_record_id' => 0,
//        ])->andWhere(['parent_servicer_user_id'=>$servicer_parent_ids])
//            ->andWhere(['orderInfo.order_status'=>$order_status, 'orderInfo.pay_status'=>OrderInfo::PAY_STATUS_PAYED])->all();
        return $amounts;
    }

}