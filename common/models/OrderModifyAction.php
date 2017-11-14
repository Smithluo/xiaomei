<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_order_modify_action".
 *
 * @property integer $id
 * @property string $action_user
 * @property string $group_id
 * @property string $order_id
 * @property string $user_id
 * @property string $consignee
 * @property string $mobile
 * @property integer $province
 * @property integer $city
 * @property integer $district
 * @property string $address
 * @property string $time
 */
class OrderModifyAction extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_order_modify_action';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['group_id', 'order_id', 'user_id', 'province', 'city', 'district'], 'integer'],
            [['time'], 'safe'],
            [['consignee', 'mobile'], 'string', 'max' => 60],
            [['address', 'action_user'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'action_user' => '操作者',
            'group_id' => '总单',
            'order_id' => '订单',
            'user_id' => '用户',
            'consignee' => '收件人',
            'mobile' => '收件手机号码',
            'province' => '省',
            'city' => '市',
            'district' => '区',
            'address' => '地址',
            'time' => 'Time',
        ];
    }
}
