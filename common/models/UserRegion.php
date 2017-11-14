<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_user_region".
 *
 * @property integer $user_id
 * @property integer $region_id
 */
class UserRegion extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_user_region';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'region_id'], 'required'],
            [['user_id', 'region_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'region_id' => 'Region ID',
        ];
    }

    /**
     * 关联区域对应的联系人
     * @return \yii\db\ActiveQuery
     */
    public function getUsers(){
        return $this->hasOne(Users::className(), ['user_id' => 'user_id']);
    }
}
