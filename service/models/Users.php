<?php
/**
 * Created by PhpStorm.
 * User: clark
 * Date: 2016/10/29
 * Time: 12:07
 */

namespace service\models;

use \Yii;

class Users extends \common\models\Users
{

    public $total_amount;
    public $total_discount;

    /**
     * 验证 被操作用户是否属于当前服务商
     *
     * 优先判断地址是否属于服务商，如果没地址，则判断是否绑定了服务商ID
     *
     */
    public function checkIsValid($userId)
    {
        if (is_numeric($userId) && $userId > 0) {
            //  过滤用户没有绑定指定渠道
            $servicer = Users::find()->select('province')
                ->where([
                    'user_id' => Yii::$app->user->identity['user_id']
                ])->one();

            $user = Users::find()->joinWith(['servicerUser su'])
                ->where([
                    'user_id' => $userId,
                ])->one();
        } else {
            return false;
        }
    }

}