<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_moq".
 *
 * @property integer $id
 * @property string $goods_id
 * @property string $moq
 * @property integer $user_rank
 */
class Moq extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_moq';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['goods_id', 'moq', 'user_rank'], 'integer'],
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
            'moq' => '商品起订数量',
            'user_rank' => '用户等级',
        ];
    }
}
