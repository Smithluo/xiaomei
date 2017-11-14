<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_paid_coupon".
 *
 * @property integer $id
 * @property string $amount
 * @property string $event_id
 * @property string $rule_id
 */
class PaidCoupon extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_paid_coupon';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['event_id', 'rule_id'], 'integer'],
            [['amount'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'amount' => '送券需要满足的金额',
            'event_id' => '送券的活动',
            'rule_id' => '送券的活动规则',
        ];
    }

    public function getEvent() {
        return $this->hasOne(Event::className(), [
            'event_id' => 'event_id',
        ]);
    }

    public function getRule() {
        return $this->hasOne(FullCutRule::className(), [
            'rule_id' => 'rule_id',
        ]);
    }
}
