<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "o_brand_spec_goods_cat".
 *
 * @property integer $id
 * @property integer $brand_id
 * @property string $title
 * @property integer $sort_order
 */
class BrandSpecGoodsCat extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_brand_spec_goods_cat';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sort_order', 'brand_id'], 'integer'],
            [['title'], 'string', 'max' => 56],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '标题',
            'sort_order' => '排序值',
            'brand_id' => '品牌',
        ];
    }

    public function getBrand() {
        return $this->hasOne(Brand::className(), [
            'brand_id' => 'brand_id',
        ]);
    }

    /**
     * 获取分类下的商品
     * @return \yii\db\ActiveQuery
     */
    public function getSpecGoodsList() {
        return $this->hasMany(BrandSpecGoods::className(), [
            'spec_goods_cat_id' => 'id',
        ]);
    }

    public function getSpecGoodsProvider() {
        $specSearchModel = new BrandSpecGoodsSearch();
        $provider = $specSearchModel->search([
            'BrandSpecGoodsSearch' => [
                'spec_goods_cat_id' => $this->id,
            ],
        ]);
        return $provider;
    }

    public static function getAllSpecGoodsCatMap() {
        $all = self::find()->indexBy('id')->all();
        return ArrayHelper::getColumn($all, 'title', true);
    }
}
