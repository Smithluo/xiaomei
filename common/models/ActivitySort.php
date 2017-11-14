<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_activity_sort".
 *
 * @property integer $id
 * @property string $type
 * @property string $alias
 * @property string $link
 * @property integer $sort_order
 * @property integer $is_show
 * @property integer $show_limit
 */
class ActivitySort extends \yii\db\ActiveRecord
{
    const IS_SHOW_NO = 0;
    const IS_SHOW_YES = 1;

    public static $isShowMap = [
        self::IS_SHOW_NO => '不显示',
        self::IS_SHOW_YES => '显示'
    ];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_activity_sort';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'alias', 'link', 'is_show', 'show_limit'], 'required'],
            [['sort_order', 'show_limit', 'is_show'], 'integer'],
            [['type'], 'string', 'max' => 20],
            [['alias'], 'string', 'max' => 10],
            [['link'], 'string', 'max' => 255],
            [['sort_order', 'show_limit'], 'integer', 'max' => 65535],
            [['type'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => '活动类型',
            'alias' => '活动类型名称',
            'link' => '链接页面',
            'sort_order' => '排序值',
            'is_show' => '是否显示',
            'show_limit' => '首页显示数量上限',
        ];
    }
}
