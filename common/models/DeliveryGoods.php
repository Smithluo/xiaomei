<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_delivery_goods".
 *
 * @property string $rec_id
 * @property string $delivery_id
 * @property string $goods_id
 * @property string $product_id
 * @property string $product_sn
 * @property string $goods_name
 * @property string $brand_name
 * @property string $goods_sn
 * @property integer $is_real
 * @property string $extension_code
 * @property string $parent_id
 * @property integer $send_number
 * @property string $goods_attr
 * @property string $goods_price
 * @property string $order_goods_rec_id
 */
class DeliveryGoods extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_delivery_goods';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['delivery_id', 'goods_id', 'product_id', 'is_real', 'parent_id', 'send_number', 'order_goods_rec_id'], 'integer'],
            [['goods_attr'], 'string'],
            [['product_sn', 'brand_name', 'goods_sn'], 'string', 'max' => 60],
            [['goods_name'], 'string', 'max' => 120],
            [['extension_code'], 'string', 'max' => 30],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'rec_id' => 'Rec ID',
            'delivery_id' => 'Delivery ID',
            'goods_id' => 'Goods ID',
            'product_id' => 'Product ID',
            'product_sn' => 'Product Sn',
            'goods_name' => 'Goods Name',
            'brand_name' => 'Brand Name',
            'goods_sn' => 'Goods Sn',
            'is_real' => 'Is Real',
            'extension_code' => 'Extension Code',
            'parent_id' => 'Parent ID',
            'send_number' => 'Send Number',
            'goods_attr' => 'Goods Attr',
            'order_goods_rec_id' => '对应的OrderGoods的id',
        ];
    }

    public function getGoods() {
        return $this->hasOne(Goods::className(), ['goods_id' => 'goods_id']);
    }

    /**
     * 获取订单商品
     * @return \yii\db\ActiveQuery
     */
    public function getOrderGoods() {
        return $this->hasOne(OrderGoods::className(), ['rec_id' => 'order_goods_rec_id']);
    }
}
