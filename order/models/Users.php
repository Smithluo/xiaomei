<?php
/**
 * Created by PhpStorm.
 * User: clark
 * Date: 2016/10/29
 * Time: 12:07
 */

namespace order\models;

use \Yii;

class Users extends \common\models\Users
{

    public $total_amount;
    public $total_discount;

    /**
     * 验证 导入的订单归属于当前用户
     */
    public function checkIsValid($userId)
    {
        if (is_numeric($userId) && $userId > 0) {

        } else {
            return false;
        }
    }

}