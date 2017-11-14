<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_goods_cat".
 *
 * @property string $goods_id
 * @property integer $cat_id
 */
class GoodsCat extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_goods_cat';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['goods_id', 'cat_id'], 'required'],
            [['goods_id', 'cat_id'], 'integer'],
            ['cat_id', 'default', 'value' => 0]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'goods_id' => '商品ID',
            'cat_id' => '扩展分类ID',
        ];
    }
}
