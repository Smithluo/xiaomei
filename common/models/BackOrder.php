<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_back_order".
 *
 * @property string $back_id
 * @property string $delivery_sn
 * @property string $order_sn
 * @property string $order_id
 * @property string $invoice_no
 * @property string $add_time
 * @property integer $shipping_id
 * @property string $shipping_name
 * @property string $user_id
 * @property string $action_user
 * @property string $consignee
 * @property string $address
 * @property integer $country
 * @property integer $province
 * @property integer $city
 * @property integer $district
 * @property string $sign_building
 * @property string $email
 * @property string $zipcode
 * @property string $tel
 * @property string $mobile
 * @property string $best_time
 * @property string $postscript
 * @property string $how_oos
 * @property string $insure_fee
 * @property string $shipping_fee
 * @property string $update_time
 * @property integer $suppliers_id
 * @property integer $status
 * @property string $return_time
 * @property integer $agency_id
 * @property string $express_id
 * @property string $group_id
 */
class BackOrder extends \yii\db\ActiveRecord
{
    //默认状态，表示未处理过，只允许有一个默认未处理的退款/货单
    const STATUS_DEFAULT = 0;
    //驳回状态，标识此次退款/货被驳回，无效
    const STATUS_REVOKE = 1;
    //成立状态，标识此次退款/货成立
    const STATUS_VALID = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_back_order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['delivery_sn', 'order_sn'], 'required'],
            [['order_id', 'add_time', 'shipping_id', 'user_id', 'country', 'province', 'city', 'district', 'update_time', 'suppliers_id', 'status', 'return_time', 'agency_id', 'express_id'], 'integer'],
            [['insure_fee', 'shipping_fee'], 'number'],
            [['delivery_sn', 'order_sn'], 'string', 'max' => 20],
            [['invoice_no'], 'string', 'max' => 50],
            [['shipping_name', 'sign_building', 'best_time', 'how_oos'], 'string', 'max' => 120],
            [['action_user'], 'string', 'max' => 30],
            [['consignee', 'email', 'zipcode', 'tel', 'mobile'], 'string', 'max' => 60],
            [['address'], 'string', 'max' => 250],
            [['postscript'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'back_id' => 'Back ID',
            'delivery_sn' => '发货单',
            'order_sn' => '订单编号',
            'order_id' => '订单ID',
            'invoice_no' => 'Invoice No',
            'add_time' => 'Add Time',
            'shipping_id' => 'Shipping ID',
            'shipping_name' => 'Shipping Name',
            'user_id' => 'User ID',
            'action_user' => 'Action User',
            'consignee' => 'Consignee',
            'address' => 'Address',
            'country' => 'Country',
            'province' => 'Province',
            'city' => 'City',
            'district' => 'District',
            'sign_building' => 'Sign Building',
            'email' => 'Email',
            'zipcode' => 'Zipcode',
            'tel' => 'Tel',
            'mobile' => 'Mobile',
            'best_time' => 'Best Time',
            'postscript' => 'Postscript',
            'how_oos' => 'How Oos',
            'insure_fee' => 'Insure Fee',
            'shipping_fee' => 'Shipping Fee',
            'update_time' => 'Update Time',
            'suppliers_id' => 'Suppliers ID',
            'status' => 'Status',
            'return_time' => 'Return Time',
            'agency_id' => 'Agency ID',
            'express_id' => 'Express ID',
        ];
    }

    /**
     * @inheritdoc
     * @return BackOrderQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new BackOrderQuery(get_called_class());
    }

    /**
     * 获取退款/退货商品
     * @return \yii\db\ActiveQuery
     */
    public function getBackGoods() {
        return $this->hasMany(BackGoods::className(), [
            'back_id' => 'back_id',
        ]);
    }

    /**
     * 获取订单
     * @return \yii\db\ActiveQuery
     */
    public function getOrderInfo() {
        return $this->hasOne(OrderInfo::className(), [
            'order_id' => 'order_id',
        ]);
    }
}
