<?php
/**
 * Created by PhpStorm.
 * User: clark
 * Date: 2017/3/20
 * Time: 11:10
 */

namespace common\helper;


use common\models\Category;

class CategoryHelper
{

    /**
     * 递归获取指定节点的所有子节点
     * @param $catMap = [
     *      'catId' => 传入为指定的节点，返回为所有指定节点及其所有子节点
     *      'subCatList' => 第一次传入为空，每次递归存储当前获取到的子节点
     * ]
     * @return mixed
     */
    public static function getCatChildren($catMap)
    {
        if (empty($catMap['subCatList'])) {
            $parentId = $catMap['catId'];
        } else {
            $parentId = $catMap['subCatList'];
        }

        if (!is_array($catMap['catId'])) {
            $catMap['catId'] = [$catMap['catId']];
        }

        $rs = Category::find()
            ->select(['cat_id'])
            ->where(['parent_id' => $parentId])
            ->asArray()
            ->all();

        if (!empty($rs)) {
            $catMap['subCatList'] = array_column($rs, 'cat_id');
            $catMap['catId'] = array_merge($catMap['catId'], $catMap['subCatList']);

            $catMap = self::getCatChildren($catMap);
        } else {
            return $catMap;
        }
    }
}