<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_index_spec_config".
 *
 * @property integer $id
 * @property string $goods_id
 * @property integer $sort_order
 * @property string $tip
 * @property string $title
 * @property string $sub_title
 */
class IndexSpecConfig extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_index_spec_config';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['goods_id', 'sort_order'], 'integer'],
            [['goods_id'], 'required'],
            [['sort_order'], 'default', 'value' => 0],
            [['tip'], 'string', 'max' => 8],
            [['title', 'sub_title'], 'string', 'max' => 20],
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
            'tip' => '顶部tip',
            'title' => '标题',
            'sub_title' => '副标题',
        ];
    }

    /**
     * 商品关系
     * @return \yii\db\ActiveQuery
     */
    public function getGoods()
    {
        return $this->hasOne(Goods::className(), ['goods_id' => 'goods_id']);
    }
}
