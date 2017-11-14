<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_activity_manzeng".
 *
 * @property string $goods_id
 * @property string $sort_order
 * @property integer $is_show
 */
class ActivityManzeng extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_activity_manzeng';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['goods_id'], 'required'],
            [['goods_id', 'sort_order', 'is_show'], 'integer'],
            ['sort_order', 'default', 'value' => 50],
            ['is_show', 'default', 'value' => 1],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'goods_id' => '商品',
            'sort_order' => '排序',
            'is_show' => '是否显示',
        ];
    }

    public function getGoods() {
        return $this->hasOne(Goods::className(), [
            'goods_id' => 'goods_id',
        ]);
    }
}
