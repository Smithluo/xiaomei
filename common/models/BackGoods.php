<?php

namespace common\models;

use common\models\OrderGoods;
use common\helper\ImageHelper;
use Yii;

/**
 * This is the model class for table "o_back_goods".
 *
 * @property string $rec_id
 * @property string $back_id
 * @property string $goods_id
 * @property string $product_id
 * @property string $product_sn
 * @property string $goods_name
 * @property string $brand_name
 * @property string $goods_sn
 * @property integer $is_real
 * @property integer $send_number
 * @property string $goods_attr
 * @property string $goods_price
 * @property string $order_goods_rec_id
 */
class BackGoods extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_back_goods';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['back_id', 'goods_id', 'product_id', 'is_real', 'send_number', 'order_goods_rec_id'], 'integer'],
            [['goods_attr'], 'string'],
            [['product_sn', 'brand_name', 'goods_sn'], 'string', 'max' => 60],
            [['goods_name'], 'string', 'max' => 120],
            [['goods_price'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'rec_id' => 'Rec ID',
            'back_id' => 'Back ID',
            'goods_id' => 'Goods ID',
            'product_id' => 'Product ID',
            'product_sn' => 'Product Sn',
            'goods_name' => 'Goods Name',
            'brand_name' => 'Brand Name',
            'goods_sn' => 'Goods Sn',
            'is_real' => 'Is Real',
            'send_number' => 'Send Number',
            'goods_attr' => 'Goods Attr',
        ];
    }

    public function getBackOrder() {
        return $this->hasOne(BackOrder::className(), [
            'back_id' => 'back_id',
        ]);
    }

    public function getGoods() {
        return $this->hasOne(Goods::className(), [
            'goods_id' => 'goods_id',
        ]);
    }

    public function getGoodsThumb() {
        if (empty($this->goods)) {
            return '';
        }
        return ImageHelper::get_image_path($this->goods->goods_thumb);
    }

    static public function createFromOrderGoods($orderGoods) {
        $backGoods = new BackGoods();

        $backGoods->goods_id = $orderGoods->goods_id;
        $backGoods->product_id = $orderGoods->product_id;
        $backGoods->product_sn = $orderGoods->product_id;
        $backGoods->goods_name = $orderGoods->goods_name;
        $backGoods->brand_name = $orderGoods->goods->brand->brand_name ?: '';
        $backGoods->goods_sn = $orderGoods->goods_sn;
        $backGoods->is_real = $orderGoods->is_real;
        $backGoods->goods_attr = $orderGoods->goods_attr;
        $backGoods->goods_price = $orderGoods->goods_price;
        $backGoods->order_goods_rec_id = $orderGoods->rec_id;

        return $backGoods;
    }

    public function getOrderGoods() {
        return $this->hasOne(OrderGoods::className(), [
            'rec_id' => 'order_goods_rec_id',
        ]);
    }

    public function getOrderGoodsNumber() {
        if (empty($this->orderGoods)) {
            return 0;
        }
        return $this->orderGoods->goods_number;
    }
}
