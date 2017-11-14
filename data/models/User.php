<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/5 0005
 * Time: 17:04
 */
namespace data\models;

use yii\db\ActiveRecord;

class User extends ActiveRecord
{
    public static function tableName()
    {
        return 'o_users';
    }
}