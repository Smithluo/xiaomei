<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/28
 * Time: 11:26
 */

namespace backend\models;

class Category extends \common\models\Category
{

    /**
     * 获取商品分类
     * @param int $parent_id    根ID
     * @return array
     */
    public static function getGoodsCatIdMap($parent_id = 299)
    {
        if (!$parent_id) {
            $parent_id = self::GOODS_CAT_PARENT_ID;
        }
        //  获取一级分类
        $parent_cat = self::find()
            ->select('cat_id, cat_name')
            ->where([
                'is_show' => self::IS_SHOW,
                'parent_id' => $parent_id,
            ])->orderBy([
                'sort_order' => SORT_ASC
            ])->asArray()
            ->all();

        $parent_cat_map = array_column($parent_cat, 'cat_name', 'cat_id');
        $cat_map = [];

        //  获取二级分类
        $cat_list = self::find()
            ->select('cat_id, cat_name, parent_id')
            ->where([
                'is_show' => self::IS_SHOW,
                'parent_id' => array_keys($parent_cat_map),
            ])->orderBy([
                'sort_order' => SORT_ASC
            ])->all();

        foreach ($parent_cat as $item) {
            $cat_map[$item['cat_id']] = $item['cat_name'];
            foreach ($cat_list as $cat) {
                if ($cat->parent_id == $item['cat_id']) {
                    $cat_map[$cat->cat_id] = $item['cat_name'].' - '.$cat->cat_name;
                }
            }
        }

        return $cat_map;
    }

    /**
     * 获取指定的分类列表的名称
     *
     * @param $catIdList        商品分类
     * @param string $column    指定要获取的字段
     * @param string $key       指定数组的key
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getCatList($catIdList, $column = '', $key = '')
    {
        $rs = self::find()->where(['cat_id' => $catIdList])->asArray()->all();
        if ($column) {
            if ($key) {
                return array_column($rs, $column, $key);
            } else {
                return array_column($rs, $column);
            }
        } else {
            return $rs;
        }
    }

    public function getGoods() {
        return $this->hasMany(Goods::className(), ['cat_id' => 'cat_id']);
    }

    public function getGoodsCat() {
        return $this->hasMany(GoodsCat::className(), ['cat_id' => 'cat_id']);
    }

}