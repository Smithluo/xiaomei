<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_goods_action".
 *
 * @property integer $id
 * @property string $user_name
 * @property integer $goods_id
 * @property string $goods_name
 * @property string $shop_price
 * @property integer $disable_discount
 * @property string $volume_price
 * @property string $time
 * @property integer $goods_number
 */
class GoodsAction extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_goods_action';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['goods_id', 'disable_discount', 'goods_number'], 'integer'],
            [['shop_price'], 'number'],
            [['volume_price'], 'string'],
            [['time'], 'safe'],
            [['user_name'], 'string', 'max' => 20],
            [['goods_name'], 'string', 'max' => 120],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_name' => '操作者用户名',
            'goods_id' => '商品的ID',
            'goods_name' => '商品名称',
            'shop_price' => '修改后的价格',
            'disable_discount' => '是否参与会员折扣',
            'volume_price' => '阶梯价',
            'time' => '操作时间',
            'goods_number' => '库存',
        ];
    }
}
