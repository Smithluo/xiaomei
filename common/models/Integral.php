<?php

namespace common\models;

use common\helper\DateTimeHelper;
use Yii;

/**
 * This is the model class for table "o_integral".
 *
 * @property integer $id
 * @property integer $integral
 * @property string $user_id
 * @property string $pay_code
 * @property string $out_trade_no
 * @property string $note
 * @property string $created_at
 * @property string $updated_at
 * @property integer $status
 */
class Integral extends \yii\db\ActiveRecord
{
    const STATUS_FREEZE = 0;
    const STATUS_THAW   = 1;

    const PAY_CODE_BACKEND  = 'backend';
    const PAY_CODE_ALIPAY   = 'alipay';
    const PAY_CODE_YINLIAN  = 'yinlian';
    const PAY_CODE_WXPAY    = 'wxpay';
    const PAY_CODE_INTEGRAL = 'integral';   //  积分支付，减积分

    /**
     * @inheritdoc 表名
     */
    public static function tableName()
    {
        return 'o_integral';
    }

    public static $statusMap = [
        self::STATUS_THAW   => '生效',
        self::STATUS_FREEZE => '冻结',
    ];

    public static $payCodeMap = [
        self::PAY_CODE_BACKEND  => '后台手动',
        self::PAY_CODE_ALIPAY   => '支付宝',
        self::PAY_CODE_YINLIAN  => '银联企业用户支付',
        self::PAY_CODE_WXPAY    => '微信支付',
        self::PAY_CODE_INTEGRAL => '积分兑换',
    ];

    /**
     * @inheritdoc  字段规则
     */
    public function rules()
    {
        return [
            [['integral', 'user_id', 'out_trade_no', 'note', 'created_at', 'updated_at',], 'required'],
            [['integral', 'user_id', 'created_at', 'updated_at', 'status'], 'integer'],
            [['pay_code'], 'string', 'max' => 20],
            [['out_trade_no'], 'string', 'max' => 32],
            [['note'], 'safe'],
            ['updated_at', 'default', 'value' => DateTimeHelper::getFormatGMTTimesTimestamp()]
        ];
    }

    /**
     * @inheritdoc  字段备注
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'integral' => '积分出入账',
            'user_id' => '用户ID',
            'pay_code' => '支付方式',
            'out_trade_no' => '第三方支付流水号',
            'note' => '订单号',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'status' => '积分状态',
        ];
    }

    /**
     * 映射用户信息表
     * @return \yii\db\ActiveQuery
     */
    public function getUserId()
    {
        return $this->hasOne(Users::className(), ['user_id' => 'user_id']);
    }

    /**
     * 获取用户积分流水对应的订单    有手动赠送积分的没有对应订单
     * @return array|\yii\db\ActiveQuery
     */
    public function getOrderInfo()
    {
        return $this->hasOne(OrderInfo::className(), ['order_id' => 'note']);
    }


    /**
     * 计算当前可用余额
     * @param $userId
     * @return int|mixed
     */
    public static function getBalance($userId)
    {
        $rs = self::find()
            ->select('SUM(integral) AS balance')
            ->where([
                'user_id' => $userId,
                'status' => self::STATUS_THAW,
            ])->asArray()
            ->one();

        if ($rs && isset($rs['balance'])) {
            return (int)$rs['balance'];
        } else {
            return 0;
        }
    }
}
