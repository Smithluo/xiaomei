<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_yee_payinfo".
 *
 * @property integer $id
 * @property string $order_sn
 * @property integer $pay_log_id
 * @property string $out_trade_no
 * @property string $total_fee
 */
class YeePayinfo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_yee_payinfo';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pay_log_id'], 'integer'],
            [['total_fee'], 'number'],
            [['order_sn', 'out_trade_no'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_sn' => '订单编号',
            'pay_log_id' => '支付日志',
            'out_trade_no' => '商户订单号',
            'total_fee' => '支付金额',
        ];
    }
}
