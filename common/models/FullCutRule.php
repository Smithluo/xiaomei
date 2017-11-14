<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_full_cut_rule".
 *
 * @property integer $rule_id
 * @property string $rule_name
 * @property integer $event_id
 * @property string $above
 * @property string $cut
 * @property integer $status
 * @property integer $term_of_validity
 */
class FullCutRule extends \yii\db\ActiveRecord
{
    const STATUS_VALID      = 1;    //  有效状态
    const STATUS_INVALID    = 0;    //  无效状态

    public static $statusMap = [
        self::STATUS_VALID      => '有效',
        self::STATUS_INVALID    => '无效',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_full_cut_rule';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['rule_name', 'event_id', 'above', 'cut'], 'required'],
            [['event_id', 'status', 'term_of_validity'], 'integer'],
            [['above', 'cut'], 'number'],
            [['rule_name'], 'string', 'max' => 40],
            [['term_of_validity'], 'default', 'value' => 0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'rule_id' => 'Rule ID',
            'rule_name' => '规则名称',
            'event_id' => '活动ID',
            'above' => '满足金额',
            'cut' => '减免金额',
            'status' => '状态',
            'term_of_validity' => '领券后有效时间(s)',
        ];
    }

    /**
     * 活动规则对应的活动
     * @return \yii\db\ActiveQuery
     */
    public function getEvent()
    {
        return $this->hasOne(Event::className(), ['event_id' => 'event_id']);
    }

    /**
     * 获取已领取的优惠券数量
     * @param $userId
     * @return int|string
     */
    public function getCouponCountTaken($userId) {
        if (empty($userId)) {
            return 0;
        }
        $count = CouponRecord::find()->where([
            'rule_id' => $this->rule_id,
            'user_id' => $userId,
        ])->count();
        return intval($count);
    }

    /**
     * 获取一张未领取的券
     * @return $this
     */
    public function getCouponCanTake() {
        return $this->hasOne(CouponRecord::className(), [
            'rule_id' => 'rule_id',
        ])->onCondition([
            'user_id' => 0,
        ]);
    }

    /**
     * 关联规则下的所有优惠券
     * @return \yii\db\ActiveQuery
     */
    public function getCouponRecord()
    {
        return $this->hasMany(CouponRecord::className(), ['rule_id' => 'rule_id']);
    }

    public function getCouponCount() {
        return CouponRecord::find()->where([
            'rule_id' => $this->rule_id,
        ])->count();
    }

    public function getCouponTotalTaken() {
        return CouponRecord::find()->where([
            'rule_id' => $this->rule_id,
        ])->andWhere([
            '>',
            'user_id',
            0,
        ])->count();
    }

    public function getCouponDataInfo() {
        return [
            'ruleId' => $this->rule_id,
            'ruleName' => $this->rule_name,
            'sub_type' => $this->event['sub_type'],
            'cut' => intval($this->cut),
            'totalCount' => $this->getCouponCount(),
            'takenCount' => $this->getCouponTotalTaken(),
        ];
    }
}
