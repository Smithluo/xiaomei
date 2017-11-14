<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_brand_divide_record".
 *
 * @property integer $id
 * @property string $order_id
 * @property integer $brand_id
 * @property string $goods_amount
 * @property string $shipping_fee
 * @property string $user_id
 * @property string $divide_amount
 * @property string $cash_record_id
 * @property string $created_at
 * @property integer $status
 */
class BrandDivideRecord extends \yii\db\ActiveRecord
{
    //  0 未提取 1 已提取
    const BRAND_DIVIDE_RECORD_STATUS_UNTRACTED = 0;
    const BRAND_DIVIDE_RECORD_STATUS_TRACTED = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_brand_divide_record';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'brand_id'], 'required'],
            [['order_id', 'brand_id', 'user_id', 'cash_record_id', 'status'], 'integer'],
            [['goods_amount', 'shipping_fee', 'divide_amount'], 'number'],
            [['created_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '分成流水ID',
            'order_id' => '订单ID',
            'brand_id' => '品牌ID',
            'goods_amount' => '商品总价',
            'shipping_fee' => '运费价格',
            'user_id' => '零售店ID',
            'divide_amount' => '结算金额',
            'cash_record_id' => '入账记录ID',
            'created_at' => '记录产生时间',
            'status' => '提取状态',
        ];
    }

    public function getOrderInfo()
    {
        return $this->hasOne(OrderInfo::className(), ['order_id' => 'order_id']);
    }
}
