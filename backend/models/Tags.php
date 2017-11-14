<?php
/**
 * Created by PhpStorm.
 * User: clark
 * Date: 2016/11/10
 * Time: 18:15
 */

namespace backend\models;


class Tags extends \common\models\Tags
{
    /**
     * 获取所有的标签
     * @return array
     */
    public static function getAllTagIds() {
        $allTagIds = [];
        $allTags = self::findAll(['enabled' => 1]);
        foreach ($allTags as $tag) {
            $allTagIds[$tag->id] = $tag->name;
        }
        return $allTagIds;
    }

    /**
     * 获取当前商品的标签
     * @param $tags
     * @return string
     */
    public static function getTagsStr($tags)
    {
        //  商品标签
        $tags_str = '';
        $allTagIds = self::getAllTagIds();
        if (!empty($tags)) {
            foreach ($tags as $tag) {
                //  容错， 团采标默认不显示
                if (!empty($allTagIds[$tag->id])) {
                    if (!$tags_str) {
                        $tags_str .= $allTagIds[$tag->id];
                    } else {
                        $tags_str .= ' | '.$allTagIds[$tag->id];
                    }
                }
            }

        }
        $tags_str = trim($tags_str);
        $tags_str = trim($tags_str, '|');

        return $tags_str;
    }

    /**
     * 获取当前商品的标签
     * @param $tags
     * @return string
     */
    public static function getTagsMap($tags)
    {
        $tagsMap = [];
        $allTagIds = self::getAllTagIds();
        if (!empty($tags)) {
            foreach ($tags as $tag) {
                //  容错， 团采标默认不显示
                if (!empty($allTagIds[$tag->id])) {
                    $tagsMap[$tag->id] = $allTagIds[$tag->id];
                }
            }
        }
        $tagsMap = array_filter($tagsMap);

        return $tagsMap;
    }
}