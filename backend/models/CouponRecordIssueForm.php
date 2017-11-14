<?php
/**
 * Created by PhpStorm.
 * User: clark
 * Date: 2017/3/3
 * Time: 17:45
 */

namespace backend\models;

use yii\base\Model;

class CouponRecordIssueForm extends Model
{

    public $event_id;
    public $rule_id;
    public $number;
    public $circulation;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['event_id', 'rule_id'], 'required'],
            [['event_id', 'rule_id', 'number'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'event_id'      => '优惠券活动ID',
            'rule_id'       => '优惠券规则',
            'number'        => '增加发行量',
        ];
    }

    /**
     * 获取优惠券的发行数量
     * @param $ruleId
     * @param $eventId
     * @return \yii\db\ActiveRecord
     */
    public static function getCirculation($eventId, $ruleId)
    {
        $rs = CouponRecord::find()
            ->where([
                'event_id' => $eventId,
                'rule_id' => $ruleId
            ])->count();

        if ($rs) {
            return $rs;
        } else {
            return 0;
        }
    }

    /**
     * 获取已绑定用户的优惠券数量
     * @param $ruleId
     * @param $eventId
     * @return \yii\db\ActiveRecord
     */
    public static function getBindCouponCount($eventId, $ruleId)
    {
        $rs = CouponRecord::find()
            ->where([
                'event_id' => $eventId,
                'rule_id' => $ruleId
            ])->andWhere([
                '>', 'user_id', 0
            ])->count();

        if ($rs) {
            return $rs;
        } else {
            return 0;
        }
    }

    /**
     * 获取已使用的优惠券数量
     * @param $ruleId
     * @param $eventId
     * @return \yii\db\ActiveRecord
     */
    public static function getUsedCouponCount($eventId, $ruleId)
    {
        $rs = CouponRecord::find()
            ->where([
                'event_id' => $eventId,
                'rule_id' => $ruleId
            ])->andWhere([
                '>', 'used_at', 0
            ])->asArray()
            ->count();

        if ($rs) {
            return $rs;
        } else {
            return 0;
        }
    }

    /**
     * 获取不重复的优惠券编号
     *
     * @param int $length
     * @return string
     */
    public static function getNewCouponSn($length = 10)
    {
        $str = '';
        $chars = 'ACDEFGHJKMNPQRTWXY34679';
        $count = strlen($chars);

        for ($i = 0; $i < $length; $i++) {
            $str .= $chars[mt_rand(0, $count - 1)];
        }

        //  检验随机数是否重复
        $rs = CouponRecord::find()
            ->where(['coupon_sn' => $str])
            ->count();

        if ($rs > 0) {
            self::getNewCouponSn($length);
        } else {
            return $str;
        }
    }
}