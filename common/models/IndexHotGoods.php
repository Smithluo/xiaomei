<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_index_hot_goods".
 *
 * @property integer $id
 * @property string $goods_id
 * @property integer $sort_order
 */
class IndexHotGoods extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_index_hot_goods';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['goods_id'], 'required'],
            [['goods_id', 'sort_order'], 'integer'],
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
            'goods_id' => '商品',
            'sort_order' => '排序值',
        ];
    }

    public function getGoods() {
        return $this->hasOne(Goods::className(), ['goods_id' => 'goods_id']);
    }
}
