<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_index_paihang_goods".
 *
 * @property integer $id
 * @property string $title
 * @property string $description
 * @property string $floor_id
 * @property string $goods_id
 * @property integer $sort_order
 */
class IndexPaihangGoods extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_index_paihang_goods';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['description'], 'string'],
            [['goods_id', 'sort_order', 'floor_id'], 'integer'],
            [['title'], 'string', 'max' => 60],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '处于前3时的标题',
            'floor_id' => '品类楼层',
            'description' => '处于前3时的描述文本',
            'goods_id' => '商品',
            'sort_order' => '排序值',
        ];
    }

    public function getGoods() {
        return $this->hasOne(Goods::className(), [
            'goods_id' => 'goods_id',
        ]);
    }

    public function getFloor() {
        return $this->hasOne(IndexPaihangFloor::className(), [
            'id' => 'floor_id',
        ]);
    }
}
