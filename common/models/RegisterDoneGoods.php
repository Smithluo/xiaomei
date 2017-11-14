<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_register_done_goods".
 *
 * @property integer $id
 * @property string $goods_id
 * @property integer $is_show
 * @property integer $sort_order
 */
class RegisterDoneGoods extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_register_done_goods';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['goods_id', 'is_show', 'sort_order'], 'integer'],
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
            'is_show' => '是否显示',
            'sort_order' => '排序值',
        ];
    }

    public function getGoods()
    {
        return $this->hasOne(Goods::className(), [
            'goods_id' => 'goods_id'
        ]);
    }
}
