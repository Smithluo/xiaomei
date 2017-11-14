<?php

namespace common\models\payment;

use Yii;

/**
 * This is the model class for table "o_touch_payment".
 *
 * @property integer $pay_id
 * @property string $pay_code
 * @property string $pay_name
 * @property string $pay_fee
 * @property string $pay_desc
 * @property integer $pay_order
 * @property string $pay_config
 * @property integer $enabled
 * @property integer $is_cod
 * @property integer $is_online
 */
class TouchPayment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_touch_payment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pay_desc', 'pay_config'], 'required'],
            [['pay_desc', 'pay_config'], 'string'],
            [['pay_order', 'enabled', 'is_cod', 'is_online'], 'integer'],
            [['pay_code'], 'string', 'max' => 20],
            [['pay_name'], 'string', 'max' => 120],
            [['pay_fee'], 'string', 'max' => 10],
            [['pay_code'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pay_id'        => '支付方式ID',
            'pay_code'      => '支付方式代码',
            'pay_name'      => '支付方式名称',
            'pay_fee'       => '支付方式费率',
            'pay_desc'      => '支付方式描述',
            'pay_order'     => '排序值',
            'pay_config'    => '支付方式配置',
            'enabled'       => '是否可用',
            'is_cod'        => '是否货到付款',
            'is_online'     => '是否在线支付',
        ];
    }

    /**
     * @inheritdoc
     * @return TouchPaymentQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TouchPaymentQuery(get_called_class());
    }
}
