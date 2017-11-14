<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_yinlian_payinfo".
 *
 * @property integer $id
 * @property string $order_sn
 * @property integer $pay_log_id
 * @property string $out_trade_no
 * @property string $total_fee
 */
class YinlianPayinfo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_yinlian_payinfo';
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
            'order_sn' => 'Order Sn',
            'pay_log_id' => 'Pay Log ID',
            'out_trade_no' => 'Out Trade No',
            'total_fee' => 'Total Fee',
        ];
    }
}
