<?php
/**
 * Created by PhpStorm.
 * User: clark
 * Date: 2016/10/31
 * Time: 15:48
 */

namespace service\models;


class ServicerUserInfo extends \common\models\ServicerUserInfo
{

    public function getServicerUserId($servicerCode)
    {
        $rs = self::find()->leftJoin('users')->where(['servicer_code' => $servicerCode])->one();

        if ($rs) {
            return $rs->users['user_id'];
        }
    }
}