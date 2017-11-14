<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_article_cat".
 *
 * @property integer $cat_id
 * @property string $cat_name
 * @property integer $cat_type
 * @property string $keywords
 * @property string $cat_desc
 * @property integer $sort_order
 * @property integer $show_in_nav
 * @property integer $parent_id
 */
class ArticleCat extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_article_cat';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cat_type', 'sort_order', 'show_in_nav', 'parent_id'], 'integer'],
            [['cat_name', 'keywords', 'cat_desc'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'cat_id' => '文章分类ID',
            'cat_name' => '文章分类名称',
            'cat_type' => '文章分类类型',
            'keywords' => '关键词',
            'cat_desc' => '描述',
            'sort_order' => '排序值（逆序，0～255）',
            'show_in_nav' => '是否在帮助中心显示',
            'parent_id' => '父分类',
        ];
    }

    /**
     * @inheritdoc
     * @return ArticleCatQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ArticleCatQuery(get_called_class());
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
            if ($this->show_in_nav == null) {
                $this->show_in_nav = 0;
            }
            return true;
        } else {
            return false;
        }
    }

    public function getParent() {
        return $this->hasOne(ArticleCat::className(), ['cat_id' => 'parent_id']);
    }

    public function getChildren() {
        return $this->hasMany(ArticleCat::className(), ['parent_id' => 'cat_id']);
    }

    public function getArticleList() {
        return $this->hasMany(Article::className(), [
            'cat_id' => 'cat_id',
        ]);
    }

    public static function getCatMap() {
        static $data = [];
        if (empty($data)) {
            $catList = ArticleCat::find()->asArray()->all();
            $catList = array_column($catList, 'cat_name', 'cat_id');
            $data = $catList;
        }
        return $data;
    }
}
