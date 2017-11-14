<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_tags".
 *
 * @property integer $id
 * @property integer $type
 * @property string $name
 * @property string $desc
 * @property integer $sort
 * @property integer $enabled
 * @property string $code
 * @property string $mCode
 */
class Tags extends \yii\db\ActiveRecord
{
    const TAG_TYPE_GOODS = 0;
    const TAG_TYPE_ACTIVITY = 1;
    const TAG_TYPE_PROPERTY = 2;

    const TAG_ENABLE = 1;
    const TAG_NOT_ENABLE = 0;

    const TAG_TYPE_NAME_GOODS = '商品标';
    const TAG_TYPE_NAME_ACTIVITY = '活动标';
    const TAG_TYPE_NAME_PROPERTY = '属性标';

    public static function tagTypeName($type) {
        switch ($type) {
            case self::TAG_TYPE_GOODS:
                return self::TAG_TYPE_NAME_GOODS;
            case self::TAG_TYPE_ACTIVITY:
                return self::TAG_TYPE_NAME_ACTIVITY;
            case self::TAG_TYPE_PROPERTY:
                return self::TAG_TYPE_NAME_PROPERTY;
        }
    }

    public static $tag_show_limit_map = [
        self::TAG_TYPE_GOODS => 1,
        self::TAG_TYPE_ACTIVITY => 2,
        self::TAG_TYPE_PROPERTY => 1,
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_tags';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'sort', 'enabled'], 'integer'],
            [['code', 'mCode'], 'string'],
            [['name'], 'string', 'max' => 6],
            [['desc'], 'string', 'max' => 255],
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
            'name' => '名称',
            'desc' => '描述',
            'sort' => '排序值',
            'enabled' => '是否显示',
            'code' => '模版代码',
            'mCode' => '微信站模版代码',
        ];
    }
}
