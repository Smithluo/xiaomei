<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_brand_spec_goods".
 *
 * @property integer $id
 * @property string $spec_goods_cat_id
 * @property string $goods_id
 * @property integer $sort_order
 */
class BrandSpecGoods extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_brand_spec_goods';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['spec_goods_cat_id', 'goods_id', 'sort_order'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'spec_goods_cat_id' => '分类',
            'goods_id' => '商品',
            'sort_order' => '排序值',
        ];
    }

    public function getSpecCat() {
        return $this->hasOne(BrandSpecGoodsCat::className(), [
            'id' => 'spec_goods_cat_id',
        ]);
    }

    public function getGoods() {
        return $this->hasOne(Goods::className(), [
            'goods_id' => 'goods_id',
        ]);
    }
}
