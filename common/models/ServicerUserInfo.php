<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_servicer_user_info".
 *
 * @property integer $id
 * @property string $servicer_code
 */
class ServicerUserInfo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_servicer_user_info';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['servicer_code'], 'required'],
            [['servicer_code'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'servicer_code' => 'Servicer Code',
        ];
    }

    /**
     * @inheritdoc
     * @return ServicerUserInfoQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ServicerUserInfoQuery(get_called_class());
    }

    public function getUsers()
    {
        return $this->hasOne(Users::className(), ['servicer_info_id' => 'id']);
    }
}
