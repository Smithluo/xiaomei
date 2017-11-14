<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_zhifa_goods".
 *
 * @property integer $id
 * @property integer $type
 * @property integer $goods_id
 * @property integer $sort_order
 */
class ZhifaGoods extends \yii\db\ActiveRecord
{
    const TYPE_QINGCANG = 0;
    const TYPE_HOT = 1;
    const TYPE_WULIAO = 2;

    static $typeMap = [
        self::TYPE_QINGCANG => '清仓特卖',
        self::TYPE_HOT => '热批热卖',
        self::TYPE_WULIAO => '有物有料',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_zhifa_goods';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'goods_id', 'sort_order'], 'integer'],
            [['goods_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => '类型',
            'goods_id' => '商品',
            'sort_order' => '排序值',
        ];
    }

    public function getGoods() {
        return $this->hasOne(Goods::className(), [
            'goods_id' => 'goods_id',
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
