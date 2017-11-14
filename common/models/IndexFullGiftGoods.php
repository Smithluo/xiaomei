<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_index_full_gift_goods".
 *
 * @property integer $id
 * @property string $title
 * @property string $sub_title
 * @property string $goods_id
 * @property integer $sort_order
 */
class IndexFullGiftGoods extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_index_full_gift_goods';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['goods_id', 'sort_order'], 'required'],
            [['goods_id', 'sort_order'], 'integer'],
            [['title'], 'string', 'max' => 20],
            [['sub_title'], 'string', 'max' => 28],
            ['sort_order', 'integer', 'max' => 65535],
            ['sort_order', 'default', 'value' => 30000],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'goods_id' => '商品ID',
            'sort_order' => '排序值',
            'title' => 'PC站显示的标题',
            'sub_title' => 'PC站显示的副标题',
        ];
    }

    public function getGoods() {
        return $this->hasOne(Goods::className(), [
            'goods_id' => 'goods_id',
        ]);
    }
}
