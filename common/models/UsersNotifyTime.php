<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_users_notify_time".
 *
 * @property string $user_id
 * @property string $notify_time
 */
class UsersNotifyTime extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_users_notify_time';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id'], 'integer'],
            [['notify_time'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => '用户ID',
            'notify_time' => '最后查看公告列表的时间',
        ];
    }
}
