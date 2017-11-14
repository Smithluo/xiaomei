<?php
/**
 * Created by PhpStorm.
 * User: clark
 * Date: 2017/2/11
 * Time: 17:38
 */

namespace common\helper;

/**
 * 支付方式帮助类
 *
 * 修改支付方式名称，去除样式，在支付成功时修正支付方式，存储 以 pay_name 为准
 * 修改o_payment 表的 pay_name 值，修改 o_order_info 的历史订单，修正pay_name 与新的值对应，
 * 历史订单修正支付方式，通过 o_alipay_info、o_wechat_pay_info
 * Class PaymentHelper
 * @package common\helper
 */
class PaymentHelper
{
    const PAY_ID_ALIPAY     = 1;
    const PAY_ID_YINLIAN    = 2;
    const PAY_ID_WXPAY      = 3;
    const PAY_ID_ALIPAY_IOS = 4;
    const PAY_ID_EB_YINLIAN = 5;
    const PAY_ID_BACKEND    = 105;
    const PAY_ID_INTEGRAL   = 106;
    const PAY_ID_WXPAY_IOS   = 107;

    const PAY_CODE_ALIPAY     = 'alipay';   //  支付宝支付
    const PAY_CODE_YINLIAN    = 'yinlian';  //  银联支付
    const PAY_CODE_WXPAY      = 'wxpay';    //  微信支付
    const PAY_CODE_ALIPAY_IOS = 'alpayIos'; //  支付宝IOS支付
    const PAY_CODE_EB_YINLIAN = 'ebYinlian'; //  易宝支付
    const PAY_CODE_BACKEND    = 'backend';  //  后台支付、线下支付
    const PAY_CODE_INTEGRAL   = 'integral'; //  积分支付
    const PAY_CODE_WXPAY_IOS = 'wxpay_ios'; // 微信IOS支付

    public static $paymentMap = [
        self::PAY_ID_ALIPAY       => '支付宝支付',
        self::PAY_ID_YINLIAN      => '银联支付',
        self::PAY_ID_WXPAY        => '微信支付',
        self::PAY_ID_ALIPAY_IOS   => '支付宝IOS支付',
        self::PAY_ID_EB_YINLIAN   => '易宝支付银联',
        self::PAY_ID_BACKEND      => '线下支付',
        self::PAY_ID_INTEGRAL     => '积分支付',
        self::PAY_ID_WXPAY_IOS     => '微信IOS支付',
    ];
}