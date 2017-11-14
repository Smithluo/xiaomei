<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_new_arrived_goods".
 *
 * @property integer $id
 * @property integer $goods_id
 * @property integer $sort_order
 */
class NewArrivedGoods extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_new_arrived_goods';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['goods_id', 'sort_order'], 'integer'],
            [['goods_id'], 'unique'],
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
        return $this->hasOne(Goods::className(), [
            'goods_id' => 'goods_id',
        ]);
    }
}
