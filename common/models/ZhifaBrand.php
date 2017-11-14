<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_zhifa_brand".
 *
 * @property integer $id
 * @property integer $brand_id
 * @property integer $sort_order
 */
class ZhifaBrand extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_zhifa_brand';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['brand_id', 'sort_order'], 'integer'],
            [['brand_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'brand_id' => '品牌',
            'sort_order' => '排序值',
        ];
    }

    public function getBrand() {
        return $this->hasOne(Brand::className(), [
            'brand_id' => 'brand_id',
        ]);
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        if (empty($this->sort_order)) {
            $this->sort_order = 1000;
        }
        return true;
    }
}
