<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_knowledge_show_brand".
 *
 * @property integer $id
 * @property integer $brand_id
 * @property integer $sort_order
 */
class KnowledgeShowBrand extends \yii\db\ActiveRecord
{
    const PLATFORM_M        = 'm';
    const PLATFORM_PC       = 'pc';
    const PLATFORM_IOS      = 'ios';
    const PLATFORM_ANDROID  = 'android';

    public static $platformMap = [
        self::PLATFORM_M        => 'M站',
        self::PLATFORM_PC       => 'PC站',
        self::PLATFORM_IOS      => '苹果APP',
        self::PLATFORM_ANDROID  => '安卓APP',
    ];


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_knowledge_show_brand';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['brand_id'], 'required'],
            [['brand_id'], 'integer'],
            [['brand_id'], 'platformUnique', 'params' => 'platform'],
            ['sort_order', 'integer', 'max' => 65535],
            ['sort_order', 'default', 'value' => 30000],
            ['platform', 'string', 'max' => 10 ], //  ['m', 'pc', 'ios', 'android']
        ];
    }

    /**
     * 验证指定平台下的 推荐品牌的唯一性
     * @param $attribute
     * @param $params
     */
    public function platformUnique($attribute, $params)
    {
        $rs = self::find()
            ->where([
                'platform' => $this->platform,
                $attribute => $this->$attribute,
            ])->one();
        if (!empty($rs)) {
            $this->addError($attribute, '同一个平台下的推荐品牌不能重复');
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'brand_id' => '推荐品牌',
            'sort_order' => '排序值',
            'platform' => '平台',
        ];
    }

    /**
     * 关联品牌表
     * @return \yii\db\ActiveQuery
     */
    public function getBrand()
    {
        return $this->hasOne(Brand::className(), ['brand_id' => 'brand_id']);
    }
}
