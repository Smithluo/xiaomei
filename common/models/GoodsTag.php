<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_goods_tag".
 *
 * @property string $goods_id
 * @property string $tag_id
 */
class GoodsTag extends \yii\db\ActiveRecord
{
    public static function primaryKey()
    {
        return ['goods_id', 'tag_id'];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_goods_tag';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['goods_id', 'tag_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'goods_id' => '商品id',
            'tag_id' => '标签id',
        ];
    }

    public function getGoods()
    {
        return $this->hasMany(Goods::className(), ['goods_id' => 'goods_id']);
    }

    /**
     * 打标的商品 获取 该商品参与的活动
     * @return \yii\db\ActiveQuery
     */
    public function getEventToGoods()
    {
        return $this->hasMany(EventToGoods::className(), ['goods_id' => 'goods_id']);
    }

    /**
     * 打标的商品 获取 该商品参与的活动
     * @return \yii\db\ActiveQuery
     */
    public function getEvents()
    {
        return $this->hasMany(Event::className(), ['event_id' => 'event_id'])
            ->via('eventToGoods');
    }
}
