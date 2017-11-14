<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_payment".
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
class Payment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_payment';
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
            'pay_id' => 'Pay ID',
            'pay_code' => 'Pay Code',
            'pay_name' => 'Pay Name',
            'pay_fee' => 'Pay Fee',
            'pay_desc' => 'Pay Desc',
            'pay_order' => 'Pay Order',
            'pay_config' => 'Pay Config',
            'enabled' => 'Enabled',
            'is_cod' => 'Is Cod',
            'is_online' => 'Is Online',
        ];
    }
}
