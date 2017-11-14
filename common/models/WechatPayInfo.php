<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_wechat_pay_info".
 *
 * @property integer $pay_id
 * @property string $openid
 * @property string $order_sn
 * @property integer $pay_log_id
 * @property string $out_trade_no
 * @property string $appid
 * @property string $mch_id
 * @property string $device_info
 * @property string $nonce_str
 * @property string $prepay_id
 * @property string $result_code
 * @property string $return_code
 * @property string $return_msg
 * @property string $sign
 * @property string $trade_type
 * @property string $err_code
 * @property string $err_code_des
 * @property string $code_url
 * @property integer $create_time
 * @property integer $enable
 * @property string $transaction_id
 * @property string $attach
 * @property string $bank_type
 * @property integer $cash_fee
 * @property string $fee_type
 * @property string $is_subscribe
 * @property string $is_refund
 * @property integer $refund_time
 * @property string $pay_success_time
 * @property string $user_id
 */
class WechatPayInfo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_wechat_pay_info';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_sn', 'pay_log_id', 'out_trade_no', 'return_code', 'return_msg', 'enable', 'user_id'], 'required'],
            [['pay_log_id', 'create_time', 'enable', 'cash_fee', 'refund_time', 'user_id'], 'integer'],
            [['openid', 'return_msg', 'err_code_des'], 'string', 'max' => 128],
            [['order_sn', 'out_trade_no', 'appid', 'mch_id', 'device_info', 'nonce_str', 'sign', 'err_code', 'transaction_id'], 'string', 'max' => 32],
            [['prepay_id', 'code_url'], 'string', 'max' => 64],
            [['result_code', 'return_code', 'trade_type', 'attach', 'bank_type', 'pay_success_time'], 'string', 'max' => 16],
            [['fee_type'], 'string', 'max' => 8],
            [['is_subscribe', 'is_refund'], 'string', 'max' => 1],
            [['openid'], 'default', 'value' => ''],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pay_id' => 'Pay ID',
            'openid' => 'Openid',
            'order_sn' => 'Order Sn',
            'pay_log_id' => 'Pay Log ID',
            'out_trade_no' => 'Out Trade No',
            'appid' => 'Appid',
            'mch_id' => 'Mch ID',
            'device_info' => 'Device Info',
            'nonce_str' => 'Nonce Str',
            'prepay_id' => 'Prepay ID',
            'result_code' => 'Result Code',
            'return_code' => 'Return Code',
            'return_msg' => 'Return Msg',
            'sign' => 'Sign',
            'trade_type' => 'Trade Type',
            'err_code' => 'Err Code',
            'err_code_des' => 'Err Code Des',
            'code_url' => 'Code Url',
            'create_time' => 'Create Time',
            'enable' => 'Enable',
            'transaction_id' => 'Transaction ID',
            'attach' => 'Attach',
            'bank_type' => 'Bank Type',
            'cash_fee' => 'Cash Fee',
            'fee_type' => 'Fee Type',
            'is_subscribe' => 'Is Subscribe',
            'is_refund' => 'Is Refund',
            'refund_time' => 'Refund Time',
            'pay_success_time' => 'Pay Success Time',
            'user_id' => 'User ID',
        ];
    }
}
