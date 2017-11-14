<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/3 0003
 * Time: 9:21
 */

namespace backend\models;


class OrderGroup extends \common\models\OrderGroup
{

    public function getUsers()
    {
        return $this->hasOne(Users::className(), [
            'user_id' => 'user_id',
        ]);
    }

}