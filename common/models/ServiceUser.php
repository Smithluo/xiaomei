<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

class ServiceUser extends Users
{
    public $divide_amount;
    public $servicer_code;
    public $role;

    public static function findByServiceCode($service_code)
    {
        return static::find()->rightJoin('o_servicer_user_info', 'o_service_user_info = o_user.servicer_info_id')->where(['o_service_user_info.service_code'=>$service_code]);
    }

    public function validatePassword($password) {
        if($this->servicer_info_id == 0) {
            return false;
        }
        return parent::validatePassword($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [[
            ['role'],
            'safe'
        ]]);
    }
}
