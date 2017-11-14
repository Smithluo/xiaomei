<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_user_rank".
 *
 * @property integer $rank_id
 * @property string $rank_name
 * @property string $min_points
 * @property string $max_points
 * @property integer $discount
 * @property integer $show_price
 * @property integer $special_rank
 * @property integer $hide_price_num
 * @property integer $shipping_fee_level
 */
class UserRank extends \yii\db\ActiveRecord
{
    //  用户等级
    const USER_RANK_REGISTED = 1;
    const USER_RANK_MEMBER = 2;
    const USER_RANK_VIP = 3;

    //  用户等级
    public static $user_rank_map = [
        self::USER_RANK_REGISTED    => '普通会员', //  待审核
        self::USER_RANK_MEMBER      => 'VIP会员',
        self::USER_RANK_VIP         => 'SVIP会员',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_user_rank';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['min_points', 'max_points', 'discount', 'show_price', 'special_rank', 'hide_price_num', 'shipping_fee_level'], 'integer'],
            [['rank_name'], 'string', 'max' => 30],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'rank_id' => 'Rank ID',
            'rank_name' => 'Rank Name',
            'min_points' => 'Min Points',
            'max_points' => 'Max Points',
            'discount' => 'Discount',
            'show_price' => 'Show Price',
            'special_rank' => 'Special Rank',
            'hide_price_num' => '隐藏价格数',
            'shipping_fee_level' => '运费级别',
        ];
    }
    
}
