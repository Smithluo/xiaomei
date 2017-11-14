<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_bank_info".
 *
 * @property integer $id
 * @property string $user_name
 * @property string $id_card_no
 * @property string $bank_name
 * @property string $bank_card_no
 * @property string $bank_address
 */
class BankInfo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_bank_info';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_name', 'id_card_no', 'bank_name', 'bank_card_no'], 'required'],
            [['user_name'], 'string', 'max' => 100],
            [['id_card_no'], 'string', 'max' => 20],
            [['bank_card_no'], 'string', 'max' => 20],
            [['bank_name', 'bank_card_no'], 'string', 'max' => 512],
            [['bank_address'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_name' => '开户姓名',
            'id_card_no' => '身份证号',
            'bank_name' => '银行名称',
            'bank_card_no' => '银行卡号',
            'bank_address' => '支行名称',
        ];
    }

    /**
     * @inheritdoc
     * @return BankInfoQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new BankInfoQuery(get_called_class());
    }
}
