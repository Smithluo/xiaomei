<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_index_keywords".
 *
 * @property integer $id
 * @property string $group_id
 * @property string $title
 * @property integer $sort_order
 * @property integer $is_show
 * @property string $url
 * @property integer $ext
 */
class IndexKeywords extends \yii\db\ActiveRecord
{
    const EXT_DEFAULT = 0;
    const EXT_BRAND = 1;

    public static $extMap = [
        self::EXT_DEFAULT => '普通关键词',
        self::EXT_BRAND => '品牌关键词',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_index_keywords';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['group_id', 'sort_order', 'is_show', 'ext'], 'integer'],
            [['url'], 'string', 'max' => 255],
            [['title'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'group_id' => '关键词组',
            'title' => '标题',
            'sort_order' => '排序值',
            'is_show' => '是否显示',
            'url' => '跳转链接',
            'ext' => '扩展场景',
        ];
    }

    public function getGroup() {
        return $this->hasOne(IndexKeywordsGroup::className(), [
            'id' => 'group_id',
        ]);
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        if (empty($this->sort_order)) {
            $this->sort_order = 1000;
        }
        return true;
    }
}
