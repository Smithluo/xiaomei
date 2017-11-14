<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_goods_attr".
 *
 * @property string $goods_attr_id
 * @property string $goods_id
 * @property integer $attr_id
 * @property string $attr_value
 * @property string $attr_price
 */
class GoodsAttr extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_goods_attr';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['goods_id', 'attr_id'], 'integer'],
            [['attr_value'], 'required'],
            [['attr_value'], 'string'],
            [['attr_price'], 'string', 'max' => 255],
            ['attr_value', 'filter', 'filter' => 'trim'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'goods_attr_id' => 'Goods Attr ID',
            'goods_id' => 'Goods ID',
            'attr_id' => 'Attr ID',
            'attr_value' => 'Attr Value',
            'attr_price' => 'Attr Price',
        ];
    }

    /**
     * 商品属性对应商品 1:n
     */
    public function getGoods()
    {
        return $this->hasOne(Goods::className(), ['goods_id' => 'goods_id']);
    }

    /**
     * 商品属性 关联 属性表  getAttribute 已被 BaseActiveRecord 使用
     * @return \yii\db\ActiveQuery
     */
    public function getOattribute()
    {
        return $this->hasOne(Attribute::className(), ['attr_id' => 'attr_id']);
    }

    /**
     * 获取产地以及对应有效商品数
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getEffectiveAttr()
    {
        return GoodsAttr::find()->alias('ga')->select([
            'attr_value',
            'count(ga.goods_id) as goods_count'
        ])->joinWith(['goods g'])->where([
            'g.is_on_sale' => Goods::IS_ON_SALE,
            'attr_id' => 165,
        ])->andWhere([
            'not', ['attr_value' => '']
        ])->groupBy('attr_value')
            ->orderBy('attr_value')
            ->asArray()
            ->all();
    }
}
