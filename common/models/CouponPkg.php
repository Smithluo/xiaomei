<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_coupon_pkg".
 *
 * @property string $event_id
 * @property integer $enable
 */
class CouponPkg extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_coupon_pkg';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['event_id'], 'required'],
            [['event_id', 'enable'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'event_id' => '活动ID',
            'enable' => '是否可以领取',
        ];
    }

    public function getEvent() {
        return $this->hasOne(Event::className(), [
            'event_id' => 'event_id',
        ]);
    }
}
