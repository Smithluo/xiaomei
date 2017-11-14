<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_feedback".
 *
 * @property string $msg_id
 * @property string $parent_id
 * @property string $user_id
 * @property string $user_name
 * @property string $user_email
 * @property string $msg_title
 * @property integer $msg_type
 * @property integer $msg_status
 * @property string $msg_content
 * @property string $msg_time
 * @property string $message_img
 * @property string $order_id
 * @property integer $msg_area
 * @property string $user_phone
 */
class Feedback extends \yii\db\ActiveRecord
{
    const MSG_DEFAULT_TYPE = 0;
    const MSG_SUGGEST_TYPE = 1;
    const MSG_BUG_TYPE = 2;
    const MSG_PLAYBACK_SPEED_TYPE = 3;
    const MSG_OTHER_TYPE = 4;

    public static $msg_type_map = [
        self::MSG_DEFAULT_TYPE => '默认（微信端提交的）',
        self::MSG_SUGGEST_TYPE => '意见建议',
        self::MSG_BUG_TYPE => 'BUG反馈',
        self::MSG_PLAYBACK_SPEED_TYPE => '播放速度',
        self::MSG_OTHER_TYPE => '其他问题',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_feedback';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id', 'user_id', 'msg_type', 'msg_status', 'msg_time', 'order_id', 'msg_area'], 'integer'],
            [['msg_content'], 'required'],
            [['msg_content'], 'string'],
            [['user_name', 'message_img'], 'string', 'max' => 255],
            [['user_email'], 'string', 'max' => 60],
            [['msg_title'], 'string', 'max' => 200],
            [['user_phone'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'msg_id' => 'ID',
//            'parent_id' => 'Parent ID',
            'user_id' => '用户',
//            'user_name' => 'User Name',
//            'user_email' => 'User Email',
            'msg_title' => '标题',
            'msg_type' => '反馈类型',
//            'msg_status' => 'Msg Status',
            'msg_content' => '内容',
            'msg_time' => '反馈时间',
//            'message_img' => 'Message Img',
//            'order_id' => 'Order ID',
//            'msg_area' => 'Msg Area',
            'user_phone' => '联系电话',
        ];
    }

    public function getUsers() {
        return $this->hasOne(Users::className(),['user_id' => 'user_id']);
    }

}
