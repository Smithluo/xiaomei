<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_brand_policy".
 *
 * @property integer $id
 * @property integer $brand_id
 * @property string $policy_content
 * @property string $policy_link
 * @property integer $sort_order
 * @property integer $status
 */
class BrandPolicy extends \yii\db\ActiveRecord
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
        return 'o_brand_policy';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['brand_id', 'sort_order', 'status'], 'integer'],
            [['policy_content', 'policy_link'], 'string', 'max' => 255],
            [['brand_id', 'status', 'policy_content', 'policy_link'], 'required'],
            ['sort_order', 'default', 'value' => 1000],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'brand_id' => '品牌',
            'policy_content' => '内容',
            'policy_link' => '链接',
            'sort_order' => '排序值',
            'status' => '状态',
        ];
    }

    public function getBrand() {
        return $this->hasOne(Brand::className(), ['brand_id' => 'brand_id']);
    }
}
