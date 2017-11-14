<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_goods_collection_item".
 *
 * @property integer $id
 * @property string $coll_id
 * @property string $goods_id
 * @property integer $sort_order
 * @property \common\models\Goods $goods
 */
class GoodsCollectionItem extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_goods_collection_item';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['coll_id', 'goods_id', 'sort_order'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'coll_id' => '专辑',
            'goods_id' => '商品',
            'sort_order' => '排序值',
        ];
    }

    public function getCollection() {
        return $this->hasOne(GoodsCollection::className(), [
            'id' => 'coll_id',
        ]);
    }

    public function getGoods() {
        return $this->hasOne(Goods::className(), [
            'goods_id' => 'goods_id'
        ]);
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        if (empty($this->sort_order)) {
            $this->sort_order = 1000;
        }
        return true;
    }
}
