<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_sms_ip".
 *
 * @property string $ip
 * @property string $count
 * @property string $mobile
 */
class SmsIp extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_sms_ip';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ip'], 'required'],
            [['count'], 'integer'],
            [['ip'], 'string', 'max' => 46],
            [['mobile'], 'string', 'max' => 15],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ip' => 'Ip',
            'count' => 'Count',
        ];
    }
}
