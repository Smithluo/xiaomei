<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_event_to_brand".
 *
 * @property integer $id
 * @property integer $event_id
 * @property integer $brand_id
 */
class EventToBrand extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_event_to_brand';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['event_id', 'brand_id'], 'required'],
            [['event_id', 'brand_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'event_id' => '活动ID',
            'brand_id' => '品牌ID',
        ];
    }

    /**
     * 关联 品牌
     * @return \yii\db\ActiveQuery
     */
    public function getBrand()
    {
        return $this->hasOne(Brand::className(), ['brand_id' => 'brand_id']);
    }

    public function getEvent()
    {
        return $this->hasMany(Event::className(), ['event_id' => 'event_id']);
    }
}
