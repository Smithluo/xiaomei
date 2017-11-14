<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/25 0025
 * Time: 9:26
 */

namespace api\modules\v2\models;

class Users extends \common\models\Users
{

    /**
     * 获取用户等级
     * @return \yii\db\ActiveQuery
     */
    public function getUserRank() {
        return $this->hasOne(UserRank::className(), ['rank_id' => 'user_rank']);
    }

    /**
     * 获取服务商
     * @return \yii\db\ActiveQuery
     */
    public function getServicerUser() {
        return $this->hasOne(Users::className(), ['user_id' => 'servicer_user_id']);
    }
}