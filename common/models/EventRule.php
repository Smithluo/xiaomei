<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_event_rule".
 *
 * @property integer $rule_id
 * @property string $rule_name
 * @property integer $match_type
 * @property integer $match_value
 * @property integer $match_effect
 * @property integer $gift_id
 * @property integer $gift_num
 * @property string $gift_show_peice
 * @property string $gift_need_pay
 * @property integer $updated_at
 * @property integer $event_id
 */
class EventRule extends \yii\db\ActiveRecord
{
    const MATCH_TYPE_GOODS_NUM      = 1;
    const MATCH_TYPE_GOODS_AMOUNT   = 2;

    const MATCH_EFFECT_ONE = 1;
    const MATCH_EFFECT_ALL = 2;

    public static $match_type_map = [
        self::MATCH_TYPE_GOODS_NUM      => '商品件数',
//        self::MATCH_TYPE_GOODS_AMOUNT   => '商品总价',
    ];

    public static $match_effect_map = [
        self::MATCH_EFFECT_ONE => '参与活动的单品',
//        self::MATCH_EFFECT_ALL => '参与活动的所有商品',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_event_rule';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['rule_name', 'match_value', 'gift_id', 'gift_num', 'gift_show_peice', 'updated_at', 'event_id'], 'required'],
            [['match_type', 'match_value', 'match_effect', 'gift_id', 'gift_num', 'updated_at', 'event_id'], 'integer'],
            [['gift_show_peice', 'gift_need_pay'], 'number'],
            [['rule_name'], 'string', 'max' => 80],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'rule_id' => '活动策略ID',
            'rule_name' => '策略名称',
            'match_type' => '满足类型',
            'match_value' => '满足数量',
            'match_effect' => '生效范围',
            'gift_id' => '赠品ID',
            'gift_num' => '赠送数量',
            'gift_show_peice' => '赠品单价',
            'gift_need_pay' => '需要支付',
            'updated_at' => '创建时间',
            'event_id' => '活动ID',
        ];
    }

    /**
     * 通过满赠规则获取赠品信息
     * @return \yii\db\ActiveQuery
     */
    public function getGift()
    {
        return $this->hasOne(Goods::className(), ['goods_id' => 'gift_id']);
    }

    /**
     * 获取活动规则对应的活动
     * @return \yii\db\ActiveQuery
     */
    public function getEvent()
    {
        return $this->hasOne(Event::className(), ['event_id' => 'event_id']);
    }
}
