<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_touch_article_cat".
 *
 * @property integer $cat_id
 * @property string $cat_name
 * @property string $keywords
 * @property string $cat_desc
 * @property integer $sort_order
 * @property integer $parent_id
 * @property integer $show_in_news
 */
class TouchArticleCat extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_touch_article_cat';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sort_order', 'parent_id', 'show_in_news'], 'integer'],
            [['sort_order'], 'default', 'value' => 0],
            [['cat_name', 'keywords', 'cat_desc'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'cat_id' => '分类ID',
            'cat_name' => '分类名称',
            'keywords' => '关键词',
            'cat_desc' => '描述',
            'sort_order' => '排序值（逆序，0～255）',
            'parent_id' => '父分类',
            'show_in_news' => '是否在资讯模块展示',
        ];
    }

    /**
     * @inheritdoc
     * @return TouchArticleCatQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TouchArticleCatQuery(get_called_class());
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->sort_order == null) {
                $this->sort_order = 0;
            }
            if ($this->parent_id == null) {
                $this->parent_id = 0;
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * 父分类
     * @return \yii\db\ActiveQuery
     */
    public function getParent() {
        return $this->hasOne(TouchArticleCat::className(), ['cat_id' => 'parent_id']);
    }

    /**
     * 子分类
     * @return \yii\db\ActiveQuery
     */
    public function getChildren() {
        return $this->hasMany(TouchArticleCat::className(), ['parent_id' => 'cat_id']);
    }

    /**
     * 分类下的所有文章
     * @return \yii\db\ActiveQuery
     */
    public function getArticles() {
        return $this->hasMany(TouchArticle::className(), ['cat_id' => 'cat_id']);
    }

    /**
     * 关联PC的文章分类
     * 用于获取 微信文章分类对应的PC文章分类ID
     */
    public function getArticleCat()
    {
        return $this->hasOne(ArticleCat::className(), ['cat_name' => 'cat_name']);
    }

    public static function getTouchCatMap() {
        static $data = [];
        if (empty($data)) {
            $catList = TouchArticleCat::find()->asArray()->all();
            $catList = array_column($catList, 'cat_name', 'cat_id');
            $catList[-1] = '保留';
            $data = $catList;
        }
        return $data;
    }
}
