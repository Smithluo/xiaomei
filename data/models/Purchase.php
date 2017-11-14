<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/5 0005
 * Time: 10:55
 */
namespace data\models;

use yii\db\ActiveRecord;

class Purchase extends ActiveRecord
{
    public static function tableName()
    {
        return 'o_users';
    }

    public function getUserInfo($phone)
    {
        $user = Purchase::find()->where(['mobile_phone' => $phone])->one();
        return $user;
    }
}