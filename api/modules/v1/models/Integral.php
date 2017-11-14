<?php
/**
 * Created by PhpStorm.
 * User: clark
 * Date: 2016/12/6
 * Time: 16:25
 */

namespace api\modules\v1\models;

use Yii;

class Integral extends \common\models\Integral
{

    /**
     * 获取指定用户的所有有效的积分流水
     *
     * 按updated_at 逆序 —— 按生效时间逆序排列
     * @param $userId
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getUserIntList($userId)
    {
        return self::find()
            ->where([
                'user_id' => $userId,
                'status' => self::STATUS_THAW
            ])->orderBy([
                'updated_at' => SORT_DESC
            ])->all();
    }

    /**
     * 创建积分入库记录
     *
     * @param $integral
     * @param $userId
     * @param $orderId
     * @param $outTradeNo
     * @param $payCode
     */
    public static function createRecord($integral, $userId, $orderId, $outTradeNo, $payCode)
    {
        //  向积分流水表中插入记录——购物赠送积分,积分兑换、团拼不赠送积分
        Yii::warning(__CLASS__.' '.__FUNCTION__.'向积分流水表中插入记录——购物赠送积分 参数：'.
            $integral.' | '.$userId.' | '.$orderId.' | '.$outTradeNo.' | '.$payCode);
        $time = gmtime();
        $integralModel = new Integral();

        $integralModel->integral        = $integral;
        $integralModel->user_id         = $userId;
        $integralModel->pay_code        = $payCode;
        $integralModel->out_trade_no    = $outTradeNo;
        $integralModel->note            = $orderId;
        $integralModel->created_at      = $time;
        $integralModel->updated_at      = $time;
        $integralModel->status          = 0;

        if ($integralModel->save()) {
            Yii::warning('向积分流水表中插入记录——购物赠送积分 成功');
        } else {
            Yii::warning('向积分流水表中插入记录——购物赠送积分 失败');
        }
    }
}