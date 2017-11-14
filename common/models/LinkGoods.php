<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_link_goods".
 *
 * @property string $goods_id
 * @property string $link_goods_id
 * @property integer $is_double
 * @property integer $admin_id
 */
class LinkGoods extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_link_goods';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['goods_id', 'admin_id'], 'required'], //  'link_goods_id',
            [['goods_id', 'link_goods_id', 'is_double', 'admin_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'goods_id' => '商品ID',
            'link_goods_id' => '关联商品ID',
            'is_double' => '是否双向关联',
            'admin_id' => '创建人',
        ];
    }
}
