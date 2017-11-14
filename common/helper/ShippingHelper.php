<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/17 0017
 * Time: 18:25
 */

namespace common\helper;


use common\models\Shipping;

class ShippingHelper
{
    static public function queryShippingInfo($shippingSn, $shippingCode = 'auto') {
        $host = "http://jisukdcx.market.alicloudapi.com";
        $path = "/express/query";
        $method = "GET";
        $appcode = "ffe4e57bf8cd43c79fa4c90c202b6247";
        $headers = [];
        array_push($headers, "Authorization:APPCODE " . $appcode);
        $querys = "number=$shippingSn&type=$shippingCode";
        $url = $host . $path . "?" . $querys;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        if (1 == strpos("$".$host, "https://"))
        {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        $result = curl_exec($curl);

        curl_close($curl);
        return json_decode($result, true);
    }

    static public function queryShippingInfoByInvoiceNo($invoiceNo) {
        $invoiceNoArray = explode(':', str_replace('：', ':', trim($invoiceNo)));
        if (count($invoiceNoArray) >= 2) {
            $shippingName = $invoiceNoArray[0];
            $shippingInvoiceNo = $invoiceNoArray[1];
            $expressCompany = 'auto';
            if (strstr($shippingName, '申通')) {
                $expressCompany = 'sto';
            } elseif (strstr($shippingName, '圆通')) {
                $expressCompany = 'yto';
            } elseif (strstr($shippingName, '德邦')) {
                $expressCompany = 'deppon';
            } elseif (strstr($shippingName, '天地华宇')) {
                $expressCompany = 'hoau';
            }

            return self::queryShippingInfo($shippingInvoiceNo, $expressCompany);
        }
        return false;
    }


    /**
     * 获取 配送方式的 [shipping_id => shipping_name] 映射
     * @return array
     */
    public static function getShippingIdNameMap()
    {
        $return = [];
        $rs = Shipping::find()->select(['shipping_id', 'shipping_name'])->asArray()->all();

        if (!empty($rs) && is_array($rs)) {
            $return = array_column($rs, 'shipping_name', 'shipping_id');
        }

        return $return;
    }

}