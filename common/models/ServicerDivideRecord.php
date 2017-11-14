<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_servicer_divide_record".
 *
 * @property integer $id
 * @property string $order_id
 * @property string $amount
 * @property string $spec_strategy_id
 * @property string $user_id
 * @property string $servicer_user_id
 * @property string $parent_servicer_user_id
 * @property string $divide_amount
 * @property string $parent_divide_amount
 * @property string $child_record_id
 * @property string $money_in_record_id
 * @property string $servicer_user_name
 * @property integer $delivery_id
 * @property string $group_id
 */
class ServicerDivideRecord extends \yii\db\ActiveRecord
{
    public $divide;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_servicer_divide_record';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'user_id', 'servicer_user_id', 'parent_servicer_user_id', 'child_record_id', 'money_in_record_id'], 'integer'],
            [['spec_strategy_id'], 'string'], //  text
            [['spec_strategy_id'], 'default', 'value' => '无指定策略id'],
            [['servicer_user_name'], 'string', 'max' => 255],
            [['group_id'], 'string', 'max' => 22],
            [['amount', 'divide_amount', 'parent_divide_amount'], 'number'],
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
            'amount' => '商品总价',
            'spec_strategy_id' => '分成策略',
            'user_id' => '零售店',
            'servicer_user_id' => '业务员',
            'parent_servicer_user_id' => '服务商',
            'divide_amount' => '业务员分成金额',
            'parent_divide_amount' => '服务商分成金额',
            'child_record_id' => '业务员分成流水',
            'money_in_record_id' => '用户钱包入账记录id',
            'servicer_user_name' => '业务员名称',
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->spec_strategy_id == null) {
                $this->spec_strategy_id = '无指定策略id';
            }
            return true;
        } else {
            return false;
        }
    }

    public function getOrderInfo() {
        return $this->hasOne(OrderInfo::className(), ['order_id' => 'order_id']);
    }

    public function getUser() {
        return $this->hasOne(Users::className(), ['user_id' => 'user_id']);
    }

    public function getServicer() {
        return $this->hasOne(ServiceUser::className(), ['user_id' => 'servicer_user_id']);
    }

    public function getParentServicer() {
        return $this->hasOne(ServiceUser::className(), ['user_id' => 'parent_servicer_user_id']);
    }

    public function getOrderGoods() {
        return $this->hasMany(OrderGoods::className(), ['order_id'=>'order_id']);
    }

    public function getCashRecord() {
        return $this->hasOne(CashRecord::className(), [
            'id' => 'money_in_record_id',
        ]);
    }
}
