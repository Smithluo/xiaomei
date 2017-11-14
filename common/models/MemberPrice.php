<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_member_price".
 *
 * @property string $price_id
 * @property string $goods_id
 * @property integer $user_rank
 * @property string $user_price
 */
class MemberPrice extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_member_price';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['goods_id', 'user_rank'], 'integer'],
            [['user_price'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'price_id' => 'Price ID',
            'goods_id' => 'Goods ID',
            'user_rank' => 'User Rank',
            'user_price' => 'User Price',
        ];
    }
}
