<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/6 0006
 * Time: 17:14
 */

namespace api\modules\v1\models;

use common\helper\CacheHelper;
use common\helper\DateTimeHelper;
use common\helper\OrderGroupHelper;
use common\helper\PaymentHelper;
use Yii;
use yii\helpers\VarDumper;

class PayNotifyCallBack extends \WxPayNotify
{

    //查询订单
    public function Queryorder($transaction_id)
    {
        $input = new \WxPayOrderQuery();
        $input->SetTransaction_id($transaction_id);
        $result = \WxPayApi::orderQuery($input);
        Yii::warning('query:' . json_encode($result));
        if(array_key_exists("return_code", $result)
            && array_key_exists("result_code", $result)
            && $result["return_code"] == "SUCCESS"
            && $result["result_code"] == "SUCCESS")
        {
            Yii::warning('result = '. VarDumper::export($result), __METHOD__);
            $outTradeNo = isset($result['out_trade_no']) ? $result['out_trade_no'] : '';

            $expTradeNo = explode('O', $outTradeNo);
            $orderIsPaidBefore = true;
            if (!empty($expTradeNo)) {
                $groupId = $expTradeNo[0];
                $orderGroup = \common\models\OrderGroup::find()->where([
                    'group_id' => $groupId,
                ])->one();

                if (empty($orderGroup) || $orderGroup['group_status'] == \common\models\OrderGroup::ORDER_GROUP_STATUS_UNPAY) {
                    $orderIsPaidBefore = false;
                }
            }

            if (!empty($outTradeNo) && strstr($outTradeNo, 'O')) {
                $alipayInfos = WechatPayInfo::find()->joinWith([
                    'paylog',
                    'orderInfo'
                ])->where(['out_trade_no' => $outTradeNo])->all();

                $paymentMap = PaymentHelper::$paymentMap;
                foreach ($alipayInfos as $payInfo) {

                    $payLog = $payInfo->paylog;
                    if ($payLog->is_paid != 0) {
                        continue;
                    }

                    if (!empty($result['transaction_id'])) {
                        $payInfo->transaction_id = $result['transaction_id'];
                    }
                    if (!empty($result['time_end'])) {
                        $payInfo->pay_success_time = $result['time_end'];
                    }
                    if (!empty($result['bank_type'])) {
                        $payInfo->bank_type = $result['bank_type'];
                    }
                    if (!empty($result['cash_fee'])) {
                        $payInfo->cash_fee = $result['cash_fee'];
                    }
                    if (!empty($result['fee_type'])) {
                        $payInfo->fee_type = $result['fee_type'];
                    }
                    if (!empty($result['is_subscribe'])) {
                        $payInfo->is_subscribe = $result['is_subscribe'];
                    }

                    if (!$payInfo->save()) {
                        Yii::error('微信支付流水保存失败 e = '. VarDumper::export($payInfo->errors), __METHOD__);
                    }

                    $payLog->is_paid = 1;

                    if (!$payLog->save()) {
                        Yii::error('支付日志保存失败 e = '. VarDumper::export($payLog->errors), __METHOD__);
                    }

                    $orderInfo = $payInfo->orderInfo;
                    $orderInfo->order_status = OrderInfo::ORDER_STATUS_CONFIRMED;
                    $orderInfo->confirm_time = DateTimeHelper::gmtime();
                    $orderInfo->pay_status = OrderInfo::PAY_STATUS_PAYED;
                    $orderInfo->pay_time = DateTimeHelper::gmtime();
                    $orderInfo->money_paid = $orderInfo->order_amount;
                    $orderInfo->order_amount = 0;
                    $orderInfo->pay_id = PaymentHelper::PAY_ID_WXPAY_IOS;
                    $orderInfo->pay_name = $paymentMap[$orderInfo->pay_id];

                    if (!$orderInfo->save()) {
                        Yii::error('订单状态保存失败 e = '. VarDumper::export($orderInfo->errors), __METHOD__);
                    } else {
                        //减库存
                        foreach ($orderInfo->orderGoods as $goods) {
                            $goods->goods->goods_number -= $goods->goods_number;
                            if ($goods->goods->goods_number < 0) {
                                $goods->goods->goods_number = 0;
                            }
                            $goods->goods->save(false);
                        }
                    }

                    if (!empty($orderInfo->orderGroup)) {
                        $orderGroup = $orderInfo->orderGroup;
                        $orderGroup->syncTimeInfo();
                        $orderGroup->syncFeeInfo();
                        $orderGroup->setupOrderStatus();
                        $orderGroup->save();
                    }

                    //  向积分流水表中插入记录——购物赠送积分,积分兑换、团拼不赠送积分
                    if ($orderInfo->extension_code != 'group_buy' && $orderInfo->extension_code != 'integral_exchange') {
                        $integral = floor(($orderInfo->goods_amount - $orderInfo->discount) / 10);
                        Yii::warning(__FUNCTION__.'wxpay create integral $integral='.$integral.', $order_id='.$payLog->order_id);

                        Integral::createRecord($integral, $orderInfo->user_id, $payLog->order_id, $outTradeNo, 'wxpay');
                    }

                    if (!empty($orderInfo->mobile)) {
                        $smsInfo = CacheHelper::getShopConfigParams('sms_order_paid_content')['value'];
                        $api = new \ChuanglanSMS('N9058011', '2701dfc4');
                        $api->send($orderInfo->mobile, $smsInfo);
                    }
                }
            }
            //派券
            if (!$orderIsPaidBefore) {
                OrderGroupHelper::sendCouponAfterPaid($outTradeNo);
            } else {
                Yii::warning('之前支付过了，所以这次通知不送券', __METHOD__);
            }
            return true;
        }
        return false;
    }

    //重写回调处理函数
    public function NotifyProcess($data, &$msg)
    {
        Yii::info('call back:' . json_encode($data));

        if(!array_key_exists("transaction_id", $data)){
            $msg = "输入参数不正确";
            return false;
        }
        //查询订单，判断订单真实性
        if(!$this->Queryorder($data["transaction_id"])){
            $msg = "订单查询失败";
            return false;
        }
        return true;
    }
}