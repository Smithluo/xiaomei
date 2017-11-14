<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_brand_spec_cat_map".
 *
 * @property integer $brand_id
 * @property string $spec_goods_cat_id
 */
class BrandSpecCatMap extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_brand_spec_cat_map';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['brand_id', 'spec_goods_cat_id'], 'required'],
            [['brand_id', 'spec_goods_cat_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'brand_id' => '品牌',
            'spec_goods_cat_id' => '分类',
        ];
    }
}
