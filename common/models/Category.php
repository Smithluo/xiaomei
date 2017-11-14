<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_category".
 *
 * @property integer $cat_id
 * @property string $cat_name
 * @property string $keywords
 * @property string $cat_desc
 * @property integer $parent_id
 * @property integer $sort_order
 * @property string $template_file
 * @property integer $show_in_nav
 * @property string $style
 * @property integer $is_show
 * @property integer $grade
 * @property string $filter_attr
 * @property integer $album_id
 * @property string $brand_list
 */
class Category extends \yii\db\ActiveRecord
{
    const IS_SHOW = 1;
    const IS_NOT_SHOW = 0;

    const GOODS_CAT_PARENT_ID = 100;
    public $goodsCount = 0;
    public $goodsCatCount = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cat_name', 'parent_id', 'sort_order', 'show_in_nav', 'is_show'], 'required'],
            [['parent_id', 'sort_order', 'show_in_nav', 'is_show', 'grade', 'album_id'], 'integer'],
            [['style', 'brand_list'], 'default', 'value' => ''],
            [['cat_name'], 'string', 'max' => 90],
            [['keywords', 'cat_desc', 'filter_attr'], 'string', 'max' => 255],
            [['template_file', 'brand_list'], 'string', 'max' => 50],
            [['style'], 'string', 'max' => 150],
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
            'keywords' => '关键字',
            'cat_desc' => '分类描述',
            'parent_id' => '上级分类ID',
            'sort_order' => '排序值（逆序，0～255）',
            'template_file' => '模版文件',
            'show_in_nav' => '是否显示在品牌列表中作为一个纬度筛选',
            'style' => '风格',
            'is_show' => '是否显示',
            'grade' => '价格分级',
            'filter_attr' => 'Filter Attr',
            'album_id' => '相册ID',
            'brand_list' => '热门品牌列表',
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->cat_id == $this->parent_id) {
                Yii::$app->session->setFlash('error', '分类的父分类不能是分类自己！');
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    /**
     * 指定分类ID获取分类名称
     * @param $id
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function getCatName($id)
    {
        return self::find()->select('cat_name')->where([
            'cat_id' => $id
        ])->asArray()->one();
    }

    /**
     * 通过中间表 实现一级分类与品牌的连接
     */
    public function getBrandCat()
    {
        return $this->hasMany(BrandCat::className(),['cat_id' => 'cat_id']);
    }

    public function getBrand()
    {
        return $this->hasMany(Brand::className(),['brand_id' => 'brand_id'])
            ->via('brandCat');
    }

    public function getChildren() {
        return $this->hasMany(Category::className(), ['parent_id' => 'cat_id']);
    }

    public function getParent() {
        return $this->hasOne(Category::className(), [
            'cat_id' => 'parent_id',
        ]);
    }

    public function getTouchCategory() {
        return $this->hasOne(TouchCategory::className(), [
            'cat_id' => 'cat_id',
        ]);
    }

    /**
     * 获取顶级分类的列表
     * @return array
     */
    public static function getTopCatMap()
    {
        $return = [];

        $rs = self::find()
            ->select(['cat_id', 'cat_name'])
            ->where([
                'parent_id' => 299,
                'is_show' => self::IS_SHOW
            ])->asArray()
            ->all();
        if (!empty($rs)) {
            $return = array_column($rs, 'cat_name', 'cat_id');
        }

        return $return;
    }

    /**
     * 生成分类树
     * @param $categories
     * @param $result
     * @param $level
     */
    public static function generateCatTree($categories, &$result, &$level) {
        foreach($categories as $category) {
            $catPre = '';
            for($i = 0; $i < $level; ++$i) {
                $catPre .= '|----';
            }
            $result[$category->cat_id] = $catPre.$category->cat_name;
            if(count($category->children) > 0) {
                ++$level;
                self::generateCatTree($category->children, $result, $level);
                --$level;
            }
        }
    }

    /**
     * 获取某个节点开始的分类树
     * @param int $parentId
     * @return array
     */
    public static function getCategoryTree($parentId = 0)
    {
        $categories = self::find()->with([
                'children',
                'children.children',
                'children.children.children',
                'children.children.children.children',
                'children.children.children.children.children',
            ])->where([self::tableName().'.parent_id' => $parentId])
            ->all();
        $allCategories = [];
        $level = 0;
        Category::generateCatTree($categories, $allCategories, $level);

        return $allCategories;
    }
}
