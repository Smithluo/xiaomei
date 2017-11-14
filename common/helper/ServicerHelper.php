<?php
/**
 * Created by PhpStorm.
 * User: clark
 * Date: 2017/1/18
 * Time: 12:06
 */

namespace common\helper;

use common\models\UserRegion;
use common\models\Users;
use \Yii;

class ServicerHelper
{

    /**
     * 获取 用户所在区域 对应的服务商的联系人信息
     * @param $province integer
     * @param $city     integer
     * @param $regTime  integer
     * @return $contact array
     */
    public static function getServicerContact($province, $city, $regTime)
    {
        $accountManager = Yii::$app->params['accountManager'];
        $accountManagerSpecial = Yii::$app->params['accountManagerSpecial'];
        if (!empty($province) && isset($accountManager[$province])) {
            $contact = $accountManager[$province];

            //  河南服务商特殊处理
            if ($province == 1662) {
                $henanBreakPointStamp = Yii::$app->params['henanBreakPointStamp'];
                if ($regTime < $henanBreakPointStamp) {
                    $contact = $accountManagerSpecial[$province];
                }
            }

        } else {
            if (isset($accountManagerSpecial[$province])) {
                //  河北特殊处理   唐山、秦皇岛市、廊坊市 归天津服务商
                $tianjinServeCityList = Yii::$app->params['tianjinServeCityList'];
                if ($province == 39 && in_array($city, $tianjinServeCityList)) {
                    $contact = $accountManagerSpecial[$province];
                }
            }
        }

        //  如果没有销售负责这个区域，则显示陈聪的联系方式
        if (empty($contact)) {
            $saler = UserRegion::find()
                ->joinWith('users')
                ->where(['region_id' => $province])
                ->orWhere(['region_id' => $city])
                ->one();

            if (
                $saler
                && !empty($saler->users->mobile_phone)
                && !empty($accountManagerSpecial[$saler->users->mobile_phone])
            ) {
                $contact = $accountManagerSpecial[$saler->users->mobile_phone];
            } else {
                $contact = $accountManagerSpecial['default'];
            }
        }

        return $contact;
    }

    /**
     * 获取 参与运费预付 的灰度区域  ——当前规则是 有服务商的区域
     *
     * @param $province 用户所属省份
     * @param $city     用户所属城市
     * @return bool     是否参与运费预付
     */
    public static function isFpbsArea($province, $city)
    {
        $accountManager = Yii::$app->params['accountManager'];
        $accountManagerSpecial = Yii::$app->params['accountManagerSpecial'];

        //  整个省份参与运费预付
        if (!empty($province) && isset($accountManager[$province])) {
            return true;
        }

        //  个别城市参与运费预付
        if (!empty($city) && isset($accountManagerSpecial[$province])) {
            $city_has_server = Yii::$app->params['city_has_server'];

            if (in_array($city, $city_has_server)) {
                return true;
            }
        }

        return false;
    }
}