<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_goods_supply_info".
 *
 * @property integer $goods_id
 * @property string $supply_price
 */
class GoodsSupplyInfo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_goods_supply_info';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['goods_id'], 'required'],
            [['goods_id'], 'integer'],
            [['supply_price'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'goods_id' => '商品ID',
            'supply_price' => '采购价',
        ];
    }
}
