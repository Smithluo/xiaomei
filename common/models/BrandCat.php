<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_brand_cat".
 *
 * @property integer $id
 * @property integer $brand_id
 * @property integer $cat_id
 */
class BrandCat extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_brand_cat';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['brand_id', 'cat_id'], 'required'],
            [['brand_id', 'cat_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'brand_id' => '品牌ID',
            'cat_id' => '一级分类ID',
        ];
    }

    /**
     * 获取品牌关系
     * @return \yii\db\ActiveQuery
     */
    public function getBrand()
    {
        return $this->hasOne(Brand::className(),['brand_id' => 'brand_id']);
    }

    /**
     * 获取与一级分类的关系
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasMany(Category::className(),['cat_id' => 'cat_id']);
    }
}
