<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/6 0006
 * Time: 13:56
 */

namespace api\modules\v1\controllers;

use api\modules\v1\models\Integral;
use api\modules\v1\models\PayNotifyCallBack;
use api\modules\v1\models\YeePayInfo;
use common\helper\OrderGroupHelper;
use common\helper\PaymentHelper;
use Yii;
use api\modules\v1\models\AlipayInfo;
use api\modules\v1\models\OrderInfo;
use common\helper\CacheHelper;
use common\helper\DateTimeHelper;
use yii\helpers\VarDumper;

class NotifyController extends BaseActiveController
{
    public $modelClass = 'api\modules\v1\models\OrderInfo';

    public function actionAlipay_rsp() {

        $postData = Yii::$app->request->post();
        if (empty($postData)) {
            Yii::error('post参数为空', __METHOD__);
            return 'fail';
        }

        $tradeNo = $postData['trade_no'];
        $outTradeNo = $postData['out_trade_no'];

        Yii::warning('回调数据：'. VarDumper::export($postData), __METHOD__);

        $expTradeNo = explode('A', $outTradeNo);
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

        $c = new \AopClient();
        $c->gatewayUrl = "https://openapi.alipay.com/gateway.do";
        $c->appId = "2016042001316372";
        $c->rsaPrivateKey = 'MIICXgIBAAKBgQDdBRWSSXb7tg2mmav2VzAiEVNuLU1NQkxl684LdLBUSyx9oCiXJ6tJVXW37DyLhSxsbdqivwTV2Xb3Czi91J9GhQqDwqpU4vSDDHDZSyEgjq/wrflATo8+ST48eVKlyscMkwPhv2lc2oJSGjgmkorb3Jl58eG7YfcCk3Aw9P6w2wIDAQABAoGAA7CACa8cQ1toou1Rx4zxCsCLSf2LmsyOhe0HxX0vLFkM5xPzWYKaA2Ff07An2pRgh3bV/X1+0SsOJ1WSnuibuAOuev8QXTXYrMPtX6MLYvRP1HrqlZoVBO1Bmc68jdoxS7omHSaK86m4yrPQIwaP02K0k3XGRqCXQ3VxODReZTECQQD/Ly7YeEd3O6is35iTca0sMPrc92ORo3IwhEdrPhOiZAyJlIb4IDBSkYxvEwbFvKcjfp8CePBZv/vaE5gp9wcnAkEA3bnxzcv2cG3UJfhQ//ysFY8nt/QrNQ25x6M+dAV093H+w0Vl22Ldv4bnOTkNYl5dUL+OGZd/8rZQCTtzcud5LQJBALuA1OAUSRbQTFlyFi9I2ODewIX6dTv/KBmEKOIhA9ZPw3KYIzBQnpEdB15aUaCbxQfsszPi32BjE9Ciky1KqQMCQQC4yPDGTEdz53Q4uLv4u0FHLmkxm6IusuOzh07TLoEOf8iMQNfkgH7B0dH+FJgc9PvcAeiRV3tgcaQ+LXfHuTV5AkEApkWYz+r5/z8yhLkNkhu0CVG863K4rWYRPOs9yWleSvn5BCYGJz07NS2j23qebQh6kDWvX+zt96Sfb4f/QITExQ==';
        $c->alipayrsaPublicKey = 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDDI6d306Q8fIfCOaTXyiUeJHkrIvYISRcc73s3vF1ZT7XN8RNPwJxo8pWaJMmvyTn9N4HQ632qJBVHf8sxHi/fEsraprwCtzvzQETrNRwVxLO5jVmRGi60j8Ue1efIlzPXV9je9mkjzOmdssymZkh2QhUrCmZYI/FCEa3/cNMW0QIDAQAB';
        $c->apiVersion = '1.0';
        $c->postCharset = 'UTF-8';
        $c->format = "json";

        //如果验签失败，再通过查询接口去拉一次订单信息，如果拉到并且状态是支付成功，也可以走接下来的流程
        if (!$c->rsaCheckV1($postData, null)) {
            $req = new \AlipayTradeQueryRequest();
            $req->setBizContent("{".
                "\"out_trade_no\":\"". $outTradeNo. "\",".
                "\"trade_no\":\"". $tradeNo. "\"".
                "}");
            $result = $c->execute($req);

            if (empty($result->alipay_trade_query_response->trade_status) || $result->alipay_trade_query_response->trade_status != 'TRADE_SUCCESS') {
                Yii::error('订单查询结果不对 result = '. VarDumper::export($result), __METHOD__);
                return 'fail';
            }
            else {
                $postData['trade_no'] = $result->alipay_trade_query_response->trade_no;
                $postData['out_trade_no'] = $result->alipay_trade_query_response->out_trade_no;
                $postData['total_amount'] = $result->alipay_trade_query_response->total_amount;
                $postData['receipt_amount'] = $result->alipay_trade_query_response->receipt_amount;
            }
        }

        $appId = $postData['app_id'];
        if ($appId != '2016042001316372') {
            Yii::error('支付宝appid验证失败', __METHOD__);
            return 'fail';
        }

        if (!empty($outTradeNo)) {
            Yii::info('outTradeNo = '. $outTradeNo, __METHOD__);
        }
        else {
            Yii::error('outTradeNo = null', __METHOD__);
            return 'fail';
        }

        if (strstr($outTradeNo, 'A')) {
            $alipayInfos = AlipayInfo::find()->joinWith([
                'paylog',
                'orderInfo'
            ])->where(['out_trade_no' => $outTradeNo])->all();

            if (empty($alipayInfos)) {
                Yii::error('找不到订单', __METHOD__);
                return 'fail';
            }

            $paymentMap = PaymentHelper::$paymentMap;
            foreach ($alipayInfos as $payInfo) {
                $payLog = $payInfo->paylog;
                if ($payLog->is_paid != 0) {
                    continue;
                }
                $payLog->is_paid = 1;

                $payLog->save();

                $orderInfo = $payInfo->orderInfo;
                $orderInfo->order_status = OrderInfo::ORDER_STATUS_CONFIRMED;
                $orderInfo->confirm_time = DateTimeHelper::gmtime();
                $orderInfo->pay_status = OrderInfo::PAY_STATUS_PAYED;
                $orderInfo->pay_time = DateTimeHelper::gmtime();
                $orderInfo->money_paid = $orderInfo->order_amount;
                $orderInfo->order_amount = 0;
                $orderInfo->pay_id = PaymentHelper::PAY_ID_ALIPAY_IOS;
                $orderInfo->pay_name = $paymentMap[$orderInfo->pay_id]; //  支付宝

                if (!$orderInfo->save()) {
                    Yii::warning($orderInfo->order_sn.' 支付状态修改失败'.json_encode($orderInfo));
                }

                //减库存
                foreach ($orderInfo->orderGoods as $goods) {
                    $goods->goods->goods_number -= $goods->goods_number;
                    if ($goods->goods->goods_number < 0) {
                        $goods->goods->goods_number = 0;
                    }
                    $goods->goods->save(false);
                }

                if (!empty($orderInfo->orderGroup)) {
                    $orderGroup = $orderInfo->orderGroup;
                    $orderGroup->syncTimeInfo();
                    $orderGroup->syncFeeInfo();
                    $orderGroup->setupOrderStatus();
                    $orderGroup->save();
                }

                //  向积分流水表中插入记录——购物赠送积分,积分兑换、团拼不赠送积分
                if (!in_array($orderInfo->extension_code, ['group_buy', 'flash_sale', 'integral_exchange'])) {
                    $integral = floor(($orderInfo->goods_amount - $orderInfo->discount) / 10);
                    if ($integral > 0) {
                        Yii::warning(__FUNCTION__.'alipay create integral $integral='.$integral.', $order_id='.$payLog->order_id);

                        Integral::createRecord($integral, $orderInfo->user_id, $payLog->order_id, $outTradeNo, 'alipay');
                    }
                }

                if (!empty($orderInfo->mobile)) {
                    $smsInfo = CacheHelper::getShopConfigParams('sms_order_paid_content')['value'];
                    $api = new \ChuanglanSMS('N9058011', '2701dfc4');
                    if ($api->send($orderInfo->mobile, $smsInfo)) {
                        Yii::warning($orderInfo->order_sn.' 支付状态修改 短信发送成功');
                    } else {
                        Yii::warning($orderInfo->order_sn.' 支付状态修改 短信发送失败！！！');
                    }
                }
            }

            //派券
            if (!$orderIsPaidBefore) {
                OrderGroupHelper::sendCouponAfterPaid($outTradeNo);
            } else {
                Yii::warning('之前支付过了，所以这次通知不送券', __METHOD__);
            }

            return 'success';
        }
        return 'fail';
    }

    public function actionWxpay_rsp() {
        Yii::info('微信支付开始', __METHOD__);
        $notify = new PayNotifyCallBack();
        $notify->Handle(false);
    }

    public function actionYeepay_rsp() {
        Yii::warning('易宝支付通知开始 post = '. VarDumper::export(Yii::$app->request->post()), __METHOD__);

        $yeepay = new \yeepayMPay(Yii::$app->params['yeepay_config']['merchantAccount'],
            Yii::$app->params['yeepay_config']['merchantPublicKey'],
            Yii::$app->params['yeepay_config']['merchantPrivateKey'],
            Yii::$app->params['yeepay_config']['yeepayPublicKey']);
        try {
            if (empty(Yii::$app->request->post('data')) || empty(Yii::$app->request->post('encryptkey')))
            {
                $msg = '参数不正确';
                Yii::error($msg, __METHOD__);
                return $msg;
            }

            $data = Yii::$app->request->post('data');
            $encryptkey = Yii::$app->request->post('encryptkey');
            $return = $yeepay->callback($data, $encryptkey); //解密易宝支付回调结果
            Yii::warning('return = '. VarDumper::export($return), __METHOD__);

            if (empty($return['merchantaccount'])) {
                $msg = '缺少商户ID';
                Yii::error($msg, __METHOD__);
                return $msg;
            }

            if ($return['merchantaccount'] != Yii::$app->params['yeepay_config']['merchantAccount']) {
                $msg = '商户ID不一致 return account = '. $return['merchantaccount'];
                Yii::error($msg, __METHOD__);
                return $msg;
            }

            if (empty($return['orderid'])) {
                $msg = '缺少订单号';
                Yii::error($msg, __METHOD__);
                return $msg;
            }

            $outTradeNo = $return['orderid'];
            Yii::warning('outTradeNo = '. $outTradeNo, __METHOD__);

            $expTradeNo = explode('E', $outTradeNo);
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

            $yeepayInfos = YeePayInfo::find()->joinWith([
                'paylog paylog',
                'orderInfo orderInfo'
            ])->where([
                'out_trade_no' => $outTradeNo,
            ])->all();

            if (empty($yeepayInfos)) {
                Yii::error('找不到订单', __METHOD__);
                return 'fail';
            }

            $paymentMap = PaymentHelper::$paymentMap;
            foreach ($yeepayInfos as $payInfo) {
                $payLog = $payInfo->paylog;
                if ($payLog->is_paid != 0) {
                    continue;
                }
                $payLog->is_paid = 1;

                $payLog->save();

                $orderInfo = $payInfo->orderInfo;
                $orderInfo->order_status = OrderInfo::ORDER_STATUS_CONFIRMED;
                $orderInfo->confirm_time = DateTimeHelper::gmtime();
                $orderInfo->pay_status = OrderInfo::PAY_STATUS_PAYED;
                $orderInfo->pay_time = DateTimeHelper::gmtime();
                $orderInfo->money_paid = $orderInfo->order_amount;
                $orderInfo->order_amount = 0;
                $orderInfo->pay_id = PaymentHelper::PAY_ID_EB_YINLIAN;
                $orderInfo->pay_name = $paymentMap[$orderInfo->pay_id]; //  支付宝
                $orderInfo->note = '易宝通知修改状态';

                if (!$orderInfo->save()) {
                    Yii::warning($orderInfo->order_sn.' 支付状态修改失败'.json_encode($orderInfo), __METHOD__);
                }
                else {
                    Yii::warning($orderInfo->order_sn. ' 支付状态修改成功', __METHOD__);

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
                    if (!$orderGroup->save()) {
                        Yii::error('总单状态保存失败 group_id = '. $orderGroup['group_id']. ', errors = '. VarDumper::dumpAsString($orderGroup->errors), __METHOD__);
                    }
                }

                //  向积分流水表中插入记录——购物赠送积分,积分兑换、团拼不赠送积分
                if (!in_array($orderInfo->extension_code, ['group_buy', 'flash_sale', 'integral_exchange'])) {
                    $integral = floor(($orderInfo->goods_amount - $orderInfo->discount) / 10);
                    if ($integral > 0) {
                        Yii::warning('yeepay create integral $integral='.$integral.', $order_id='.$payLog->order_id, __METHOD__);

                        Integral::createRecord($integral, $orderInfo->user_id, $payLog->order_id, $outTradeNo, 'alipay');
                    }
                }

                if (!empty($orderInfo->mobile)) {
                    $smsInfo = CacheHelper::getShopConfigParams('sms_order_paid_content')['value'];
                    $api = new \ChuanglanSMS('N9058011', '2701dfc4');
                    if ($api->send($orderInfo->mobile, $smsInfo)) {
                        Yii::warning($orderInfo->order_sn.' 支付状态修改 短信发送成功');
                    } else {
                        Yii::warning($orderInfo->order_sn.' 支付状态修改 短信发送失败！！！');
                    }
                }
            }

            //派券
            if (!$orderIsPaidBefore) {
                OrderGroupHelper::sendCouponAfterPaid($outTradeNo);
            } else {
                Yii::warning('之前支付过了，所以这次通知不送券', __METHOD__);
            }

            return 'SUCCESS';

        }catch (\yeepayMPayException $e) {
            return "支付失败！";
        }
    }
}