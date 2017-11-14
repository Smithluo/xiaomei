<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_index_keywords_group".
 *
 * @property integer $id
 * @property string $title
 * @property integer $cat_id
 * @property string $scene
 * @property integer $sort_order
 * @property integer $is_show
 */
class IndexKeywordsGroup extends \yii\db\ActiveRecord
{
    const SCENE_INDEX = 'index';
    const SCENE_ZHIFA = 'zhifa';

    static $sceneMap = [
        self::SCENE_INDEX => '首页',
        self::SCENE_ZHIFA => '直发',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_index_keywords_group';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sort_order', 'is_show', 'cat_id'], 'integer'],
            [['title'], 'string', 'max' => 60],
            [['scene'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '标题',
            'cat_id' => '分类',
            'scene' => '场景',
            'sort_order' => '排序值',
            'is_show' => '是否显示',
        ];
    }

    public function getKeywordsList() {
        return $this->hasMany(IndexKeywords::className(), [
            'group_id' => 'id',
        ]);
    }

    public function getCategory() {
        return $this->hasOne(Category::className(), [
            'cat_id' => 'cat_id',
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
