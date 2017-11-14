<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_index_star_goods_conf".
 *
 * @property integer $id
 * @property integer $goods_id
 * @property integer $tab_id
 * @property integer $sort_order
 */
class IndexStarGoodsConf extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_index_star_goods_conf';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['goods_id', 'tab_id', 'sort_order'], 'integer'],
            [['goods_id', 'tab_id'], 'required'],
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
            'tab_id' => '标签',
            'sort_order' => '排序值',
        ];
    }

    /**
     * 商品
     * @return \yii\db\ActiveQuery
     */
    public function getGoods() {
        return $this->hasOne(Goods::className(), ['goods_id' => 'goods_id']);
    }

    /**
     * 标签
     * @return \yii\db\ActiveQuery
     */
    public function getTab() {
        return $this->hasOne(IndexStarGoodsTabConf::className(), ['id' => 'tab_id']);
    }
}
