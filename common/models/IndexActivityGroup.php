<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_index_activity_group".
 *
 * @property integer $id
 * @property integer $type
 * @property string $title
 * @property string $desc
 * @property integer $sort_order
 * @property integer $is_show
 */
class IndexActivityGroup extends \yii\db\ActiveRecord
{
    const TYPE_FLASH = 0;
    const TYPE_GROUP_BUY = 1;
    const TYPE_FULL_GIFT = 2;
    const TYPE_FULL_CUT = 3;
    const TYPE_PACKAGE = 4;

    public static $typeMap = [
        self::TYPE_FLASH => '秒杀',
        self::TYPE_FULL_CUT => '满减',
        self::TYPE_FULL_GIFT => '满赠',
        self::TYPE_PACKAGE => '礼包',
        self::TYPE_GROUP_BUY => '团采',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_index_activity_group';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'sort_order', 'is_show'], 'integer'],
            [['title'], 'string', 'max' => 30],
            [['desc'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => '类型',
            'title' => '标题',
            'desc' => '描述',
            'sort_order' => '排序值',
            'is_show' => '是否显示',
        ];
    }
}
