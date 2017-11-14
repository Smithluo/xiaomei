<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_index_hot_brands".
 *
 * @property integer $id
 * @property integer $brand_id
 * @property integer $sort_order
 */
class IndexHotBrand extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_index_hot_brands';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['brand_id'], 'required'],
            [['brand_id', 'sort_order'], 'integer'],
            [['sort_order'], 'default', 'value' => 0],
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
        return $this->hasOne(Brand::className(), ['brand_id' => 'brand_id']);
    }
}
