<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_user_extension".
 *
 * @property integer $id
 * @property string $user_id
 * @property integer $store_number
 * @property integer $month_sale_count
 * @property integer $imports_per
 * @property integer $duty
 * @property string $qq
 * @property string $birthday
 * @property integer $identify
 */
class UserExtension extends \yii\db\ActiveRecord
{
    /**
     * 职务
     */
    const DUTY_BOSS = 1;
    const DUTY_SERVICER = 2;
    const DUTY_MANAGER = 3;
    const DUTY_OTHER = 4;

    /**
     * 职务对应关系
     */
    public static $duty_map =[
        self::DUTY_BOSS => '老板',
        self::DUTY_MANAGER => '店长',
        self::DUTY_SERVICER => '采购经理',
        self::DUTY_OTHER => '其他',
    ];

    const SALE_BELOW_100000 = 1;
    const SALE_BELOW_500000 = 2;
    const SALE_BELOW_1000000 = 3;
    const SALE_BELOW_MORE = 4;

    public static $sale_count_map =[
        self::SALE_BELOW_100000 => '10万以下',
        self::SALE_BELOW_500000 => '10万~50万',
        self::SALE_BELOW_1000000 => '50万~100万',
        self::SALE_BELOW_MORE => '100万以上',
    ];

    const IMPORT_PER_5 = 1 ;
    const IMPORT_PER_30 = 2 ;
    const IMPORT_PER_35 = 3 ;
    const IMPORT_PER_57 = 5 ;
    const IMPORT_PER_MORE = 4 ;

    public static $import_map = [
        self::IMPORT_PER_5 => '5%以下',
        self::IMPORT_PER_30 => '5%~30%',
        self::IMPORT_PER_35 => '30%~50%',
        self::IMPORT_PER_57 => '50%~70%',
        self::IMPORT_PER_MORE => '70%以上',
    ];

    const HAS_IDENTIFY = 1 ;
    const NOT_IDENTIFY = 0 ;
    const REFUSE_IDENTIFY = 2 ;

    public static $identify_map = [
        self::HAS_IDENTIFY => '通过认证',
        self::NOT_IDENTIFY => '待认证',
        self::REFUSE_IDENTIFY => '拒绝认证',
    ];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_user_extension';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'store_number', 'month_sale_count', 'imports_per', 'duty', 'identify'], 'integer'],
            [['birthday'], 'safe'],
            [['qq'], 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'store_number' => 'Store Number',
            'month_sale_count' => 'Month Sale Count',
            'imports_per' => 'Imports Per',
            'duty' => 'Duty',
            'qq' => 'Qq',
            'birthday' => 'Birthday',
            'identify' => 'identify'
        ];
    }

}
