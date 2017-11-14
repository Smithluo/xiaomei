<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_fashion_goods".
 *
 * @property integer $id
 * @property string $goods_id
 * @property integer $sort_order
 */
class FashionGoods extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_fashion_goods';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['goods_id', 'sort_order'], 'integer'],
            [['name'], 'string', 'max' => 20],
            [['desc'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'goods_id' => '商品',
            'sort_order' => '排序',
            'name' => '名字',
            'desc' => '描述',
        ];
    }

    public function getGoods()
    {
        return $this->hasOne(Goods::className(), ['goods_id' => 'goods_id']);
    }
}
